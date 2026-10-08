<?php
/**
 * Endpoint per ricevere prenotazioni esterne (Webhook simulato)
 * Endpoint: POST /api/receive_booking.php
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito. Usa POST.']);
    exit;
}

// 1. Ricevi il payload JSON (simulazione di un webhook da un OTA o Hub)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['guest_name'], $input['checkin'], $input['checkout'], $input['camera_id'], $input['price'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dati prenotazione incompleti.']);
    exit;
}

try {
    // 2. Mappa i dati nel formato CSV richiesto da HotelDruid per l'importazione nativa
    // Nota: Le intestazioni devono corrispondere ESATTAMENTE a quelle che HD si aspetta nel suo "Importa prenotazioni"
    // Di solito sono: Cognome, Nome, Data_inizio, Data_fine, Appartamento, Tariffa, Numero_persone, Prezzo_totale, Pagato, Commento
    
    $hdCsvData = [
        'Cognome' => $input['guest_name'],
        'Nome' => $input['guest_first_name'] ?? 'Ospite',
        'Data_inizio' => $input['checkin'], // Formato YYYY-MM-DD
        'Data_fine' => $input['checkout'],  // Formato YYYY-MM-DD
        'Appartamento' => $input['camera_id'], // Es: "BB1" o "MiPA - 1"
        'Tariffa' => $input['rate_name'] ?? 'Tariffa Web',
        'Numero_persone' => $input['guests'] ?? 2,
        'Prezzo_totale' => number_format($input['price'], 2, ',', ''), // HD usa la virgola per i decimali nel CSV
        'Pagato' => number_format($input['paid'] ?? 0, 2, ',', ''),
        'Commento' => "Importata da Middleware OTA - ID Esterno: " . ($input['external_id'] ?? 'N/A')
    ];

    // 3. Genera la riga CSV
    // Usiamo fputcsv su un buffer di memoria per garantire l'escaping corretto delle virgole e dei campi
    $csvLine = '';
    $temp = fopen('php://memory', 'r+');
    fputcsv($temp, array_values($hdCsvData), ',', '"', '\\');
    rewind($temp);
    $csvLine = stream_get_contents($temp);
    fclose($temp);

    // 4. Salva il CSV in una cartella locale del middleware per revisione/upload
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = 'import_' . date('Ymd_His') . '_' . uniqid() . '.csv';
    $filepath = $uploadDir . $filename;
    
    // Aggiungiamo l'header CSV se il file è nuovo (o lo includiamo sempre per sicurezza di HD)
    $header = "Cognome,Nome,Data_inizio,Data_fine,Appartamento,Tariffa,Numero_persone,Prezzo_totale,Pagato,Commento\n";
    file_put_contents($filepath, $header . $csvLine);

    // 5. (FUTURO) Qui andrebbe la chiamata cURL per fare l'upload automatico al form di HD
    // Per ora, restituiamo il successo e il percorso del file generato.

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione ricevuta e convertita in formato HotelDruid con successo.',
        'data' => [
            'external_id' => $input['external_id'] ?? 'N/A',
            'guest' => $input['guest_name'],
            'camera' => $input['camera_id'],
            'csv_file_generated' => $filename,
            'csv_content_preview' => trim($header . $csvLine)
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore interno: ' . $e->getMessage()]);
}