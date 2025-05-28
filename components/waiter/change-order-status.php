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

    // Получаем все возможные статусы
    $statusesStmt = $conn->prepare("SELECT id, name FROM order_statuses");
    $statusesStmt->execute();
    $statuses = $statusesStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}

$currentStatusId = $order['status_id'];

// Матрица разрешенных переходов (ID текущего статуса => ID разрешенных новых статусов)
$allowedTransitions = [
    1 => [5],   // Принят -> Отменен
    2 => [5],   // Готовится -> Отменен
    3 => [4, 5], // Готов -> Оплачен/Отменен
    4 => [],     // Оплачен -> нельзя
    5 => []      // Отменен -> нельзя
];

// Фильтруем доступные статусы
$filteredStatuses = [];
foreach ($statuses as $status) {
    // Разрешить только статусы, указанные в матрице переходов
    if (in_array($status['id'], $allowedTransitions[$currentStatusId] ?? [])) {
        $filteredStatuses[] = $status;
    }
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

    <div>
        <label for="new-status" class="form__label">Новый статус:</label>
        <?php if (!empty($filteredStatuses)): ?>
            <div class="select__wrapper">
                <select id="new-status" name="new_status_id" class="form__select">
                    <?php foreach ($filteredStatuses as $status): ?>
                        <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <svg class="select__arrow-icon" width="14" height="11" viewBox="0 0 14 11" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.5 1.5L7 9.5L12.5 1.5" stroke="#D1D1D1" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
        <?php else: ?>
            <div class="form__message">Нет доступных действий для этого статуса</div>
        <?php endif; ?>
    </div>

    <div class="form__button-group">
        <?php if (!empty($filteredStatuses)): ?>
            <button type="submit" class="form__button">Сохранить</button>
        <?php endif; ?>
        <button type="button" class="form__button form__button--cancel" onclick="closeModal()">Закрыть</button>
    </div>
</form>