<?php
require_once __DIR__ . '/connect-db.php';

try {
    $stmt = $conn->prepare("
        SELECT id, name, price 
        FROM dishes 
        WHERE is_available = 1
        ORDER BY name
    ");
    $stmt->execute();
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($dishes);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки блюд']);
}