<?php
/**
 * Endpoint di sincronizzazione MULTI-PROPRIETÀ con Booking.com (Versione Anti-Overbooking)
 * Endpoint: GET /api/sync_booking.php?action=push_rates&checkin=YYYY-MM-DD&checkout=YYYY-MM-DD
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/ConfigLoader.php';
require_once __DIR__ . '/adapters/ChannelInterface.php';
require_once __DIR__ . '/adapters/BookingComAdapter.php';

header('Content-Type: application/json; charset=utf-8');

// --- FUNZIONE HELPER: Controllo Disponibilità Reale ---
function isDateAvailable($pdo, $anno, $camera_id, $data) {
    $table_prenota = "prenota" . $anno;
    $table_periodi = "periodi" . $anno;
    
    // In HD, una prenotazione dal 10 al 15 ha datainizio=10 e datafine=15.
    // Per sapere se il giorno 12 è libero, cerchiamo prenotazioni che si sovrappongono:
    // (datainizio_prenota <= giorno_successivo) AND (datafine_prenota >= giorno_corrente)
    $next_day = date('Y-m-d', strtotime($data . ' +1 day'));

    try {
        $sql = "SELECT COUNT(p.idprenota) 
                FROM {$table_prenota} p
                INNER JOIN {$table_periodi} p_start ON p.iddatainizio = p_start.idperiodi
                INNER JOIN {$table_periodi} p_end ON p.iddatafine = p_end.idperiodi
                WHERE p.idappartamenti = :camera_id
                  AND p_start.datainizio <= :next_day
                  AND p_end.datafine >= :data
                  AND p.idappartamenti > 0"; // In HD, > 0 significa assegnata a una camera reale
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':camera_id' => $camera_id,
            ':data' => $data,
            ':next_day' => $next_day
        ]);
        
        $booked_count = (int)$stmt->fetchColumn();
        return $booked_count === 0; // Se 0, è libera
        
    } catch (PDOException $e) {
        // Se la tabella non esiste (es. anno non generato), consideriamo libera per non bloccare tutto
        if ($e->getCode() === '42S02') {
            return true;
        }
        // Per altri errori, logghiamo e restituiamo false per sicurezza (meglio chiudere che overbookare)
        error_log("Errore controllo disponibilità: " . $e->getMessage());
        return false;
    }
}
// --- FINE FUNZIONE HELPER ---


// 1. Sicurezza API Key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

$action = $_GET['action'] ?? null;
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;

if ($action === 'push_rates' && $checkin && $checkout) {
    
    $anno = date('Y', strtotime($checkin));
    $table_periodi = "periodi" . $anno;
    
    // Rilevamento dinamico tabella tariffe
    $possible_tariffe_tables = ["tariffe" . $anno, "ntariffe" . $anno];
    $table_tariffe = null;
    foreach ($possible_tariffe_tables as $tbl) {
        $stmt_check = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name");
        $stmt_check->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
        if ($stmt_check->fetch()) {
            $stmt_col = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = 'periodo1'");
            $stmt_col->execute([':db_name' => DB_NAME, ':table_name' => $tbl]);
            if ($stmt_col->fetch()) {
                $table_tariffe = $tbl;
                break;
            }
        }
    }

    if (!$table_tariffe) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => "Tabella tariffe non trovata per l'anno $anno."]);
        exit;
    }

    try {
        // Trova gli ID dei periodi per le date richieste
        $stmt_in = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datainizio = :data");
        $stmt_in->execute([':data' => $checkin]);
        $id_periodo_in = (int)$stmt_in->fetchColumn();

        $stmt_out = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datafine = :data");
        $stmt_out->execute([':data' => $checkout]);
        $id_periodo_out = (int)$stmt_out->fetchColumn();

        if (!$id_periodo_in || !$id_periodo_out) {
            throw new Exception("Date non trovate nei periodi dell'anno $anno.");
        }

        // 2. Carica il ConfigLoader
        $loader = new ConfigLoader();
        $properties = $loader->getPropertiesByChannel('booking');
        
        $results = [];

        // 3. Itera su ogni proprietà configurata per Booking
        foreach ($properties as $property) {
            $propertyId = $property['id'];
            $propertyName = $property['name'];
            $mappings = $property['channels']['booking']['mappings'];
            $credentials = $property['channels']['booking']['credentials'];
            $ota_property_id = $property['channels']['booking']['property_id'];

            $propertyRates = [];

            // 4. Per ogni mappatura, recupera il prezzo REALE e la DISPONIBILITÀ REALE da HotelDruid
            foreach ($mappings as $mapping) {
                $hd_rate_id = $mapping['hd_rate_id'];
                $camera_id = $mapping['camera_id']; // <-- NUOVO: ID della camera in HD
                
                $col_name = "periodo" . $id_periodo_in; 
                
                // Verifica se la colonna esiste e recupera il prezzo
                $stmt_col_check = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :col_name");
                $stmt_col_check->execute([':db_name' => DB_NAME, ':table_name' => $table_tariffe, ':col_name' => $col_name]);
                
                $prezzo = 0;
                if ($stmt_col_check->fetch()) {
                    $stmt_price = $pdo->prepare("SELECT {$col_name} FROM {$table_tariffe} WHERE idtariffe = :id");
                    $stmt_price->execute([':id' => $hd_rate_id]);
                    $prezzo = (float)$stmt_price->fetchColumn();
                }

                // Costruiamo l'array nel formato standard che l'Adapter si aspetta
                for ($i = $id_periodo_in; $i < $id_periodo_out; $i++) {
                    $stmt_date = $pdo->prepare("SELECT datainizio FROM {$table_periodi} WHERE idperiodi = :id");
                    $stmt_date->execute([':id' => $i]);
                    $data = $stmt_date->fetchColumn();
                    
                    // --- LOGICA ANTI-OVERBOOKING ---
                    $is_available = false;
                    if ($prezzo > 0) {
                        // Controlla nel DB se la camera specifica è libera in quella data
                        $is_available = isDateAvailable($pdo, $anno, $camera_id, $data);
                    }
                    
                    $propertyRates[] = [
                        'date' => $data,
                        'price' => $prezzo,
                        'available' => $is_available, // <-- ORA USA IL RISULTATO REALE DAL DB
                        'hd_rate_id' => $hd_rate_id,
                        'camera_id' => $camera_id,
                        'ota_room_id' => $mapping['ota_room_id'],
                        'ota_rate_name' => $mapping['ota_rate_name']
                    ];
                }
            }

            // 5. Invia i dati reali all'Adapter di Booking per questa specifica proprietà
            $bookingAdapter = new Adapters\BookingComAdapter([
                'property_id' => $ota_property_id,
                'username' => $credentials['username'],
                'password' => $credentials['password']
            ]);

            $adapterResult = $bookingAdapter->pushRatesAndAvailability($propertyRates);
            
            $results[$propertyId] = [
                'property_name' => $propertyName,
                'status' => $adapterResult['status'],
                'message' => $adapterResult['message'],
                'rates_processed' => count($propertyRates) / ($id_periodo_out - $id_periodo_in),
                'xml_preview' => substr($adapterResult['xml_generato'], 0, 200) . '...'
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Sincronizzazione multi-proprietà con controllo disponibilità completata',
            'data' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'table_used' => $table_tariffe,
                'properties_synced' => $results
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parametri mancanti. Usa: ?action=push_rates&checkin=YYYY-MM-DD&checkout=YYYY-MM-DD']);
}