<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Требуется авторизация']);
    exit;
}

require_once __DIR__ . '/connect-db.php';

$dishesData = $_POST['dishes'] ?? [];

if (empty($dishesData)) {
    echo json_encode(['error' => 'Не выбрано ни одного блюда']);
    exit;
}

try {
    $conn->beginTransaction();
    
    $userId = $_SESSION['user']['id'];
    $stmt = $conn->prepare("
        SELECT shift_id 
        FROM shift_assignments 
        WHERE user_id = :user_id 
        AND shift_id IN (SELECT id FROM shifts WHERE is_open = 1)
        LIMIT 1
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $shift = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$shift) {
        throw new Exception("У вас нет активной смены");
    }
    $shiftId = $shift['shift_id'];
    
    $stmt = $conn->prepare("
        INSERT INTO orders (shift_id, waiter_id, status_id, created_at)
        VALUES (:shift_id, :waiter_id, 1, NOW())
    ");
    $stmt->execute([
        ':shift_id' => $shiftId,
        ':waiter_id' => $userId
    ]);
    $orderId = $conn->lastInsertId();
    
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, dish_id, quantity, price)
        VALUES (:order_id, :dish_id, :quantity, :price)
    ");
    
    $dishIds = array_keys($dishesData);
    
    if (empty($dishIds)) {
        throw new Exception("Нет ID блюд для получения цен");
    }
    
    $placeholders = implode(',', array_fill(0, count($dishIds), '?'));
    $priceStmt = $conn->prepare("SELECT id, price FROM dishes WHERE id IN ($placeholders)");
    $priceStmt->execute($dishIds);
    $prices = $priceStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $calculatedTotal = 0;
    $hasItems = false;
    
    foreach ($dishesData as $dishId => $dish) {
        if (!isset($prices[$dishId])) {
            throw new Exception("Блюдо с ID $dishId не найдено");
        }
        
        $price = $prices[$dishId];
        $quantity = (int)$dish['quantity'];
        
        if ($quantity <= 0) {
            continue;
        }
        
        $stmt->execute([
            ':order_id' => $orderId,
            ':dish_id' => $dishId,
            ':quantity' => $quantity,
            ':price' => $price
        ]);
        
        $calculatedTotal += $price * $quantity;
        $hasItems = true;
    }
    
    if (!$hasItems) {
        throw new Exception("Все блюда имеют нулевое количество");
    }
    
    $conn->commit();
    echo json_encode([
        'success' => true, 
        'order_id' => $orderId, 
        'total_price' => $calculatedTotal
    ]);

    header('Location: /Tortotoro/waiter.php');
    exit;
    
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}