<div class="form__outer" id="form-outer"></div>
<form
  class="form form--modal form--two-columns"
  action="registration-form.php"
  method="post"
>
  <h1 class="form__heading secondary-heading">Увольнение</h1>
  <div class="form__columns-wrapper">
    <div class="form__input-group">
      <div class="form__text-inputs-wrapper">
        <div class="form__input-wrapper">
          <label class="form__input-label" for="last-name">Фамилия</label>
          <input class="form__text-input" type="text" id="last-name" disabled/>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="first-name">Имя</label>
          <input class="form__text-input" type="text" id="first-name" disabled/>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="patronomic">Отчество</label>
          <input class="form__text-input" type="text" id="patronomic" disabled/>
        </div>
        <div class="form__input-wrapper">
          <label class="form__input-label" for="patronomic">Должность</label>
          <input class="form__text-input" type="text" id="patronomic" disabled/>
        </div>
      </div>
      <button class="form__button">Уволить</button>
    </div>
    <div class="form__photo-group">
      <div class="photo__wrapper">
        <img
          class="photo"
          src="assets/images/images.jpg"
          alt="Загружаемое фото"
        />
      </div>
    </div>
  </div>
</form>
