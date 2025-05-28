<?php
require_once '../../php-modules/connect-db.php';
session_start();

// Проверка прав доступа
if (!isset($_SESSION['user'])) {
    die('<div class="form__error">Доступ запрещен</div>');
}

$shiftId = (int)($_GET['shift_id'] ?? 0);

try {
    // Получаем информацию о смене
    $stmt = $conn->prepare("
        SELECT 
            s.start_time,
            s.end_time,
            GROUP_CONCAT(
                CONCAT(u.last_name, ' ', LEFT(u.first_name, 1), '.') 
                SEPARATOR ', '
            ) AS workers
        FROM shifts s
        LEFT JOIN shift_assignments sa ON s.id = sa.shift_id
        LEFT JOIN users u ON sa.user_id = u.id
        WHERE s.id = ?
    ");
    $stmt->execute([$shiftId]);
    $shift = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получаем заказы смены
    $query = "
        SELECT 
            o.id,
            o.created_at,
            os.name AS status,
            SUM(oi.quantity * oi.price) AS total,
            GROUP_CONCAT(
                CONCAT(d.name, ' (', oi.quantity, '×', oi.price, '₽)') 
                SEPARATOR ', '
            ) AS items
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN dishes d ON oi.dish_id = d.id
        WHERE o.shift_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$shiftId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die('<div class="form__error">Ошибка загрузки данных</div>');
}

$start = new DateTime($shift['start_time']);
$end = new DateTime($shift['end_time']);
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns">
    <h1 class="form__heading secondary-heading">
        Заказы за смену <?= $start->format('d.m.Y') ?>
    </h1>
    
    <div class="form__info-block">
        <p>Время: <?= $start->format('H:i').' - '.$end->format('H:i') ?></p>
        <p>Работники: <?= htmlspecialchars($shift['workers'] ?? 'Нет данных') ?></p>
    </div>

    <table class="table">
        <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell table__cell--head">Позиции</th>
                <th class="table__cell table__cell--head">Стоимость</th>
                <th class="table__cell table__cell--head">Время создания</th>
                <th class="table__cell table__cell--head">Статус</th>
            </tr>
        </thead>
        <tbody class="table__body">
            <?php foreach($orders as $order): ?>
                <?php
                $createdAt = new DateTime($order['created_at']);
                ?>
                <tr class="table__row">
                    <td class="table__cell"><?= htmlspecialchars($order['items']) ?></td>
                    <td class="table__cell"><?= $order['total'] ?>₽</td>
                    <td class="table__cell"><?= $createdAt->format('H:i') ?></td>
                    <td class="table__cell"><?= htmlspecialchars($order['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            
            <?php if(empty($orders)): ?>
                <tr class="table__row">
                    <td colspan="4" class="table__cell">Нет заказов за эту смену</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>