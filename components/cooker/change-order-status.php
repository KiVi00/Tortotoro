<?php
session_start();
require_once __DIR__ . '/../../php-modules/connect-db.php';

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    die('Не указан номер заказа');
}

try {
    // Получаем информацию о заказе
    $orderStmt = $conn->prepare("
        SELECT 
            o.id,
            os.name AS status,
            os.id AS status_id
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        WHERE o.id = :order_id
        LIMIT 1
    ");
    $orderStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $orderStmt->execute();
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die('Заказ не найден');
    }

    // Определяем следующий доступный статус для повара
    $nextStatus = null;
    if ($order['status_id'] == 1) { // Текущий статус "Принят"
        $statusStmt = $conn->prepare("
            SELECT id, name 
            FROM order_statuses 
            WHERE id = 2
        ");
        $statusStmt->execute();
        $nextStatus = $statusStmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($order['status_id'] == 2) { // Текущий статус "Готовится"
        $statusStmt = $conn->prepare("
            SELECT id, name 
            FROM order_statuses 
            WHERE id = 3
        ");
        $statusStmt->execute();
        $nextStatus = $statusStmt->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal" id="change-status-form" action="php-modules/update-order-status.php" method="post">
    <input type="hidden" name="order_id" value="<?= $orderId ?>">

    <h1 class="form__heading secondary-heading">Изменение статуса заказа №<?= $orderId ?></h1>

    <div class="form__group">
        <label class="form__label">Текущий статус:</label>
        <div class="form__value"><?= htmlspecialchars($order['status']) ?></div>
    </div>

    <?php if ($nextStatus): ?>
        <div class="form__group">
            <label for="new-status" class="form__label">Новый статус:</label>
            <input type="hidden" name="new_status_id" value="<?= $nextStatus['id'] ?>">
            <div class="form__value"><?= htmlspecialchars($nextStatus['name']) ?></div>
        </div>

        <div class="form__button-group">
            <button type="submit" class="form__button">Подтвердить</button>
            <button type="button" class="form__button form__button--cancel" onclick="closeModal()">Отмена</button>
        </div>
    <?php else: ?>
        <div class="form__group">
            <p>Нет доступных действий для данного статуса</p>
        </div>
        <div class="form__actions">
            <button type="button" class="form__button form__button--cancel" onclick="closeModal()">Закрыть</button>
        </div>
    <?php endif; ?>
</form>