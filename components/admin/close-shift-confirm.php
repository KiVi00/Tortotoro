<?php
session_start();

require_once '../../php-modules/connect-db.php';

$shiftId = (int)($_GET['shift_id'] ?? 0);

try {
    $stmt = $conn->prepare("SELECT is_open FROM shifts WHERE id = ?");
    $stmt->execute([$shiftId]);
    $isOpen = $stmt->fetchColumn();

    if (!$isOpen) die('Смену невозможно повторно закрыть');

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
    
} catch(PDOException $e) {
    die('<div class="form__error">Ошибка загрузки данных</div>');
}

if(!$shift) {
    die('<div class="form__error">Смена не найдена</div>');
}

$start = new DateTime($shift['start_time']);
$end = new DateTime($shift['end_time']);
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal" action="php-modules/close-shift-handler.php" method="post">
    <h1 class="form__heading secondary-heading">Подтверждение закрытия смены</h1>
    
    <div class="form__info-block">
        <p>Дата: <?= $start->format('d.m.Y') ?></p>
        <p>Время: <?= $start->format('H:i').' - '.$end->format('H:i') ?></p>
        <p>Работники: <?= htmlspecialchars($shift['workers']) ?></p>
    </div>
    
    <input type="hidden" name="shift_id" value="<?= $shiftId ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
    <div class="form__button-group">
        <button type="button" class="form__button form__button--cancel" onclick="closeModal()">Отмена</button>
        <button type="submit" class="form__button form__button--danger">Подтвердить закрытие</button>
    </div>
</form>