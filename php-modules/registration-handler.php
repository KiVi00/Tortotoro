<?php
session_start();

if (!isset($_SESSION['user'])) {
    die(json_encode(['error' => 'Доступ запрещен']));
}

if ($_SESSION['user']['role_id'] != 1) {
    die(json_encode(['error' => 'Недостаточно прав']));
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['reg_error'] = 'Ошибка безопасности сессии';
    header('Location: /Tortotoro/admin.php');
    exit;
}

require_once __DIR__ . '/connect-db.php';

$required = ['login', 'password', 'first_name', 'last_name', 'role_id'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['reg_error'] = 'Все обязательные поля должны быть заполнены';
        header('Location: /Tortotoro/admin.php');
        exit;
    }
}

$login = trim($_POST['login']);
$password = $_POST['password'];
$firstName = trim($_POST['first_name']);
$lastName = trim($_POST['last_name']);
$patronomic = trim($_POST['patronomic'] ?? null);
$roleId = (int)$_POST['role_id'];

if (strlen($password) < 8) {
    $_SESSION['reg_error'] = 'Пароль должен быть не менее 8 символов';
    header('Location: /Tortotoro/admin.php');
    exit;
}

$photoPath = '/Tortotoro/assets/images/default.jpg';
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('user_') . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = '/Tortotoro/uploads/avatars/' . $filename;
    }
}

try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->execute([$login]);
    
    if ($stmt->fetch()) {
        $_SESSION['reg_error'] = 'Логин уже занят';
        header('Location: /Tortotoro/admin.php');
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("
        INSERT INTO users (
            first_name, 
            last_name, 
            patronomic, 
            login, 
            password_hash, 
            photo_file, 
            role_id, 
            is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");
    
    $stmt->execute([
        $firstName,
        $lastName,
        $patronomic,
        $login,
        $passwordHash,
        $photoPath,
        $roleId
    ]);

     $_SESSION['reg_error'] = 'Регистрация успешна ';
    header('Location: /Tortotoro/admin.php');
    exit;

} catch(PDOException $e) {
    $_SESSION['reg_error'] = 'Ошибка регистрации: ' . $e->getMessage();
    header('Location: /Tortotoro/admin.php');
    exit;
}