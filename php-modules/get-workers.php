<?php
require_once __DIR__ . '/connect-db.php';

try {
    $stmt = $conn->prepare("
        SELECT id, first_name, last_name 
        FROM users 
        WHERE role_id IN (2, 3) 
        AND is_active = 1
    ");
    $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}