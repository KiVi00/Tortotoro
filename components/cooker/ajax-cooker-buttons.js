"use strict";

window.closeModal = function () {
  const modalContent = document.getElementById("modal-window-content");
  if (!modalContent) return;

  modalContent.classList.remove("content__modal--active");
  document.body.style.overflowY = "scroll";

  setTimeout(() => {
    modalContent.innerHTML = "";
  }, 200);
};

// Обработчики для формы создания заказа
function initOrderForm() {
  const form = document.getElementById('create-order-form');
  if (!form || form.dataset.initialized) return;
  form.dataset.initialized = true;

  // Делегирование событий для динамических элементов
  form.addEventListener('click', function (e) {
    // Увеличение количества
    if (e.target.closest('#increase-button')) {
      const quantityDisplay = document.getElementById('quantity-display');
      quantityDisplay.textContent = parseInt(quantityDisplay.textContent) + 1;
    }

    // Уменьшение количества
    if (e.target.closest('#decrease-button')) {
      const quantityDisplay = document.getElementById('quantity-display');
      const current = parseInt(quantityDisplay.textContent);
      if (current > 1) {
        quantityDisplay.textContent = current - 1;
      }
    }

    // Добавление блюда
    if (e.target.closest('#add-dish-button')) {
      addDishToOrder();
    }

    // Удаление блюда
    if (e.target.closest('.remove-dish-button')) {
      e.preventDefault();
      e.target.closest('.selected-dish').remove();
      updateTotalAmount();
    }
  });

  // Функция добавления блюда в заказ
  function addDishToOrder() {
    const dishSelect = document.getElementById('dish-select');
    const quantityDisplay = document.getElementById('quantity-display');
    const selectedDishesContainer = document.getElementById('selected-dishes');

    if (!dishSelect.value) return;

    const dishId = dishSelect.value;
    const dishName = dishSelect.options[dishSelect.selectedIndex].text;
    const dishPrice = dishSelect.options[dishSelect.selectedIndex].dataset.price || '0';
    const quantity = quantityDisplay.textContent;

    // Проверяем, не добавлено ли уже это блюдо
    if (document.querySelector(`.selected-dish[data-dish-id="${dishId}"]`)) {
      alert('Это блюдо уже добавлено в заказ');
      return;
    }

    // Создаем элемент для добавленного блюда
    const dishElement = document.createElement('div');
    dishElement.className = 'selected-dish';
    dishElement.dataset.dishId = dishId;
    dishElement.innerHTML = `
            <span>${dishName}</span>
            <span>${quantity} x ${dishPrice}₽</span>
            <input type="hidden" name="dishes[${dishId}][id]" value="${dishId}">
            <input type="hidden" name="dishes[${dishId}][quantity]" value="${quantity}">
            <button type="button" class="remove-dish-button">&times;</button>
        `;

    selectedDishesContainer.appendChild(dishElement);

    // Обновляем общую сумму
    updateTotalAmount();

    // Сбрасываем выбор
    dishSelect.value = '';
    quantityDisplay.textContent = '1';
  }

  // Функция обновления общей суммы
  function updateTotalAmount() {
    const dishes = document.querySelectorAll('.selected-dish');
    let total = 0;

    dishes.forEach(dish => {
      const priceText = dish.querySelector('span:nth-child(2)').textContent;
      const price = parseFloat(priceText.split(' x ')[1]);
      const quantity = parseInt(priceText.split(' x ')[0]);
      total += price * quantity;
    });

    document.getElementById('total-amount').textContent = `${total}₽`;
  }
}

// ИСПРАВЛЕННАЯ ФУНКЦИЯ ОБРАБОТКИ ФОРМ
async function handleFormSubmit(form) {
  try {
    const response = await fetch(form.action, {
      method: form.method,
      body: new FormData(form)
    });

    // Обработка редиректа
    if (response.redirected) {
      window.location.href = response.url;
      return;
    }

    // Обработка JSON-ответов
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || 'Ошибка сервера');
      }

      // Специальная обработка для формы изменения статуса
      if (form.id === 'change-status-form') {
        // Обновляем статус в таблице
        const statusCell = document.querySelector(`.table__cell[data-order-id="${result.order_id}"]`);
        if (statusCell) {
          statusCell.textContent = result.new_status;
        }

        // Закрываем модальное окно
        closeModal();
        return;
      }
    }

    closeModal();
  } catch (error) {
    alert(error.message);
  }
}

