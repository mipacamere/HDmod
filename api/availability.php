<?php
/**
 * API Availability - Verifica disponibilità camere (Corretto per architettura HD)
 * Endpoint: GET /api/availability.php?checkin=YYYY-MM-DD&checkout=YYYY-MM-DD
 */

// 1. Sicurezza: Permetti solo l'accesso tramite il nostro config
define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
// --- CONTROLLO SICUREZZA API KEY ---
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;

if ($api_key !== API_SECRET_KEY) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        'success' => false,
        'error' => 'Accesso non autorizzato: API Key mancante o non valida.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
// --- FINE CONTROLLO ---

// 2. Header JSON
header('Content-Type: application/json; charset=utf-8');

// 3. Recupera e valida i parametri GET
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;

if (!$checkin || !$checkout) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Parametri mancanti. Usa: ?checkin=YYYY-MM-DD&checkout=YYYY-MM-DD'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Validazione base del formato data
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato data non valido. Usa AAAA-MM-GG.']);
    exit;
}

if (strtotime($checkout) <= strtotime($checkin)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'La data di check-out deve essere successiva al check-in.']);
    exit;
}

// Estrai l'anno dal check-in. HotelDruid crea tabelle specifiche per anno (es. prenota2026, periodi2026)
$anno = date('Y', strtotime($checkin));
$table_prenota = "prenota" . $anno;
$table_periodi = "periodi" . $anno;

try {
    // 4. Query per trovare le camere DISPONIBILI
    // Logica: Prendi tutte le camere, ESCLUDI quelle che hanno una prenotazione 
    // che si sovrappone all'intervallo [checkin, checkout].
    // Una prenotazione si sovrappone se: (inizio_prenota <= checkout) AND (fine_prenota >= checkin)
    
    $sql = "
        SELECT 
            a.idappartamenti
        FROM appartamenti a
        WHERE a.idappartamenti NOT IN (
            SELECT p.idappartamenti
            FROM {$table_prenota} p
            INNER JOIN {$table_periodi} p_start ON p.iddatainizio = p_start.idperiodi
            INNER JOIN {$table_periodi} p_end ON p.iddatafine = p_end.idperiodi
            WHERE p_start.datainizio <= :checkout 
              AND p_end.datafine >= :checkin
              AND p.idappartamenti > 0 
        )
        ORDER BY a.idappartamenti ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':checkin' => $checkin,
        ':checkout' => $checkout
    ]);

    $camere_disponibili = $stmt->fetchAll();

    // 5. Risposta di successo
    echo json_encode([
        'success' => true,
        'data' => [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'anno_riferimento' => $anno,
            'tabelle_usate' => [
                'prenotazioni' => $table_prenota,
                'periodi' => $table_periodi
            ],
            'camere_disponibili' => $camere_disponibili,
            'totale_libere' => count($camere_disponibili)
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Errore nel database: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}