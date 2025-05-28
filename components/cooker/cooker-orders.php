<?php
session_start();
require_once __DIR__ . '/../../php-modules/connect-db.php';

try {
    $currentUserId = $_SESSION['user']['id'];

    // Получаем текущую открытую смену пользователя
    $shiftQuery = "
        SELECT s.id 
        FROM shifts s
        JOIN shift_assignments sa ON s.id = sa.shift_id
        WHERE sa.user_id = :user_id
          AND s.is_open = 1
        LIMIT 1
    ";

    $shiftStmt = $conn->prepare($shiftQuery);
    $shiftStmt->bindParam(':user_id', $currentUserId, PDO::PARAM_INT);
    $shiftStmt->execute();
    $shift = $shiftStmt->fetch(PDO::FETCH_ASSOC);

    if (!$shift) {
        $orders = [];
    } else {
        $shiftId = $shift['id'];

        // Запрос для получения заказов текущей смены
        $query = "
        SELECT 
            o.id,
            o.created_at,
            os.name AS status,
            GROUP_CONCAT(CONCAT(d.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS dish_names
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN dishes d ON oi.dish_id = d.id
        JOIN order_statuses os ON o.status_id = os.id
        WHERE o.shift_id = :shift_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':shift_id', $shiftId, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
    $orders = [];
}
?>

<h1 class="content__title">Панель повара</h1>
<div class="table__outer">
    <table class="table">
        <caption class="table__heading">
            <h2 class="secondary-heading">Заказы за смену</h2>
        </caption>
        <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell table__cell--head">Позиции</th>
                <th class="table__cell table__cell--head">Время создания</th>
                <th class="table__cell table__cell--head">Статус</th>
            </tr>
        </thead>
        <tbody class="table__body">
            <?php if (empty($orders)): ?>
                <tr class="table__row">
                    <td colspan="3" class="table__cell">Нет заказов за текущую смену</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $createdAt = new DateTime($order['created_at']);
                    $time = $createdAt->format('H:i');
                    
                    $statusClass = '';
                    if ($order['status'] === 'Принят' || $order['status'] === 'Готовится') {
                        $statusClass = 'table__cell--interactive';
                    }
                    ?>
                    <tr class="table__row">
                        <td class="table__cell table__cell--dynamic">
                            <?= htmlspecialchars($order['dish_names']) ?>
                        </td>
                        <td class="table__cell">
                            <?= $time ?>
                        </td>
                        <td class="table__cell <?= $statusClass ?>" 
                            data-order-id="<?= $order['id'] ?>" 
                            data-modal="components/cooker/change-order-status.php">
                            <?= htmlspecialchars($order['status']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>