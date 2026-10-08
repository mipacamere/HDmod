<?php
/**
 * Motore di Sincronizzazione iReservation
 * File: includes/interconnect/ireservation/sync.php
 */

// Includi la configurazione
require_once(__DIR__ . '/var.php');

/**
 * Funzione per scrivere nei log di sincronizzazione
 */
function ires_log($message, $level = 'INFO') {
    global $ireservation_log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($ireservation_log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Funzione per ottenere il Token JWT da iReservation
 */
function ires_get_token() {
    global $ireservation_api_key, $ireservation_username, $ireservation_password;
    
    $url = 'https://api.ireservation.it/v1/oauth2/authorizations';
    $payload = json_encode([
        'username' => $ireservation_username,
        'password' => $ireservation_password
    ]);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . $ireservation_api_key
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($http_code === 200 && isset($data['result']['token'])) {
        ires_log("Autenticazione riuscita. Token ottenuto.");
        return $data['result']['token'];
    } else {
        ires_log("ERRORE Autenticazione: HTTP $http_code. Risposta: " . json_encode($data), 'ERROR');
        return false;
    }
}

/**
 * Funzione generica per chiamate API iReservation
 */
function ires_api_call($endpoint, $payload, $token) {
    global $ireservation_api_key;
    $url = 'https://api.ireservation.it' . $endpoint;
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token,
        'x-api-key: ' . $ireservation_api_key
    ];
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($http_code >= 400 || (isset($data['code']) && $data['code'] !== 0)) {
        ires_log("ERRORE API su $endpoint: HTTP $http_code. Msg: " . ($data['message'] ?? 'Sconosciuto'), 'ERROR');
        return false;
    }
    
    return $data['result'] ?? $data;
}

// --- ESECUZIONE DI TEST (Da rimuovere in produzione) ---
ires_log("--- Avvio script di test ---");
$token = ires_get_token();
if ($token) {
    ires_log("Token valido, pronto per le operazioni di sync.");
    // QUI ANDRÀ LA LOGICA DI POLLING E SALVATAGGIO
}
ires_log("--- Fine script di test ---\n");
?>