<?php
/**
 * Configurazione Modulo iReservation per HotelDruid
 * File: includes/interconnect/ireservation/var.php
 */

// Credenziali API (DA COMPILARE QUANDO ARRIVANO DA DAVIDE)
$ireservation_api_key = 'INSERISCI_QUI_X_API_KEY';
$ireservation_username = 'INSERISCI_QUI_USERNAME';
$ireservation_password = 'INSERISCI_QUI_PASSWORD';
$ireservation_calendar_id = 330; // Sostituisci con il tuo Calendar ID reale

// Mappatura Camere: HD idappartamenti => iReservation room_id
// Esempio: se la camera "1" in HD corrisponde alla camera "104" in iReservation
$ireservation_room_mapping = [
    '1' => 104, // 'ID_CAMERA_HD' => ID_CAMERA_IRESERVATION
    '2' => 105,
    // Aggiungi tutte le tue camere qui
];

// Impostazioni di Sincronizzazione
$ireservation_polling_minutes = 1; // Controlla nuove prenotazioni ogni X minuti
$ireservation_test_mode = true;    // TRUE = usa date nel 2028 per i test. FALSE = produzione.
$ireservation_test_checkin = '2028-11-01';
$ireservation_test_checkout = '2028-11-05';

// Percorso per i log (relativo alla root di HD)
$ireservation_log_file = __DIR__ . '/logs/sync_' . date('Y-m') . '.log';

?>