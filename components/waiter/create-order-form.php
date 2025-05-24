<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" action="registration-form.php" method="post">
  <h1 class="form__heading secondary-heading">Создание заказа</h1>
  <div class="form__input-group">
    <div class="form__text-inputs-wrapper">
      <div class="form__input-wrapper">
        <label class="form__input-label" for="shift-start">Начало смены</label>
        <input class="form__text-input" type="text" id="shift-start" placeholder="ГГ.ММ.ДД ЧЧ:ММ">
      </div>
      <div class="form__input-wrapper">
        <label class="form__input-label" for="shift-end">Конец смены</label>
        <input class="form__text-input" type="text" id="shift-end" placeholder="ГГ.ММ.ДД ЧЧ:ММ">
      </div>
    </div>
    <div class="form__input-wrapper">
      <div class="select__wrapper">
        <svg class="select__arrow-icon" width="14" height="11" viewBox="0 0 14 11" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M1.5 1.5L7 9.5L12.5 1.5" stroke="#D1D1D1" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <select class="form__select select" name="select-worker" id="select-worker">
          <option class="select__option" value="default">Выберите сотрудника</option>
          <option class="select__option" value="worker1">Виноградов Кирилл Сергеевич</option>
          <option class="select__option" value="waiter2">Дмитрий Мурзин</option>
        </select>
      </div>
    </div>
    <div class="form__input-wrapper">
      <textarea class="form__textarea" name="current-shift-workers" id="current-shift-workers" placeholder="Сотрудники" disabled></textarea>
    </div>
    <button class="form__button">Добавить смену</button>
  </div>
</form>