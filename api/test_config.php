<?php
/**
 * Test del ConfigLoader Multi-Property
 * Endpoint: GET /api/test_config.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/ConfigLoader.php';

header('Content-Type: application/json; charset=utf-8');

// Sicurezza API Key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

try {
    $loader = new ConfigLoader();
    
    // Test 1: Tutte le proprietà
    $allProperties = $loader->getAllProperties();
    
    // Test 2: Solo proprietà con Booking abilitato
    $bookingProperties = $loader->getPropertiesByChannel('booking');

    echo json_encode([
        'success' => true,
        'message' => 'Configurazione caricata con successo',
        'debug' => [
            'totale_propieta' => count($allProperties),
            'nomi_propieta' => array_keys($allProperties),
            'propieta_con_booking_attivo' => count($bookingProperties)
        ],
        'esempio_mappatura_bbnazionale_booking' => $allProperties['bbnazionale']['channels']['booking']['mappings'][0] ?? null
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}