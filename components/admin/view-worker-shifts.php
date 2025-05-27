<?php

session_start();

if (!isset($_SESSION['user'])) {
    die('<div class="form__error">Доступ запрещен</div>');
}

require_once '../../php-modules/connect-db.php';

$workerId = (int)($_GET['worker_id'] ?? 0);

try {

    $stmt = $conn->prepare("
        SELECT 
            CONCAT(last_name, ' ', first_name, ' ', COALESCE(patronomic, '')) AS full_name,
            role_id
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$workerId]);
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "
        SELECT 
            s.id,
            s.start_time,
            s.end_time,
            s.is_open,
GROUP_CONCAT(
    CONCAT(u.last_name, ' ', LEFT(u.first_name, 1), '.') 
    SEPARATOR ', '
) AS workers
        FROM shifts s
        JOIN shift_assignments sa ON s.id = sa.shift_id
        JOIN users u ON sa.user_id = u.id
        JOIN roles r ON u.role_id = r.id
        WHERE sa.user_id = ?
        GROUP BY s.id
        ORDER BY s.start_time DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$workerId]);
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}

$stmt = $conn->prepare("SELECT name FROM roles WHERE id = ?");
$stmt->execute([$worker['role_id']]);
$position = $stmt->fetchColumn();
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns">
    <h1 class="form__heading secondary-heading">Смены работника: <?= htmlspecialchars($worker['full_name']) ?></h1>
    <div class="form__info-group">
        <p class="form__info-item">Должность: <?= htmlspecialchars($position) ?></p>
    </div>
    
    <table class="table">
        <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell table__cell--head">Дата</th>
                <th class="table__cell table__cell--head">Время</th>
                <th class="table__cell table__cell--head">Коллеги</th>
                <th class="table__cell table__cell--head">Статус</th>
            </tr>
        </thead>
        <tbody class="table__body">
            <?php foreach($shifts as $shift): ?>
                <?php
                $start = new DateTime($shift['start_time']);
                $end = new DateTime($shift['end_time']);
                $status = $shift['is_open'] ? 'Открыта' : 'Закрыта';
                $statusClass = $shift['is_open'] ? 'table__cell--open' : 'table__cell--closed';
                ?>
                <tr class="table__row">
                    <td class="table__cell"><?= $start->format('d.m.Y') ?></td>
                    <td class="table__cell"><?= $start->format('H:i') ?>–<?= $end->format('H:i') ?></td>
                    <td class="table__cell"><?= htmlspecialchars($shift['workers']) ?></td>
                    <td class="table__cell <?= $statusClass ?>"><?= $status ?></td>
                </tr>
            <?php endforeach; ?>
            
            <?php if(empty($shifts)): ?>
                <tr class="table__row">
                    <td colspan="4" class="table__cell">Нет данных о сменах</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>