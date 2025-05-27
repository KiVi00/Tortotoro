<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Tortotoro/index.php');
    exit;
}

// CSRF-защита
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Ошибка безопасности сессии';
    header('Location: /Tortotoro/index.php');
    exit;
}

require_once __DIR__ . '/connect-db.php';

$login = trim($_POST['login'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($login) || empty($password)) {
    $_SESSION['error'] = 'Все поля обязательны для заполнения';
    header('Location: /index.php');
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT 
            u.id, 
            u.first_name, 
            u.last_name, 
            u.patronomic, 
            u.password_hash, 
            u.role_id, 
            r.name AS role_name 
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE login = :login 
        AND is_active = 1
    ");
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => trim($user['last_name'] . ' ' . $user['first_name'] . ' ' . ($user['patronomic'] ?? '')),
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name']
        ];

        $redirect = match ((int) $user['role_id']) {
            1 => '/Tortotoro/admin.php',
            2 => '/Tortotoro/waiter.php',
            3 => '/Tortotoro/cooker.php',
        };

        header("Location: $redirect");
        exit;
    }

    $_SESSION['error'] = 'Неверный логин или пароль';
    header('Location: /Tortotoro/index.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = 'Ошибка при авторизации';
    header('Location: /Tortotoro/index.php');
    exit;
}