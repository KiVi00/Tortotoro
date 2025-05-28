<?php
session_start();

// Проверка прав администратора
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    die(json_encode(['error' => 'Доступ запрещен']));
}

// CSRF защита
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['error' => 'Ошибка безопасности сессии']));
}

require_once __DIR__ . '/connect-db.php';

$shiftId = (int) ($_POST['shift_id'] ?? 0);

try {
    // Обновляем статус смены
    $stmt = $conn->prepare("UPDATE shifts SET is_open = 0 WHERE id = ?");
    $stmt->execute([$shiftId]);

    // Закрываем все связанные заказы
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status_id = 5  # ID статуса 'Отменен'
        WHERE shift_id = ? AND status_id NOT IN (4,5) # Не обновляем уже оплаченные/отмененные
    ");
    $stmt->execute([$shiftId]);

    echo json_encode(['success' => true]);
    header('Location: /Tortotoro/admin.php');
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}