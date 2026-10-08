<?php
/**
 * API Cancel Booking - Cancella una prenotazione da HotelDruid (Versione Definitiva)
 * Endpoint: POST /api/cancel_booking.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// Sicurezza API Key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato: API Key mancante o non valida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito. Usa POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$codice_prenota = $input['codice_prenotazione'] ?? null;
$id_prenota = $input['id_prenotazione'] ?? null;

if (!$codice_prenota && !$id_prenota) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Devi fornire codice_prenotazione o id_prenotazione.']);
    exit;
}

try {
    $anno_corrente = date('Y');
    $prenotazione_trovata = null;
    $tabella_trovata = null;

    // Cicla sugli anni (anno scorso, quest'anno, anno prossimo)
    for ($anno = $anno_corrente - 1; $anno <= $anno_corrente + 1; $anno++) {
        $table_prenota = "prenota" . $anno;
        
        try {
            // Tenta direttamente la ricerca
            if ($codice_prenota) {
                $stmt = $pdo->prepare("SELECT idprenota, idappartamenti FROM {$table_prenota} WHERE codice = :codice LIMIT 1");
                $stmt->execute([':codice' => $codice_prenota]);
            } else {
                $stmt = $pdo->prepare("SELECT idprenota, idappartamenti FROM {$table_prenota} WHERE idprenota = :id LIMIT 1");
                $stmt->execute([':id' => $id_prenota]);
            }
            
            $prenotazione_trovata = $stmt->fetch();
            if ($prenotazione_trovata) {
                $tabella_trovata = $table_prenota;
                break; // Trovata! Esce dal ciclo.
            }
        } catch (PDOException $e) {
            // Se l'errore è "tabella non esistente" (42S02), la ignoriamo e passiamo all'anno successivo
            if ($e->getCode() === '42S02') {
                continue; 
            }
            // Se è un altro errore SQL, lo blocchiamo e lo segnaliamo
            throw $e;
        }
    }

    if (!$prenotazione_trovata) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Prenotazione non trovata in nessun anno.']);
        exit;
    }

    // Cancella la prenotazione
    $pdo->beginTransaction();
    $stmt_delete = $pdo->prepare("DELETE FROM {$tabella_trovata} WHERE idprenota = :id");
    $stmt_delete->execute([':id' => $prenotazione_trovata['idprenota']]);
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione cancellata con successo.',
        'data' => [
            'id_prenotazione_cancellata' => $prenotazione_trovata['idprenota'],
            'camera' => $prenotazione_trovata['idappartamenti']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore: ' . $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}