<?php
define('API_ACCESS', true);
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Controlla la struttura della tabella clienti
    $stmt = $pdo->query("DESCRIBE clienti");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Controlla l'ID più alto attuale
    $stmt2 = $pdo->query("SELECT MAX(idclienti) as max_id FROM clienti");
    $max_id = $stmt2->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => 'Struttura tabella clienti:',
        'columns' => $columns,
        'max_id_attuale' => $max_id
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT);
}