<?php
/**
 * API Set Prices - Aggiorna le tariffe in HotelDruid (Versione Smart Detection)
 * Endpoint: POST /api/set_prices.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Usa POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$checkin = $input['checkin'] ?? null;
$checkout = $input['checkout'] ?? null;
$id_tariffa = (int)($input['tariffa_id'] ?? 1);
$prezzo = $input['prezzo'] ?? null;
$modalita = $input['modalita'] ?? 'fisso';
$prezzi_giornalieri = $input['prezzi_giornalieri'] ?? [];

if (!$checkin || !$checkout || $prezzo === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dati mancanti.']);
    exit;
}

$anno = date('Y', strtotime($checkin));
$table_periodi = "periodi" . $anno;

// 1. RILEVAMENTO SMART (uguale a get_prices.php)
$possible_tariffe_tables = ["tariffe" . $anno, "ntariffe" . $anno];
$table_tariffe = null;

foreach ($possible_tariffe_tables as $tbl) {
    $stmt_check = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name");
    $stmt_check->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
    
    if ($stmt_check->fetch()) {
        $stmt_col = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = 'periodo1'");
        $stmt_col->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
        
        if ($stmt_col->fetch()) {
            $table_tariffe = $tbl;
            break;
        }
    }
}

if (!$table_tariffe) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => "Nessuna tabella tariffe valida trovata per l'anno $anno."]);
    exit;
}

try {
    // 2. Trova gli ID dei periodi
    $stmt_in = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datainizio = :data");
    $stmt_in->execute([':data' => $checkin]);
    $id_periodo_in = (int)$stmt_in->fetchColumn();

    $stmt_out = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datafine = :data");
    $stmt_out->execute([':data' => $checkout]);
    $id_periodo_out = (int)$stmt_out->fetchColumn();

    if (!$id_periodo_in || !$id_periodo_out) {
        throw new Exception("Date non trovate nei periodi dell'anno $anno.");
    }

    $pdo->beginTransaction();
    $periodi_aggiornati = 0;
    
    if ($modalita === 'fisso') {
        for ($i = $id_periodo_in; $i < $id_periodo_out; $i++) {
            $col_name = "periodo" . $i;
            
            // Verifica se la colonna esiste prima di aggiornare
            $stmt_col_check = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :col_name");
            $stmt_col_check->execute([':db_name' => DB_NAME, ':table_name' => $table_tariffe, ':col_name' => $col_name]);
            
            if ($stmt_col_check->fetch()) {
                $stmt_update = $pdo->prepare("UPDATE {$table_tariffe} SET {$col_name} = :prezzo WHERE idtariffe = :id");
                $stmt_update->execute([':prezzo' => (float)$prezzo, ':id' => $id_tariffa]);
                $periodi_aggiornati++;
            }
        }
    } elseif ($modalita === 'giornaliero' && !empty($prezzi_giornalieri)) {
        foreach ($prezzi_giornalieri as $item) {
            $data = $item['data'] ?? null;
            $prezzo_giorno = $item['prezzo'] ?? null;
            
            if ($data && $prezzo_giorno !== null) {
                $stmt_periodo = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datainizio = :data");
                $stmt_periodo->execute([':data' => $data]);
                $id_periodo = (int)$stmt_periodo->fetchColumn();
                
                if ($id_periodo >= $id_periodo_in && $id_periodo < $id_periodo_out) {
                    $col_name = "periodo" . $id_periodo;
                    $stmt_col_check = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :col_name");
                    $stmt_col_check->execute([':db_name' => DB_NAME, ':table_name' => $table_tariffe, ':col_name' => $col_name]);
                    
                    if ($stmt_col_check->fetch()) {
                        $stmt_update = $pdo->prepare("UPDATE {$table_tariffe} SET {$col_name} = :prezzo WHERE idtariffe = :id");
                        $stmt_update->execute([':prezzo' => (float)$prezzo_giorno, ':id' => $id_tariffa]);
                        $periodi_aggiornati++;
                    }
                }
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'table_used' => $table_tariffe,
        'message' => 'Prezzi aggiornati con successo.',
        'data' => [
            'tariffa_id' => $id_tariffa,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'periodi_aggiornati' => $periodi_aggiornati,
            'modalita' => $modalita
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}