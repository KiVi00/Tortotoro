<?php
session_start();

// Проверка аутентификации и прав
if (!isset($_SESSION['user'])) {
    $_SESSION['shift_error'] = 'Требуется авторизация';
    header('Location: /Tortotoro/login.php');
    exit;
}

if ($_SESSION['user']['role_id'] != 1) {
    $_SESSION['shift_error'] = 'Доступ запрещен: недостаточно прав';
    header('Location: /Tortotoro/admin.php');
    exit;
}

// Валидация CSRF токена
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['shift_error'] = 'Ошибка безопасности сессии';
    header('Location: /Tortotoro/admin.php');
    exit;
}

require_once __DIR__ . '/connect-db.php';

// Проверка обязательных полей
$required = ['start_time', 'end_time', 'workers'];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || (is_array($_POST[$field]) && empty($_POST[$field]))) {
        $_SESSION['shift_error'] = 'Все поля обязательны для заполнения';
        header('Location: /Tortotoro/admin.php');
        exit;
    }
}

// Валидация дат
try {
    $start = new DateTime($_POST['start_time']);
    $end = new DateTime($_POST['end_time']);
} catch (Exception $e) {
    $_SESSION['shift_error'] = 'Неверный формат даты';
    header('Location: /Tortotoro/admin.php');
    exit;
}

$now = new DateTime();
$minDuration = new DateInterval('PT1H');
$maxDuration = new DateInterval('PT24H');
$interval = $start->diff($end);

// Проверка временных интервалов
if ($start >= $end) {
    $_SESSION['shift_error'] = 'Конец смены должен быть позже начала';
    header('Location: /Tortotoro/admin.php');
    exit;
}

if ($interval > $maxDuration) {
    $_SESSION['shift_error'] = "Максимальная продолжительность смены - 24 часа";
    header('Location: /Tortotoro/admin.php');
    exit;
}

if ($interval < $minDuration) {
    $_SESSION['shift_error'] = 'Минимальная продолжительность смены - 1 час';
    header('Location: /Tortotoro/admin.php');
    exit;
}

// Проверка работников
$workers = array_map('intval', $_POST['workers']);
$uniqueWorkers = array_unique($workers);

if (count($uniqueWorkers) < 1) {
    $_SESSION['shift_error'] = 'Не выбраны работники';
    header('Location: /Tortotoro/admin.php');
    exit;
}

try {
    $conn->beginTransaction();

    // Создание смены
    $stmt = $conn->prepare("
        INSERT INTO shifts (start_time, end_time, is_open) 
        VALUES (:start, :end, 1)
    ");
    $stmt->execute([
        ':start' => $start->format('Y-m-d H:i:s'),
        ':end' => $end->format('Y-m-d H:i:s')
    ]);
    $shiftId = $conn->lastInsertId();

    // Назначение работников
    $stmt = $conn->prepare("
        INSERT INTO shift_assignments (shift_id, user_id)
        VALUES (:shiftId, :userId)
    ");

    foreach ($uniqueWorkers as $workerId) {
        // Проверка существования пользователя
        $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $check->execute([$workerId]);
        if (!$check->fetch()) {
            throw new Exception("Неверный ID работника: $workerId");
        }
        
        $stmt->execute([
            ':shiftId' => $shiftId,
            ':userId' => $workerId
        ]);
    }

    $conn->commit();
    $_SESSION['shift_success'] = 'Смена успешно создана';
    header('Location: /Tortotoro/admin.php');
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['shift_error'] = 'Ошибка: ' . $e->getMessage();
    error_log('Shift creation error: ' . $e->getMessage());
    header('Location: /Tortotoro/admin.php');
    exit;
}