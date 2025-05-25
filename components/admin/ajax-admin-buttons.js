"use strict";

const headerButtons = document.querySelectorAll(".header__button");

headerButtons.forEach(element => {
  element.addEventListener("click", () => {
    headerButtons.forEach(button => button.classList.remove("header__button--current"));
    element.classList.add("header__button--current");
  })
});

async function loadComponent(url, containerId = "component-box") {
  try {
    document.getElementById("loading-box").innerHTML = "Загрузка...";
    const response = await fetch(url, {
      method: "GET",
      headers: { "Content-Type": "text/html" },
    });

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

    const html = await response.text();
    document.getElementById(containerId).innerHTML = html;
    document.getElementById("loading-box").innerHTML = "";

    initDynamicHandlers();
  } catch (error) {
    console.error("Ошибка загрузки:", error);
  }
}

document.addEventListener("DOMContentLoaded", () =>
  loadComponent("components/admin/shifts.php")
);

document
  .getElementById("workers")
  .addEventListener("click", () =>
    loadComponent("components/admin/workers.php")
  );

document
  .getElementById("shifts")
  .addEventListener("click", () =>
    loadComponent("components/admin/shifts.php")
  );

function initDynamicHandlers() {
  document
    .getElementById("component-box")
    .addEventListener("click", async (e) => {
      const target = e.target;

      if (target.closest("#add-worker-button")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch(
            "components/admin/registration-form.php",
            {
              method: "GET",
              headers: {
                "Content-Type": "text/html",
              },
            }
          );

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }

      if (target.closest("#add-shift-button")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch("components/admin/add-shift-form.php", {
            method: "GET",
            headers: {
              "Content-Type": "text/html",
            },
          });

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
      if (target.closest("#workers-2")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch("components/admin/edit-shift-form.php", {
            method: "GET",
            headers: {
              "Content-Type": "text/html",
            },
          });

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
      if (target.closest("#status-2")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch(
            "components/admin/close-shift-confirm.php",
            {
              method: "GET",
              headers: {
                "Content-Type": "text/html",
              },
            }
          );

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
      if (target.closest("#shift-2")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch("components/admin/view-shift-form.php", {
            method: "GET",
            headers: {
              "Content-Type": "text/html",
            },
          });

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
      if (target.closest("#view-shifts")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch(
            "components/admin/view-worker's-shift.php",
            {
              method: "GET",
              headers: {
                "Content-Type": "text/html",
              },
            }
          );

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
      if (target.closest("#worker-status")) {
        try {
          document.getElementById("loading-box").innerHTML = "Загрузка...";
          const response = await fetch(
            "components/admin/dismiss-worker-form.php",
            {
              method: "GET",
              headers: {
                "Content-Type": "text/html",
              },
            }
          );

          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

          const html = await response.text();

          document.getElementById("modal-window-content").innerHTML = html;
          document.getElementById("loading-box").innerHTML = "";
          document.body.style.overflowY = "hidden";

          document
            .getElementById("modal-window-content")
            .classList.add("content__modal--active");

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document
                .querySelector(".content__modal")
                .classList.remove("content__modal--active");
              document.body.style.overflowY = "scroll";
              setTimeout(() => {
                document.getElementById("modal-window-content").innerHTML = "";
              }, 200);
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }
    });
}

document.addEventListener("DOMContentLoaded", () => {
  initDynamicHandlers();
});
