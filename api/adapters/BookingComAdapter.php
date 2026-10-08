<?php
namespace Adapters;

class BookingComAdapter implements ChannelInterface {
    
    private $propertyId;
    private $username;
    private $password;
    private $apiEndpoint = "https://supply-xml.booking.com/connectivity/"; // Endpoint di Sandbox/Test

    public function __construct($config) {
        $this->propertyId = $config['property_id'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function pushRatesAndAvailability(array $standardRates): array {
        // 1. Traduci il nostro formato standard in XML di Booking
        $xmlPayload = $this->buildBookingXml($standardRates);

        // 2. Invia la richiesta HTTP a Booking (simulazione per ora)
        // In produzione, useremmo cURL con certificati SSL specifici richiesti da Booking
        // $response = $this->sendCurlRequest($this->apiEndpoint . 'update_rates.php', $xmlPayload);
        
        return [
            'status' => 'success',
            'ota' => 'Booking.com',
            'message' => 'Payload XML generato correttamente (pronto per l\'invio)',
            'xml_generato' => htmlspecialchars($xmlPayload) // Mostriamo l'XML per debug
        ];
    }

    public function parseIncomingBooking(array $otaPayload): array {
        // Traduce l'XML/JSON di Booking nel nostro formato standard per create_booking.php
        return [
            'checkin' => $otaPayload['ArrivalDate'] ?? null,
            'checkout' => $otaPayload['DepartureDate'] ?? null,
            'camera_id' => $otaPayload['RoomTypeId'] ?? null,
            'guest_nome' => $otaPayload['GuestFirstName'] ?? 'N/A',
            'guest_cognome' => $otaPayload['GuestLastName'] ?? 'N/A',
            'guest_email' => $otaPayload['GuestEmail'] ?? 'N/A',
            'prezzo_totale' => $otaPayload['TotalPrice'] ?? 0,
            'ota_reference' => $otaPayload['BookingId'] ?? null
        ];
    }

    private function buildBookingXml(array $rates): string {
        // Costruzione dell'XML richiesto da Booking.com Connectivity API
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><RateAvailabilityRequest></RateAvailabilityRequest>');
        $xml->addAttribute('version', '1.0');
        
        $auth = $xml->addChild('Authentication');
        $auth->addChild('Username', $this->username);
        $auth->addChild('Password', $this->password);
        $auth->addChild('PropertyId', $this->propertyId);

        $ratesNode = $xml->addChild('Rates');
        
        foreach ($rates as $rate) {
            $rateNode = $ratesNode->addChild('Rate');
            $rateNode->addChild('Date', $rate['date']);
            $rateNode->addChild('Price', number_format($rate['price'], 2, '.', ''));
            $rateNode->addChild('Availability', $rate['available'] ? '1' : '0');
        }

        return $xml->asXML();
    }
}