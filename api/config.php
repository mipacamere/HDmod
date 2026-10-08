	<?php
/**
 * API Config - Middleware per HotelDruid
 * Connessione sicura al database di HD
 */

// Impedisci l'accesso diretto via browser (solo API calls)
if (!defined('API_ACCESS')) {
    http_response_code(403);
    exit('Accesso diretto non consentito');
}

// Configurazione database (stessi dati che hai usato per installare HD)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hoteldruid');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Connessione al database con PDO (più sicuro di mysqli)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Errore di connessione al database: ' . $e->getMessage());
}

// Chiave segreta per l'autenticazione delle API
// CAMBIALA con una stringa lunga e casuale (lettere, numeri, simboli)
define('API_SECRET_KEY', 'MiaChiaveSegretaSuperComplessa_2026');

// --- Configurazione iReservation (per invio prenotazioni) ---
define('IRES_API_URL', 'https://api.ireservation.it');
define('IRES_CALENDAR_ID', 330); // Sostituisci con il tuo Calendar ID
define('IRES_USERNAME', 'il_tuo_username');
define('IRES_PASSWORD', 'la_tua_password');
define('IRES_API_KEY', 'la_tua_api_key');
