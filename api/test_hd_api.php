<?php
define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/HotelDruidApiClient.php';

header('Content-Type: application/json; charset=utf-8');

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

try {
    // AGGIUNTO IL PARAMETRO doc=35
    $client = new HotelDruidApiClient(
        'https://lodge655.journeylodge.com/admin/api.php',
        '35', // ID del documento
        'hFTATNAB',
        '2026' // Anno
    );

    // Proviamo a prendere TUTTE le prenotazioni del 2026 per essere sicuri di trovarne almeno una
    $reservations = $client->getReservations(); 

    echo json_encode([
        'success' => true,
        'message' => "Recuperate " . count($reservations) . " prenotazioni dal server REMOTO",
        'data' => $reservations
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}