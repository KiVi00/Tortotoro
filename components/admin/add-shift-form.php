<?php session_start() ?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" action="php-modules/shift-handler.php" method="post">
  <h1 class="form__heading secondary-heading">Добавление смены</h1>
  <?php if(isset($_SESSION['shift_error'])): ?>
    <div class="form__error"><?= htmlspecialchars($_SESSION['shift_error']) ?></div>
  <?php unset($_SESSION['shift_error']); endif; ?>
  
  <div class="form__input-group">
    <div class="form__text-inputs-wrapper">
      <div class="form__input-wrapper">
        <label class="form__input-label" for="shift-start">Начало смены</label>
        <input class="form__text-input" type="datetime-local" id="shift-start" name="start_time" required>
      </div>
      <div class="form__input-wrapper">
        <label class="form__input-label" for="shift-end">Конец смены</label>
        <input class="form__text-input" type="datetime-local" id="shift-end" name="end_time" required>
      </div>
    </div>
    <div class="form__input-wrapper">
      <div class="select__wrapper">
        <select class="form__select select" name="workers[]" id="select-worker" multiple required>
        </select>
      </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <button class="form__button" type="submit">Добавить смену</button>
  </div>
</form>