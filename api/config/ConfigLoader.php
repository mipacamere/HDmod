<?php
/**
 * ConfigLoader - Gestisce la configurazione multi-proprietà e multi-OTA
 */

class ConfigLoader {
    private $propertiesDir;
    private $properties = [];

    public function __construct() {
        // Percorso assoluto alla cartella delle proprietà
        $this->propertiesDir = __DIR__ . '/properties';
        $this->loadAllProperties();
    }

    private function loadAllProperties() {
        if (!is_dir($this->propertiesDir)) {
            throw new Exception("La directory delle proprietà non esiste: " . $this->propertiesDir);
        }

        $files = glob($this->propertiesDir . '/*.json');
        foreach ($files as $file) {
            $jsonContent = file_get_contents($file);
            $data = json_decode($jsonContent, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {
                $this->properties[$data['id']] = $data;
            } else {
                error_log("Errore nel parsing del file di configurazione: " . basename($file));
            }
        }
    }

    /**
     * Restituisce tutte le proprietà configurate
     */
    public function getAllProperties(): array {
        return $this->properties;
    }

    /**
     * Restituisce una specifica proprietà per ID
     */
    public function getProperty(string $propertyId): ?array {
        return $this->properties[$propertyId] ?? null;
    }

    /**
     * Restituisce le proprietà che hanno un canale specifico abilitato
     */
    public function getPropertiesByChannel(string $channelName): array {
        $result = [];
        foreach ($this->properties as $property) {
            if (isset($property['channels'][$channelName]) && $property['channels'][$channelName]['enabled']) {
                $result[] = $property;
            }
        }
        return $result;
    }
}