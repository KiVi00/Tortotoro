<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    die(json_encode(['error' => 'Доступ запрещен']));
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['error' => 'Ошибка безопасности сессии']));
}

require_once __DIR__ . '/connect-db.php';

$workerId = (int)$_POST['worker_id'] ?? 0;

try {
    $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$workerId]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);

    header('Location: /Tortotoro/admin.php');
    exit;
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}