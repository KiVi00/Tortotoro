"use strict";

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

      if (target.closest("#add-button")) {
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

          document.getElementById("loading-box").innerHTML = "";
          document.getElementById("registration-form").innerHTML = html;

          document.body.style.overflowY = "hidden";

          document
            .getElementById("form-outer")
            .addEventListener("click", () => {
              document.getElementById("registration-form").innerHTML = "";
              document.body.style.overflowY = "scroll";
            });
        } catch (error) {
          console.error("Ошибка загрузки:", error);
        }
      }

      if (target.closest("#settings-button")) {
      }
    });
}

document.addEventListener("DOMContentLoaded", () => {
  initDynamicHandlers();
});
