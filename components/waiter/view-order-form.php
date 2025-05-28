<?php
session_start();
require_once __DIR__ . '/../../php-modules/connect-db.php';

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    die('Не указан номер заказа');
}

try {
    // Основные данные заказа
    $orderStmt = $conn->prepare("
        SELECT 
            o.id,
            SUM(oi.price * oi.quantity) AS total_price,
            o.created_at,
            os.name AS status
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN order_statuses os ON o.status_id = os.id
        WHERE o.id = :order_id
        GROUP BY o.id
    ");
    $orderStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $orderStmt->execute();
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die('Заказ не найден');
    }

    // Позиции заказа
    $itemsStmt = $conn->prepare("
        SELECT d.name, oi.price, oi.quantity
        FROM order_items oi
        JOIN dishes d ON oi.dish_id = d.id
        WHERE oi.order_id = :order_id
    ");
    $itemsStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $itemsStmt->execute();
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Форматирование времени
    $createdAt = new DateTime($order['created_at']);
    $time = $createdAt->format('H:i');
} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}
?>

<div class="form__outer" id="form-outer">
  <form class="form form--modal">
    <h1 class="form__heading secondary-heading">Просмотр заказа</h1>
    
    <div class="order-summary">
      <div class="order-summary__item">
        <span class="order-summary__label">Номер заказа:</span>
        <span class="order-summary__value">№<?= $order['id'] ?></span>
      </div>
      <div class="order-summary__item">
        <span class="order-summary__label">Общая стоимость:</span>
        <span class="order-summary__value"><?= number_format($order['total_price'], 0, '', ' ') ?>&#8381;</span>
      </div>
      <div class="order-summary__item">
        <span class="order-summary__label">Время создания:</span>
        <span class="order-summary__value"><?= $time ?></span>
      </div>
      <div class="order-summary__item">
        <span class="order-summary__label">Статус:</span>
        <span class="order-summary__value"><?= htmlspecialchars($order['status']) ?></span>
      </div>
    </div>

    <table class="table">
      <caption class="table__heading">
        <h2 class="secondary-heading">Состав заказа</h2>
      </caption>
      <thead class="table__head">
        <tr class="table__row">
          <th class="table__cell table__cell--head">Блюдо</th>
          <th class="table__cell table__cell--head">Количество</th>
          <th class="table__cell table__cell--head">Цена</th>
          <th class="table__cell table__cell--head">Сумма</th>
        </tr>
      </thead>
      <tbody class="table__body">
        <?php foreach ($items as $item): ?>
          <tr class="table__row">
            <td class="table__cell"><?= htmlspecialchars($item['name']) ?></td>
            <td class="table__cell"><?= $item['quantity'] ?></td>
            <td class="table__cell"><?= $item['price'] ?>&#8381;</td>
            <td class="table__cell"><?= $item['price'] * $item['quantity'] ?>&#8381;</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    
    <div class="form__actions">
      <button type="button" class="form__button" onclick="closeModal()">Закрыть</button>
    </div>
  </form>
</div>