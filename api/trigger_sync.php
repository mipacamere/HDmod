<?php
/**
 * Endpoint per triggerare una sincronizzazione manuale
 * Endpoint: POST /api/trigger_sync.php
 */

define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/ConfigLoader.php';
require_once __DIR__ . '/adapters/ChannelInterface.php';
require_once __DIR__ . '/adapters/BookingComAdapter.php';

header('Content-Type: application/json; charset=utf-8');

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if ($api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Usa POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$checkin = $input['checkin'] ?? date('Y-m-d', strtotime('+7 days'));
$checkout = $input['checkout'] ?? date('Y-m-d', strtotime('+14 days'));

// Funzione per loggare le sincronizzazioni
function logSync($propertyId, $status, $message, $details = []) {
    $logFile = __DIR__ . '/logs/sync_log.json';
    $logs = [];
    
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true) ?? [];
    }
    
    $logs[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'property_id' => $propertyId,
        'status' => $status,
        'message' => $message,
        'details' => $details
    ];
    
    // Mantieni solo gli ultimi 100 log
    $logs = array_slice($logs, -100);
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
}

try {
    $loader = new ConfigLoader();
    $properties = $loader->getPropertiesByChannel('booking');
    
    $anno = date('Y', strtotime($checkin));
    $table_periodi = "periodi" . $anno;
    
    // Rilevamento tabella tariffe
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
        throw new Exception("Tabella tariffe non trovata per l'anno $anno.");
    }
    
    // Trova ID periodi
    $stmt_in = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datainizio = :data");
    $stmt_in->execute([':data' => $checkin]);
    $id_periodo_in = (int)$stmt_in->fetchColumn();
    
    $stmt_out = $pdo->prepare("SELECT idperiodi FROM {$table_periodi} WHERE datafine = :data");
    $stmt_out->execute([':data' => $checkout]);
    $id_periodo_out = (int)$stmt_out->fetchColumn();
    
    $results = [];
    
    foreach ($properties as $property) {
        $propertyId = $property['id'];
        $mappings = $property['channels']['booking']['mappings'];
        $credentials = $property['channels']['booking']['credentials'];
        $ota_property_id = $property['channels']['booking']['property_id'];
        
        $propertyRates = [];
        
        foreach ($mappings as $mapping) {
            $hd_rate_id = $mapping['hd_rate_id'];
            $camera_id = $mapping['camera_id'];
            $col_name = "periodo" . $id_periodo_in;
            
            $stmt_col_check = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :col_name");
            $stmt_col_check->execute([':db_name' => DB_NAME, ':table_name' => $table_tariffe, ':col_name' => $col_name]);
            
            $prezzo = 0;
            if ($stmt_col_check->fetch()) {
                $stmt_price = $pdo->prepare("SELECT {$col_name} FROM {$table_tariffe} WHERE idtariffe = :id");
                $stmt_price->execute([':id' => $hd_rate_id]);
                $prezzo = (float)$stmt_price->fetchColumn();
            }
            
            for ($i = $id_periodo_in; $i < $id_periodo_out; $i++) {
                $stmt_date = $pdo->prepare("SELECT datainizio FROM {$table_periodi} WHERE idperiodi = :id");
                $stmt_date->execute([':id' => $i]);
                $data = $stmt_date->fetchColumn();
                
                $is_available = false;
                if ($prezzo > 0) {
                    $is_available = isDateAvailable($pdo, $anno, $camera_id, $data);
                }
                
                $propertyRates[] = [
                    'date' => $data,
                    'price' => $prezzo,
                    'available' => $is_available,
                    'hd_rate_id' => $hd_rate_id,
                    'camera_id' => $camera_id,
                    'ota_room_id' => $mapping['ota_room_id'],
                    'ota_rate_name' => $mapping['ota_rate_name']
                ];
            }
        }
        
        $bookingAdapter = new Adapters\BookingComAdapter([
            'property_id' => $ota_property_id,
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ]);
        
        $adapterResult = $bookingAdapter->pushRatesAndAvailability($propertyRates);
        
        $results[$propertyId] = [
            'property_name' => $property['name'],
            'status' => $adapterResult['status'],
            'rates_processed' => count($propertyRates) / ($id_periodo_out - $id_periodo_in)
        ];
        
        // Logga il risultato
        logSync(
            $propertyId,
            $adapterResult['status'],
            $adapterResult['message'],
            [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'rates_count' => count($propertyRates) / ($id_periodo_out - $id_periodo_in)
            ]
        );
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Sincronizzazione completata',
        'data' => [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'properties_synced' => $results
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    logSync('system', 'error', $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Funzione helper per controllo disponibilità
function isDateAvailable($pdo, $anno, $camera_id, $data) {
    $table_prenota = "prenota" . $anno;
    $table_periodi = "periodi" . $anno;
    $next_day = date('Y-m-d', strtotime($data . ' +1 day'));
    
    try {
        $sql = "SELECT COUNT(p.idprenota) 
                FROM {$table_prenota} p
                INNER JOIN {$table_periodi} p_start ON p.iddatainizio = p_start.idperiodi
                INNER JOIN {$table_periodi} p_end ON p.iddatafine = p_end.idperiodi
                WHERE p.idappartamenti = :camera_id
                  AND p_start.datainizio <= :next_day
                  AND p_end.datafine >= :data
                  AND p.idappartamenti > 0";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':camera_id' => $camera_id,
            ':data' => $data,
            ':next_day' => $next_day
        ]);
        
        return ((int)$stmt->fetchColumn()) === 0;
        
    } catch (PDOException $e) {
        return true;
    }
}