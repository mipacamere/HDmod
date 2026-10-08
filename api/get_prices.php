<?php
/**
 * API Get Prices - Legge le tariffe da HotelDruid (Versione Smart Detection)
 * Endpoint: GET /api/get_prices.php?checkin=YYYY-MM-DD&checkout=YYYY-MM-DD&tariffa=1
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

$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;
$id_tariffa = (int)($_GET['tariffa'] ?? 1);

if (!$checkin || !$checkout) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parametri mancanti.']);
    exit;
}

$anno = date('Y', strtotime($checkin));
$table_periodi = "periodi" . $anno;

// 1. RILEVAMENTO SMART: Cerca una tabella che esista E che abbia la colonna 'periodo1'
$possible_tariffe_tables = ["tariffe" . $anno, "ntariffe" . $anno]; // Mettiamo tariffe per primo
$table_tariffe = null;

foreach ($possible_tariffe_tables as $tbl) {
    // Controlla se la tabella esiste
    $stmt_check = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name");
    $stmt_check->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
    
    if ($stmt_check->fetch()) {
        // Controlla se questa tabella ha la colonna 'periodo1' (firma della tabella tariffe base)
        $stmt_col = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = 'periodo1'");
        $stmt_col->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
        
        if ($stmt_col->fetch()) {
            $table_tariffe = $tbl;
            break; // Trovata la tabella corretta!
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

    $prezzi_giornalieri = [];
    $prezzo_totale = 0;

    // 3. Estrai i prezzi
    for ($i = $id_periodo_in; $i < $id_periodo_out; $i++) {
        $col_name = "periodo" . $i;
        
        // Verifica dinamica se la colonna esiste per questo specifico periodo
        $stmt_col_check = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :col_name");
        $stmt_col_check->execute([':db_name' => DB_NAME, ':table_name' => $table_tariffe, ':col_name' => $col_name]);
        
        if ($stmt_col_check->fetch()) {
            $stmt_price = $pdo->prepare("SELECT {$col_name} FROM {$table_tariffe} WHERE idtariffe = :id");
            $stmt_price->execute([':id' => $id_tariffa]);
            $prezzo = (float)$stmt_price->fetchColumn();
            
            $stmt_date = $pdo->prepare("SELECT datainizio FROM {$table_periodi} WHERE idperiodi = :id");
            $stmt_date->execute([':id' => $i]);
            $data = $stmt_date->fetchColumn();
            
            $prezzi_giornalieri[] = ['data' => $data, 'prezzo' => $prezzo];
            $prezzo_totale += $prezzo;
        }
    }

    echo json_encode([
        'success' => true,
        'table_used' => $table_tariffe,
        'data' => [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'tariffa_id' => $id_tariffa,
            'prezzo_totale' => $prezzo_totale,
            'prezzi_giornalieri' => $prezzi_giornalieri,
            'totale_notti' => count($prezzi_giornalieri)
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}