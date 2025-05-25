<?php
$title = "Tortotoro: Официант";
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="assets/styles/main.css" />
  </head>

  <body class="page">
    <?php require $_SERVER['DOCUMENT_ROOT'].'/Tortotoro/components/cooker/cooker-header.php' ?>
    <main class="content">
      <div class="content__loading" id="loading-box"></div>
      <div
        class="content__modal content__modal--inactive"
        id="modal-window-content"
      ></div>
      <div
        class="content__inner content__inner--filled container"
        id="component-box"
      ></div>
    </main>
    <script src="components/cooker/ajax-cooker-buttons.js"></script>
  </body>
</html>
