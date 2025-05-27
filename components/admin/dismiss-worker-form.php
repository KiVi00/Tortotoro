<?php
session_start();

require_once '../../php-modules/connect-db.php';
$workerId = $_GET['worker_id'] ?? 0;

try {
    $stmt = $conn->prepare("
        SELECT 
            id,
            last_name,
            first_name,
            patronomic,
            role_id,
            photo_file
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$workerId]);
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Ошибка загрузки данных: " . $e->getMessage());
}

if(!$worker) {
    die("Работник не найден");
}

$stmt = $conn->prepare("SELECT name FROM roles WHERE id = ?");
$stmt->execute([$worker['role_id']]);
$role = $stmt->fetchColumn();
?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" action="php-modules/dismiss-worker-handler.php" method="post">
  <h1 class="form__heading secondary-heading">Увольнение</h1>
  <div class="form__columns-wrapper">
    <div class="form__input-group">
      <div class="form__text-inputs-wrapper">
        <input type="hidden" name="worker_id" value="<?= $worker['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        
        <div class="form__input-wrapper">
          <label class="form__input-label">Фамилия</label>
          <input class="form__text-input" value="<?= htmlspecialchars($worker['last_name']) ?>" disabled>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label">Имя</label>
          <input class="form__text-input" value="<?= htmlspecialchars($worker['first_name']) ?>" disabled>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label">Отчество</label>
          <input class="form__text-input" value="<?= htmlspecialchars($worker['patronomic'] ?? '') ?>" disabled>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label">Должность</label>
          <input class="form__text-input" value="<?= htmlspecialchars($role) ?>" disabled>
        </div>
      </div>
      <button class="form__button form__button--danger" type="submit">Подтвердить увольнение</button>
    </div>
    <div class="form__photo-group">
      <div class="photo__wrapper">
        <img class="photo" 
             src="<?= htmlspecialchars($worker['photo_file'] ?? 'assets/images/default-avatar.jpg') ?>" 
             alt="Фото работника">
      </div>
    </div>
  </div>
</form>