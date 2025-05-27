<?php session_start() ?>

<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" action="php-modules/registration-handler.php" method="post"
  enctype="multipart/form-data">
  <h1 class="form__heading secondary-heading">Регистрация</h1>
  <div class="form__columns-wrapper">
    <div class="form__input-group">
      <div class="form__text-inputs-wrapper">
        <div class="form__input-wrapper">
          <label class="form__input-label" for="login">Логин</label>
          <input class="form__text-input" type="text" id="login" name="login" required>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="password">Пароль</label>
          <input class="form__text-input" type="password" id="password" name="password" required minlength="8">
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="last-name">Фамилия</label>
          <input class="form__text-input" type="text" id="last-name" name="last_name" required>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="first-name">Имя</label>
          <input class="form__text-input" type="text" id="first-name" name="first_name" required>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="patronomic">Отчество</label>
          <input class="form__text-input" type="text" id="patronomic" name="patronomic">
        </div>
      </div>
      <div class="form__input-wrapper">
        <div class="select__wrapper">
          <svg class="select__arrow-icon" width="14" height="11" viewBox="0 0 14 11" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M1.5 1.5L7 9.5L12.5 1.5" stroke="#D1D1D1" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
          <select class="form__select select" name="role_id" id="role" required>
            <option class="select__option" value="">Выберите роль</option>
            <option class="select__option" value="3">Повар</option>
            <option class="select__option" value="2">Официант</option>
            <option class="select__option" value="1">Администратор</option>
          </select>
        </div>
      </div>
      <input name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <button class="form__button" type="submit">Зарегистрировать</button>
    </div>
    <div class="form__photo-group">
      <div class="photo__wrapper">
        <img class="photo" src="/Tortotoro/assets/images/default.jpg" alt="Фото сотрудника" id="photo-preview">
      </div>
      <label class="form__button" for="file-input">Выбрать фото</label>
      <input id="file-input" type="file" name="photo" accept=".jpg, .jpeg, .png, .webp" hidden>
    </div>
  </div>
</form>

