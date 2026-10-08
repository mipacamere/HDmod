<?php
/**
 * Endpoint per sincronizzare una prenotazione da HD verso iReservation
 * Endpoint: POST /api/sync_to_ireservation.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// 1. Autenticazione Middleware
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito. Usa POST.']);
    exit;
}

// 2. Ricevi e valida il payload JSON
$input = json_decode(file_get_contents('php://input'), true);

// Campi richiesti per la creazione
$required_create = ['hd_contratto_id', 'guest_first_name', 'guest_last_name', 'checkin', 'checkout', 'ires_room_id', 'price'];
foreach ($required_create as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Campo mancante: $field"]);
        exit;
    }
}

try {
    // 3. Costruisci il payload per iReservation
    $ires_payload = [
        "lang" => "it",
        "parameters" => [
            "calendarId" => (int)IRES_CALENDAR_ID,
            "propertyUsername" => IRES_USERNAME,
            "propertyPassword" => IRES_PASSWORD,
            "action" => "I", // I = Insert (crea prenotazione diretta)
            "guestFirstName" => $input['guest_first_name'],
            "guestLastName" => $input['guest_last_name'],
            "guestEmail" => $input['email'] ?? 'sync@hoteldruid.local',
            "guestPhoneNumber" => $input['phone'] ?? '',
            "dateCheckIn" => $input['checkin'],      // Formato YYYY-MM-DD
            "dateCheckOut" => $input['checkout'],    // Formato YYYY-MM-DD
            "hostNotes" => "Sincronizzato da HD Middleware - ID HD: " . $input['hd_contratto_id'],
            "rooms" => [
                [
                    "id" => (int)$input['ires_room_id'],
                    "numberAdults" => (int)($input['adults'] ?? 1),
                    "numberChildren" => (int)($input['children'] ?? 0),
                    "price" => (float)$input['price']
                ]
            ],
            "idDistributor" => 0 // 0 = Prenotazione diretta (NON OTA, blocca solo la disponibilità)
        ]
    ];

    // 4. Esegui la chiamata cURL a iReservation
    $ch = curl_init(IRES_API_URL . '/reservations/save');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($ires_payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'x-api-key: ' . IRES_API_KEY
        ],
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $ires_data = json_decode($response, true);

    // 5. Gestisci la risposta
    if ($http_code === 200 && isset($ires_data['code']) && $ires_data['code'] === 0) {
        $ires_reservation_id = $ires_data['result']['idReservation'] ?? null;

        if ($ires_reservation_id) {
            // 6. Aggiorna il database di HD per segnare che è sincronizzato
            // Aggiungiamo l'ID iReservation al campo 'testo' del contratto per tracciabilità
            $hd_contratto_id = $input['hd_contratto_id'];
            $update_stmt = $pdo->prepare("
                UPDATE phpr_contratti 
                SET testo = CONCAT(COALESCE(testo, ''), ' | IRES_SYNC_ID:', :ires_id) 
                WHERE idcontratti = :hd_id
            ");
            $update_stmt->execute([
                ':ires_id' => $ires_reservation_id,
                ':hd_id' => $hd_contratto_id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Prenotazione sincronizzata con successo su iReservation.',
                'data' => [
                    'hd_contratto_id' => $hd_contratto_id,
                    'ireservation_id' => $ires_reservation_id
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception("Risposta iReservation valida ma manca l'idReservation.");
        }
    } else {
        $error_msg = $ires_data['message'] ?? 'Errore sconosciuto da iReservation';
        throw new Exception("iReservation ha rifiutato la richiesta: $error_msg (HTTP $http_code)");
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore database HD: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore di sincronizzazione: ' . $e->getMessage()]);
}
