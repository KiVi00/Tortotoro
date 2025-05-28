<?php
session_start();

// Добавляем заголовок JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Метод не поддерживается']));
}

require_once __DIR__ . '/connect-db.php';

// Проверка прав
if (!isset($_SESSION['user'])) {
    die(json_encode(['error' => 'Доступ запрещен']));
}

// CSRF проверка
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['error' => 'Недействительный токен']));
}

// Проверка наличия данных
if (!isset($_POST['shift_id']) || empty($_POST['workers'])) {
    die(json_encode(['error' => 'Неполные данные']));
}

$shiftId = (int) $_POST['shift_id'];
$workers = array_map('intval', $_POST['workers']);

try {
    $conn->beginTransaction();

    // Удаляем старых работников
    $stmt = $conn->prepare("DELETE FROM shift_assignments WHERE shift_id = ?");
    $stmt->execute([$shiftId]);

    // Добавляем новых
    $stmt = $conn->prepare("INSERT INTO shift_assignments (shift_id, user_id) VALUES (?, ?)");
    foreach ($workers as $workerId) {
        $stmt->execute([$shiftId, $workerId]);
    }

    $conn->commit();
    echo json_encode(['success' => true]);

    header('Location: /Tortotoro/admin.php');
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}