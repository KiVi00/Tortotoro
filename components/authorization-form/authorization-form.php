<form class="form form--authorization" action="php-modules/authorization.php" method="post">
  <h1 class="form__heading secondary-heading">Авторизация</h1>
  <?php if(isset($_SESSION['error'])): ?>
    <div class="form__error"><?= htmlspecialchars($_SESSION['error']) ?></div>
  <?php unset($_SESSION['error']); endif; ?>
  <div class="form__input-group">
    <div class="form__text-inputs-wrapper">
      <div class="form__input-wrapper">
        <label class="form__input-label" for="login">Логин</label>
        <input class="form__text-input" type="text" id="login" name="login" required>
      </div>
      <div class="form__input-wrapper">
        <label class="form__input-label" for="password">Пароль</label>
        <input class="form__text-input" type="password" id="password" name="password" required>
      </div>
    </div>
  </div>
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
  <button class="form__button" type="submit">Войти</button>
</form>