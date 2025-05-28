"use strict";

function closeModal() {
    const modalContent = document.getElementById("modal-window-content");
    if (!modalContent) return;

    modalContent.classList.remove("content__modal--active");
    document.body.style.overflowY = "scroll";

    setTimeout(() => {
        modalContent.innerHTML = "";
    }, 200);
}

document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'add-worker') {
        const select = document.getElementById('select-worker');
        if (!select) return;

        const workerId = select.value;
        const workerName = select.options[select.selectedIndex].text;

        if (document.querySelector(`input[name="workers[]"][value="${workerId}"]`)) return;

        const div = document.createElement('div');
        div.className = 'worker-tag';
        div.innerHTML = `
            ${workerName}
            <input type="hidden" name="workers[]" value="${workerId}">
            <button type="button" class="remove-worker">&times;</button>
        `;

        const container = document.getElementById('current-workers');
        if (container) container.appendChild(div);
    }

    if (e.target.classList.contains('remove-worker')) {
        const tag = e.target.closest('.worker-tag');
        if (tag) tag.remove();
    }
});

const loadComponent = async (url) => {
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
};

// ИСПРАВЛЕННАЯ ФУНКЦИЯ ОБРАБОТКИ ФОРМ
async function handleFormSubmit(form) {
    try {
        const response = await fetch(form.action, {
            method: 'POST',
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
            if (!response.ok) throw new Error(result.error || 'Ошибка сервера');
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

function initDynamicComponents() {
    initImagePreview();
    initWorkersSelect();
    initForms();
}

function initImagePreview() {
    const fileInput = document.getElementById('file-input');
    const preview = document.getElementById('photo-preview');

    if (fileInput && preview && !fileInput.dataset.listenerAdded) {
        fileInput.dataset.listenerAdded = 'true';

        fileInput.addEventListener('change', function (e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.onerror = function () {
                    console.error('Ошибка загрузки изображения');
                    preview.src = 'assets/images/default.jpg';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = 'assets/images/default.jpg';
            }
        });
    }
}

function initWorkersSelect() {
    const select = document.getElementById('select-worker');

    if (select && !select.dataset.initialized) {
        select.dataset.initialized = true;

        const loadWorkers = async () => {
            try {
                const response = await fetch('php-modules/get-workers.php');

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();

                select.innerHTML = data.map(worker =>
                    `<option value="${worker.id}">${worker.last_name} ${worker.first_name}</option>`
                ).join('');

            } catch (error) {
                console.error('Ошибка загрузки сотрудников:', error);
                alert('Не удалось загрузить список сотрудников');
            }
        };

        loadWorkers();
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
        if (element.dataset.shiftId) params.push(`shift_id=${element.dataset.shiftId}`);
        if (element.dataset.workerId) params.push(`worker_id=${element.dataset.workerId}`);
        return params.length ? `?${params.join('&')}` : '';
    }

    loadComponent("components/admin/shifts.php");
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