function initForms() {
  document.querySelectorAll('form').forEach(form => {
    if (form.dataset.initialized) return;
    form.dataset.initialized = true;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      await handleFormSubmit(form);
    });
  });
}

function initDishesSelect() {
  const select = document.getElementById('dish-select');

  if (select && !select.dataset.initialized) {
    select.dataset.initialized = true;

    const loadDishes = async () => {
      try {
        const response = await fetch('php-modules/get-dishes.php');

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();

        select.innerHTML = '<option value="">Выберите блюдо</option>' +
          data.map(dish =>
            `<option value="${dish.id}" data-price="${dish.price}">${dish.name}</option>`
          ).join('');

      } catch (error) {
        console.error('Ошибка загрузки блюд:', error);
        alert('Не удалось загрузить список блюд');
      }
    };

    loadDishes();
  }
}

function initDynamicComponents() {
  initDishesSelect();
  initOrderForm();
  initForms();

  // Инициализация кнопки закрытия модального окна
  const closeBtn = document.querySelector('.button--secondary');
  if (closeBtn) {
    closeBtn.addEventListener('click', closeModal);
  }

  // Инициализация формы изменения статуса
  const statusForm = document.getElementById('change-status-form');
  if (statusForm && !statusForm.dataset.initialized) {
    statusForm.dataset.initialized = true;

    statusForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      await handleFormSubmit(statusForm);
    });
  }
}

async function loadModalContent(url, refreshCallback = null) {
  try {
    document.getElementById("loading-box").innerHTML = "Загрузка...";
    const response = await fetch(url);

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

    const html = await response.text();
    const modalContent = document.getElementById("modal-window-content");
    modalContent.innerHTML = html;

    initDynamicComponents();

    document.getElementById("loading-box").innerHTML = "";
    modalContent.classList.add("content__modal--active");

    const closeHandler = (e) => {
      if (e.target.closest("#form-outer, .form__outer")) {
        closeModal();
        if (typeof refreshCallback === 'function') {
          refreshCallback();
        }
      }
    };

    modalContent.addEventListener("click", closeHandler);

  } catch (error) {
    console.error("Ошибка загрузки:", error);
    const errorMessage = `<div class="form__error">Ошибка: ${error.message}</div>`;
    document.getElementById("modal-window-content").innerHTML = errorMessage;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-component]").forEach(button => {
    button.addEventListener("click", () => {
      document.querySelectorAll(".header__button").forEach(b =>
        b.classList.remove("header__button--current"));
      button.classList.add("header__button--current");
      loadComponent(button.dataset.component);
    });
  });

  document.getElementById("component-box").addEventListener("click", async (e) => {
    const target = e.target.closest("[data-modal]");
    if (target) {
      e.preventDefault();
      await loadModalContent(
        `${target.dataset.modal}${getQueryParams(target)}`
      );
    }
  });

  function getQueryParams(element) {
    const params = [];
    if (element.dataset.orderId) params.push(`order_id=${element.dataset.orderId}`);
    return params.length ? `?${params.join('&')}` : '';
  }

  loadComponent("components/cooker/cooker-orders.php");
});

const observer = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.addedNodes.length) {
      initDynamicComponents();
    }
  });
});

observer.observe(document, {
  childList: true,
  subtree: true
});

async function loadComponent(url) {
  const componentBox = document.getElementById("component-box");
  try {
    componentBox.classList.add('loading');
    document.getElementById("loading-box").innerHTML = "Загрузка...";

    const response = await fetch(url);
    const html = await response.text();

    componentBox.innerHTML = html;
    componentBox.classList.remove('loading');

    document.getElementById("loading-box").innerHTML = "";
    initDynamicComponents();
  } catch (error) {
    componentBox.classList.remove('loading');
    console.error("Ошибка загрузки:", error);
  }
}