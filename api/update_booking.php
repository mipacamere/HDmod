<?php
/**
 * API Update Booking - Modifica una prenotazione in HotelDruid (Versione Definitiva)
 * Endpoint: POST /api/update_booking.php
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

$codice_prenota = $input['codice_prenotazione'] ?? null;
$id_prenota = $input['id_prenotazione'] ?? null;
$new_checkin = $input['checkin'] ?? null;
$new_checkout = $input['checkout'] ?? null;
$new_camera_id = $input['camera_id'] ?? null;

if (!$codice_prenota && !$id_prenota) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fornisci codice_prenotazione o id_prenotazione.']);
    exit;
}

if (!$new_checkin && !$new_checkout && !$new_camera_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nessun campo da aggiornare.']);
    exit;
}

try {
    $anno_corrente = date('Y');
    $prenotazione_trovata = null;
    $tabella_trovata = null;
    $anno_prenotazione = null;

    for ($anno = $anno_corrente - 1; $anno <= $anno_corrente + 1; $anno++) {
        $table_prenota = "prenota" . $anno;
        
        try {
            if ($codice_prenota) {
                $stmt = $pdo->prepare("SELECT * FROM {$table_prenota} WHERE codice = :codice LIMIT 1");
                $stmt->execute([':codice' => $codice_prenota]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM {$table_prenota} WHERE idprenota = :id LIMIT 1");
                $stmt->execute([':id' => $id_prenota]);
            }
            
            $prenotazione_trovata = $stmt->fetch();
            if ($prenotazione_trovata) {
                $tabella_trovata = $table_prenota;
                $anno_prenotazione = $anno;
                break;
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '42S02') continue; // Tabella non esiste, passo all'anno dopo
            throw $e;
        }
    }

    if (!$prenotazione_trovata) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Prenotazione non trovata.']);
        exit;
    }

    $pdo->beginTransaction();
    $update_fields = [];
    $update_params = [':id' => $prenotazione_trovata['idprenota']];

    if ($new_checkin || $new_checkout) {
        $table_periodi = "periodi" . $anno_prenotazione;
        
        if ($new_checkin) {
            $stmt_in = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datainizio = :data");
            $stmt_in->execute([':data' => $new_checkin]);
            $new_id_in = $stmt_in->fetchColumn();
            if (!$new_id_in) throw new Exception("Check-in non trovato nei periodi $anno_prenotazione.");
            $update_fields[] = "iddatainizio = :new_in";
            $update_params[':new_in'] = $new_id_in;
        }

        if ($new_checkout) {
            $stmt_out = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datafine = :data");
            $stmt_out->execute([':data' => $new_checkout]);
            $new_id_out = $stmt_out->fetchColumn();
            if (!$new_id_out) throw new Exception("Check-out non trovato nei periodi $anno_prenotazione.");
            $update_fields[] = "iddatafine = :new_out";
            $update_params[':new_out'] = $new_id_out;
        }
    }

    if ($new_camera_id) {
        $update_fields[] = "idappartamenti = :new_app";
        $update_params[':new_app'] = (string)$new_camera_id;
    }

    if (count($update_fields) > 0) {
        $sql = "UPDATE {$tabella_trovata} SET " . implode(', ', $update_fields) . " WHERE idprenota = :id";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute($update_params);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione aggiornata con successo.',
        'data' => ['id_prenotazione' => $prenotazione_trovata['idprenota']]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore: ' . $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}