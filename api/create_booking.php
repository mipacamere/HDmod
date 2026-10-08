<?php
/**
 * Endpoint per creare prenotazioni in HD da fonti esterne (es. Demone iReservation)
 * Endpoint: POST /api/create_booking.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// 1. Autenticazione
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
$required = ['external_id', 'guest_first_name', 'guest_last_name', 'checkin', 'checkout', 'camera_id', 'price'];

foreach ($required as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Campo mancante: $field"]);
        exit;
    }
}

try {
    $pdo->beginTransaction();

    // 3. Inserisci o recupera il cliente
    $stmtClient = $pdo->prepare("SELECT idclienti FROM phpr_clienti WHERE cognome = :cognome AND nome = :nome AND email = :email LIMIT 1");
    $stmtClient->execute([
        ':cognome' => $input['guest_last_name'],
        ':nome' => $input['guest_first_name'],
        ':email' => $input['email'] ?? ''
    ]);
    $client = $stmtClient->fetch();

    if ($client) {
        $idclienti = $client['idclienti'];
    } else {
        $stmtNewClient = $pdo->prepare("INSERT INTO phpr_clienti (cognome, nome, email, telefono) VALUES (:cognome, :nome, :email, :telefono)");
        $stmtNewClient->execute([
            ':cognome' => $input['guest_last_name'],
            ':nome' => $input['guest_first_name'],
            ':email' => $input['email'] ?? '',
            ':telefono' => $input['phone'] ?? ''
        ]);
        $idclienti = $pdo->lastInsertId();
    }

    // 4. Genera un ID contratto univoco (massimo attuale + 1)
    $stmtMaxContratto = $pdo->query("SELECT MAX(CAST(numero AS UNSIGNED)) as max_num FROM phpr_contratti");
    $max_num = $stmtMaxContratto->fetch()['max_num'] ?? 0;
    $new_contratto_numero = $max_num + 1;
    $idcontratti = (string)$new_contratto_numero;

    // 5. Inserisci la prenotazione (contratto)
    $stmtContratto = $pdo->prepare("
        INSERT INTO phpr_contratti 
        (idcontratti, tipo, stato, numero, idclienti, idappartamenti, idtariffe, data_inizio, data_fine, testo, utente_inserimento) 
        VALUES 
        (:idcontratti, 's', 'M', :numero, :idclienti, :idappartamenti, '1', :data_inizio, :data_fine, :testo, 'api_sync')
    ");
    
    $testo_notes = "EXT_ID:" . $input['external_id'] . " | SRC:" . ($input['source'] ?? 'OTA') . " | " . ($input['notes'] ?? '');
    
    $stmtContratto->execute([
        ':idcontratti' => $idcontratti,
        ':numero' => $new_contratto_numero,
        ':idclienti' => $idclienti,
        ':idappartamenti' => $input['camera_id'], // Es: "1" o "BB1"
        ':data_inizio' => $input['checkin'],      // YYYY-MM-DD
        ':data_fine' => $input['checkout'],       // YYYY-MM-DD
        ':testo' => $testo_notes
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione creata con successo in HotelDruid.',
        'data' => [
            'idcontratti' => $idcontratti,
            'idclienti' => $idclienti,
            'external_id' => $input['external_id']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore database: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore interno: ' . $e->getMessage()]);
}
