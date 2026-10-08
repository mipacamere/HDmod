<?php
/**
 * Test connessione al database di HotelDruid
 */

// Abilita l'accesso API
define('API_ACCESS', true);

// Includi la configurazione
require_once __DIR__ . '/config.php';

// Header JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Conta le camere nel database
    $stmt = $pdo->query("SELECT COUNT(*) as totale FROM appartamenti");
    $risultato = $stmt->fetch();
    
    // Risposta di successo
    echo json_encode([
        'success' => true,
        'message' => 'Connessione al database di HotelDruid riuscita!',
        'data' => [
            'totale_camere' => $risultato['totale']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}