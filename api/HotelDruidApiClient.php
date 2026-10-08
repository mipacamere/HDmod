<?php
/**
 * HotelDruid API Client
 * Si connette al documento "Esporta prenotazioni" configurato come API
 * e restituisce le prenotazioni in un formato standardizzato.
 */

class HotelDruidApiClient {
    private $apiUrl;
    private $docId;
    private $password;
    private $year;

    public function __construct($apiUrl, $docId, $password, $year = null) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->docId = $docId;
        $this->password = $password;
        $this->year = $year ?: date('Y');
    }

    public function getReservations($startDate = null, $endDate = null) {
        // COSTRUZIONE URL CORRETTA CON IL PARAMETRO doc
        $url = $this->apiUrl . "?doc=" . $this->docId . "&pass=" . urlencode($this->password) . "&res_year=" . $this->year;
        
        if ($startDate && $endDate) {
            $url .= "&data_inizio=" . $startDate . "&data_fine=" . $endDate;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Se il server remoto ha un certificato SSL non valido, decommenta la riga sotto:
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Errore HTTP {$httpCode} nel recupero dati da HotelDruid: " . ($error ?: 'Sconosciuto'));
        }

        // Debug: se vuoi vedere cosa restituisce esattamente il server remoto, decommenta la riga sotto:
        // error_log("RISPOSTA GREZZA DA HD: " . substr($response, 0, 500));

        return $this->parseCsvResponse($response);
    }

    private function parseCsvResponse($csvString) {
        $lines = explode("\n", trim($csvString));
        if (count($lines) < 2) {
            return []; 
        }

        $headers = str_getcsv(array_shift($lines), ',', '"', '\\');
        $reservations = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $row = str_getcsv($line, ',', '"', '\\');
            
            $reservations[] = [
                'checkin' => $this->normalizeDate($row[0] ?? ''),
                'checkout' => $this->normalizeDate($row[1] ?? ''),
                'cognome' => $row[2] ?? '',
                'nome' => $row[3] ?? '',
                'email' => $row[4] ?? '',
                'telefono' => $row[5] ?? '',
                'guests' => (int)($row[6] ?? 1),
                'camera_id' => trim($row[7] ?? ''), 
                'tariffa_nome' => trim($row[8] ?? ''),
                'prezzo_totale' => (float)str_replace(',', '.', $row[10] ?? 0),
                'pagato' => (float)str_replace(',', '.', $row[11] ?? 0),
                'commento' => $row[12] ?? '',
            ];
        }

        return $reservations;
    }

    private function normalizeDate($dateStr) {
        if (empty($dateStr)) return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return $dateStr;
        }
        $parts = explode('-', $dateStr);
        if (count($parts) === 3) {
            return sprintf("%04d-%02d-%02d", $parts[2], $parts[1], $parts[0]);
        }
        return $dateStr;
    }
}