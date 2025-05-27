"use strict";

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

async function refreshWorkersTable() {
    await loadComponent('components/admin/workers.php');
}

async function loadModalContent(url) {
    try {
        document.getElementById("loading-box").innerHTML = "Загрузка...";
        const response = await fetch(url);

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const html = await response.text();

        document.getElementById("modal-window-content").innerHTML = html;
        initImagePreview();
        initWorkersSelect();
        document.getElementById("loading-box").innerHTML = "";
        document.body.style.overflowY = "hidden";

        const modal = document.getElementById("modal-window-content");
        modal.classList.add("content__modal--active");

        document.getElementById("form-outer").addEventListener("click", () => {
            modal.classList.remove("content__modal--active");
            document.body.style.overflowY = "scroll";
            setTimeout(() => {
                modal.innerHTML = "";
                refreshWorkersTable();
            }, 200);
        });
    } catch (error) {
        console.error("Ошибка загрузки:", error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const loadComponent = async (url) => {
        try {
            document.getElementById("loading-box").innerHTML = "Загрузка...";
            const response = await fetch(url);
            const html = await response.text();
            document.getElementById("component-box").innerHTML = html;
            document.getElementById("loading-box").innerHTML = "";
        } catch (error) {
            console.error("Ошибка загрузки:", error);
        }
    };

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
            const workerId = target.dataset.workerId;
            await loadModalContent(`${target.dataset.modal}?worker_id=${workerId}`);
        }
    });



    loadComponent("components/admin/shifts.php");
});

const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
            initImagePreview();
            initWorkersSelect();
        }
    });
});

observer.observe(document, {
    childList: true,
    subtree: true
});