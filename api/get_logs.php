<?php
/**
 * Endpoint per leggere i log delle sincronizzazioni
 * Endpoint: GET /api/get_logs.php
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

$logFile = __DIR__ . '/logs/sync_log.json';
$logs = [];

if (file_exists($logFile)) {
    $logs = json_decode(file_get_contents($logFile), true) ?? [];
}

// Inverti l'ordine per mostrare i più recenti prima
$logs = array_reverse($logs);

echo json_encode([
    'success' => true,
    'data' => $logs
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);