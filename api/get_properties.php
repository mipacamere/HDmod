<?php
/**
 * Endpoint per leggere tutte le proprietà configurate
 * Endpoint: GET /api/get_properties.php
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

try {
    $loader = new ConfigLoader();
    $allProperties = $loader->getAllProperties();
    
    // Per ogni proprietà, calcola statistiche
    $propertiesWithStats = [];
    foreach ($allProperties as $propId => $prop) {
        $channels = [];
        foreach ($prop['channels'] as $channelName => $channelData) {
            $channels[$channelName] = [
                'enabled' => $channelData['enabled'] ?? false,
                'property_id' => $channelData['property_id'] ?? 'N/A',
                'mappings_count' => count($channelData['mappings'] ?? [])
            ];
        }
        
        $propertiesWithStats[$propId] = [
            'id' => $prop['id'],
            'name' => $prop['name'],
            'channels' => $channels,
            'total_mappings' => array_sum(array_map(function($ch) {
                return count($ch['mappings'] ?? []);
            }, $prop['channels']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $propertiesWithStats
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}