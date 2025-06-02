<?php
session_start();
require_once '../php-modules/connect-db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

$orderId = $_POST['order_id'] ?? null;
$newStatusId = $_POST['new_status_id'] ?? null;

if (!$orderId || !$newStatusId) {
    http_response_code(400);
    echo json_encode(['error' => 'Недостаточно данных']);
    exit;
}

try {
    $checkStmt = $conn->prepare("SELECT id, shift_id, waiter_id, status_id FROM orders WHERE id = :order_id");
    $checkStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $checkStmt->execute();
    $order = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Заказ не найден']);
        exit;
    }

    $currentStatusId = $order['status_id'];
    $shiftId = $order['shift_id'];
    $waiterId = $order['waiter_id'];
    $userId = $_SESSION['user']['id'] ?? null;
    $userRoleId = $_SESSION['user']['role_id'] ?? null;

    $hasAccess = false;
    $isChef = false;

    if ($userRoleId == 2) {
        if ($waiterId == $userId) {
            $hasAccess = true;
        }
    } elseif ($userRoleId == 3) {
        $isChef = true;
        $stmt = $conn->prepare("SELECT 1 FROM shift_assignments WHERE shift_id = :shift_id AND user_id = :user_id");
        $stmt->bindParam(':shift_id', $shiftId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetch()) {
            $hasAccess = true;
        }
    }

    if (!$hasAccess) {
        http_response_code(403);
        echo json_encode(['error' => 'Доступ запрещен']);
        exit;
    }

    if ($isChef) {
        if (
            ($currentStatusId == 1 && $newStatusId == 2) ||
            ($currentStatusId == 2 && $newStatusId == 3)
        ) {
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Повар может менять только: Принят → Готовится, Готовится → Готов']);
            exit;
        }
    } else {
        $allowedTransitions = [
            1 => [5],
            2 => [5],  
            3 => [4, 5],
            4 => [],
            5 => [] 
        ];

        if (!in_array($newStatusId, $allowedTransitions[$currentStatusId] ?? [])) {
            http_response_code(403);
            echo json_encode(['error' => 'Официант может: Отменить заказ (кроме Оплачен/Отменен) или Готов → Оплачен']);
            exit;
        }
    }

    $updateStmt = $conn->prepare("UPDATE orders SET status_id = :status_id WHERE id = :order_id");
    $updateStmt->bindParam(':status_id', $newStatusId, PDO::PARAM_INT);
    $updateStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $updateStmt->execute();

    $statusStmt = $conn->prepare("SELECT name FROM order_statuses WHERE id = :status_id");
    $statusStmt->bindParam(':status_id', $newStatusId, PDO::PARAM_INT);
    $statusStmt->execute();
    $status = $statusStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'new_status' => $status['name'] ?? 'Неизвестный статус',
        'order_id' => $orderId
    ]);

    if ($userRoleId == 2) {
        header('Location: /Tortotoro/waiter.php');
    } else if ($userRoleId == 3) {
        header('Location: /Tortotoro/cooker.php');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}