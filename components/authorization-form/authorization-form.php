<form class="form form--authorization" action="php-modules/authorization.php" method="post">
  <h1 class="form__heading secondary-heading">Авторизация</h1>
  <div class="form__input-group">
    <div class="form__text-inputs-wraper">
      <div class="form__input-wrapper">
        <label class="form__input-label" for="login">Логин</label>
        <input class="form__text-input" type="text" id="login" />
      </div>
      <div class="form__input-wrapper">
        <label class="form__input-label" for="password">Пароль</label>
        <input class="form__text-input" type="password" id="password" />
      </div>
    </div>
  </div>
  <a href="admin.php">админ</a>
  <a href="waiter.php">Официант</a>
  <a href="cooker.php">Повар</a>
  <button class="form__button">Войти</button>
</form>