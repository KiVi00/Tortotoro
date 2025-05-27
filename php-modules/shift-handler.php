<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    $_SESSION['shift_error'] = 'Доступ запрещен';
    header('Location: /Tortotoro/admin.php');
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['shift_error'] = 'Ошибка безопасности сессии';
    header('Location: /Tortotoro/admin.php');
    exit;
}

require_once __DIR__ . '/connect-db.php';

$required = ['start_time', 'end_time', 'workers'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['shift_error'] = 'Все поля обязательны для заполнения';
        header('Location: /Tortotoro/admin.php');
        exit;
    }
}

$start = $_POST['start_time'];
$end = $_POST['end_time'];
$workers = $_POST['workers'];
$minDuration = 1 * 3600;

$startTimestamp = strtotime($start);
$endTimestamp = strtotime($end);
$maxDuration = 24 * 3600;

if ($endTimestamp <= $startTimestamp) {
    $_SESSION['shift_error'] = 'Конец смены не может быть раньше начала';
    header('Location: /Tortotoro/admin.php');
    exit;
}

$duration = $endTimestamp - $startTimestamp;

if ($duration > $maxDuration) {
    $hours = round($duration / 3600, 1);
    $_SESSION['shift_error'] = "Максимальная продолжительность смены - 24 часа (указано: {$hours} ч)";
    header('Location: /Tortotoro/admin.php');
    exit;
}

if ($duration < $minDuration) {
    $_SESSION['shift_error'] = 'Минимальная продолжительность смены - 1 час';
    header('Location: /Tortotoro/admin.php');
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO shifts (start_time, end_time, is_open) 
        VALUES (?, ?, 1)
    ");
    $stmt->execute([$start, $end]);
    $shiftId = $conn->lastInsertId();

    $stmt = $conn->prepare("
        INSERT INTO shift_assignments (shift_id, user_id)
        VALUES (?, ?)
    ");

    foreach ($workers as $workerId) {
        $stmt->execute([$shiftId, $workerId]);
    }

    header('Location: /Tortotoro/admin.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['shift_error'] = 'Ошибка создания смены: ' . $e->getMessage();
    header('Location: /Tortotoro/admin.php');
    exit;
}