<?php
require_once '../../php-modules/connect-db.php';
session_start();

if (!isset($_SESSION['user'])) {
    die('<div class="form__error">Доступ запрещен</div>');
}

$shiftId = (int) ($_GET['shift_id'] ?? 0);

if ($shiftId === 0) {
    die('<div class="form__error">Неверный идентификатор смены</div>');
}

if (!isset($_SESSION['user']))
    die('Доступ запрещен');

$shiftId = (int) $_GET['shift_id'] ?? 0;

try {
    // Получаем информацию о смене
    $stmt = $conn->prepare("SELECT is_open FROM shifts WHERE id = ?");
    $stmt->execute([$shiftId]);
    $isOpen = $stmt->fetchColumn();

    if (!$isOpen)
        die('Смена закрыта для редактирования');

    // Получаем работников смены
    $stmt = $conn->prepare("
        SELECT u.id, CONCAT(u.last_name, ' ', u.first_name) AS name 
        FROM shift_assignments sa 
        JOIN users u ON sa.user_id = u.id 
        WHERE sa.shift_id = ?
    ");
    $stmt->execute([$shiftId]);
    $currentWorkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Все активные работники
    $stmt = $conn->prepare("
        SELECT id, CONCAT(last_name, ' ', first_name) AS name 
        FROM users 
        WHERE is_active = 1
    ");
    $stmt->execute();
    $allWorkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal" action="php-modules/update-shift-workers.php" method="post">
    <h1 class="form__heading secondary-heading">Редактирование смены</h1>

    <input type="hidden" name="shift_id" value="<?= $shiftId ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form__input-group">
        <div class="form__input-wrapper">
            <label class="form__input-label">Текущие работники:</label>
            <div id="current-workers" class="workers-list">
                <?php foreach ($currentWorkers as $worker): ?>
                    <div class="worker-tag" data-user-id="<?= $worker['id'] ?>">
                        <?= htmlspecialchars($worker['name']) ?>
                        <input type="hidden" name="workers[]" value="<?= $worker['id'] ?>">
                        <button type="button" class="remove-worker">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>

    <div class="form__input-group">
        <div class="form__input-wrapper"><label class="form__input-label">Добавить работника:</label>
            <div class="select__wrapper"> <svg class="select__arrow-icon" width="14" height="11" viewBox="0 0 14 11"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.5 1.5L7 9.5L12.5 1.5" stroke="#D1D1D1" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <select class="form__select" id="select-worker">
                </select>
            </div>
        </div>

    </div>
    <div class="form__button-group">
        <button type="button" class="form__button" id="add-worker">Добавить</button>
        <button type="submit" class="form__button">Сохранить</button>
    </div>
</form>

<script>
</script>