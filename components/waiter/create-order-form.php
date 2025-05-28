<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" id="create-order-form" action="php-modules/create-order.php"
  method="post">
  <h1 class="form__heading secondary-heading">Создание заказа</h1>



  <div class="form__input-group form__input-group--thin">
    <div class="form__input-wrapper form__input-wrapper--custom">
      <div id="selected-dishes" class="selected-dishes"></div>
      <div class="select__wrapper">
        <svg class="select__arrow-icon" width="14" height="11" viewBox="0 0 14 11" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M1.5 1.5L7 9.5L12.5 1.5" stroke="#D1D1D1" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <select class="form__select select" name="dish-select" id="dish-select">
          <option class="select__option" value="">Выберите блюдо</option>
        </select>

      </div>
      <div class="form__input-wrapper form__input-wrapper--row count-wrapper">
        <button type="button" class="table__button table__button--left" id="decrease-button">
          <svg width="30" height="30" viewBox="0 0 18 6" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M15 6H3C1.346 6 0 4.654 0 3C0 1.346 1.346 0 3 0H15C16.654 0 18 1.346 18 3C18 4.654 16.654 6 15 6ZM3 2C2.449 2 2 2.449 2 3C2 3.551 2.449 4 3 4H15C15.551 4 16 3.551 16 3C16 2.449 15.551 2 15 2H3Z"
              fill="black" />
          </svg>
        </button>
        <span class="form__text-content form__text-content--count" id="quantity-display"
          style="min-width: 30px; text-align: center;">1</span>
        <button type="button" class="table__button table__button--right" id="increase-button">
          <svg class="table__add-icon" width="30" height="30" viewBox="0 0 40 41" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_150_374)">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M13.3333 4.94444C13.3333 3.7657 13.8016 2.63524 14.6351 1.80175C15.4686 0.968252 16.599 0.5 17.7778 0.5H22.2222C23.401 0.5 24.5314 0.968252 25.3649 1.80175C26.1984 2.63524 26.6667 3.7657 26.6667 4.94444V13.8333H35.5556C36.7343 13.8333 37.8648 14.3016 38.6983 15.1351C39.5317 15.9686 40 17.099 40 18.2778V22.7222C40 23.901 39.5317 25.0314 38.6983 25.8649C37.8648 26.6984 36.7343 27.1667 35.5556 27.1667H26.6667V36.0556C26.6667 37.2343 26.1984 38.3648 25.3649 39.1983C24.5314 40.0317 23.401 40.5 22.2222 40.5H17.7778C16.599 40.5 15.4686 40.0317 14.6351 39.1983C13.8016 38.3648 13.3333 37.2343 13.3333 36.0556V27.1667H4.44444C3.2657 27.1667 2.13524 26.6984 1.30175 25.8649C0.468252 25.0314 0 23.901 0 22.7222V18.2778C0 17.099 0.468252 15.9686 1.30175 15.1351C2.13524 14.3016 3.2657 13.8333 4.44444 13.8333H13.3333V4.94444ZM22.2222 4.94444H17.7778V16.0556C17.7778 16.6449 17.5437 17.2102 17.1269 17.6269C16.7102 18.0437 16.1449 18.2778 15.5556 18.2778H4.44444V22.7222H15.5556C16.1449 22.7222 16.7102 22.9564 17.1269 23.3731C17.5437 23.7898 17.7778 24.3551 17.7778 24.9444V36.0556H22.2222V24.9444C22.2222 24.3551 22.4564 23.7898 22.8731 23.3731C23.2898 22.9564 23.8551 22.7222 24.4444 22.7222H35.5556V18.2778H24.4444C23.8551 18.2778 23.2898 18.0437 22.8731 17.6269C22.4564 17.2102 22.2222 16.6449 22.2222 16.0556V4.94444Z"
                fill="white" />
            </g>
            <defs>
              <clipPath id="clip0_150_374">
                <rect width="40" height="40" fill="white" transform="translate(0 0.5)" />
              </clipPath>
            </defs>
          </svg>
        </button>
      </div>
      <button type="button" class="form__button" id="add-dish-button">Добавить блюдо</button>
    </div>

  </div>

  <!-- Общая сумма -->
  <div class="form__total">
    <span>Итого:</span>
    <span id="total-amount">0&#8381;</span>
  </div>

  <!-- Блок для отображения ошибок -->
  <div id="form-errors" class="form__errors" style="color: red; margin-bottom: 15px; display: none;"></div>
    <button type="submit" class="form__button">Создать заказ</button>
</form>