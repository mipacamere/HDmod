<?php
namespace Adapters;

/**
 * Contratto che ogni Adapter OTA deve rispettare.
 * Garantisce che il nostro Middleware possa parlare con qualsiasi OTA in modo standardizzato.
 */
interface ChannelInterface {
    /**
     * Invia disponibilità e prezzi all'OTA
     * @param array $standardRates Formato standard del nostro middleware
     * @return array Risultato dell'operazione
     */
    public function pushRatesAndAvailability(array $standardRates): array;

    /**
     * Riceve una prenotazione dall'OTA (via Webhook o Polling)
     * @param array $otaPayload Dati grezzi dall'OTA
     * @return array Dati mappati nel formato standard del nostro middleware
     */
    public function parseIncomingBooking(array $otaPayload): array;
}