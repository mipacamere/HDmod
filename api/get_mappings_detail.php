<?php
/**
 * Endpoint per ottenere i dettagli completi delle mappature
 * Endpoint: GET /api/get_mappings_detail.php?property_id=bbnazionale&channel=booking
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/ConfigLoader.php';

header('Content-Type: application/json; charset=utf-8');

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

$property_id = $_GET['property_id'] ?? null;
$channel = $_GET['channel'] ?? null;

if (!$property_id || !$channel) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parametri mancanti. Usa: ?property_id=xxx&channel=yyy']);
    exit;
}

try {
    $loader = new ConfigLoader();
    $property = $loader->getProperty($property_id);
    
    if (!$property) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => "Proprietà '$property_id' non trovata."]);
        exit;
    }
    
    if (!isset($property['channels'][$channel])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => "Canale '$channel' non configurato per questa proprietà."]);
        exit;
    }
    
    $channelData = $property['channels'][$channel];
    $mappings = $channelData['mappings'] ?? [];
    
    // Per ogni mappatura, verifica se la tariffa HD esiste e ha un prezzo
    $mappingsWithDetails = [];
    foreach ($mappings as $mapping) {
        $hd_rate_id = $mapping['hd_rate_id'];
        $camera_id = $mapping['camera_id'] ?? 'N/A';
        
        $mappingsWithDetails[] = [
            'hd_rate_id' => $hd_rate_id,
            'hd_rate_name' => $mapping['hd_rate_name'] ?? 'N/A',
            'camera_id' => $camera_id,
            'ota_room_id' => $mapping['ota_room_id'] ?? 'N/A',
            'ota_rate_id' => $mapping['ota_rate_id'] ?? 'N/A',
            'ota_rate_name' => $mapping['ota_rate_name'] ?? 'N/A'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'property_id' => $property_id,
            'property_name' => $property['name'],
            'channel' => $channel,
            'property_id_ota' => $channelData['property_id'] ?? 'N/A',
            'credentials_configured' => !empty($channelData['credentials']['username']) && $channelData['credentials']['username'] !== 'tuo_username_xml',
            'mappings' => $mappingsWithDetails
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}