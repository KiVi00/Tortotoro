<?php
$title = "Tortotoro: Админ-панель";
session_start();
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
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/Tortotoro/components/admin/admin-header.php' ?>
  <main class="content">
    <?php if (isset($_SESSION['shift_error'])): ?>
      <div class="form__error"><?= htmlspecialchars($_SESSION['shift_error']) ?></div>
      <?php unset($_SESSION['shift_error']); endif; ?>
    <?php if (isset($_SESSION['reg_error'])): ?>
      <div class="form__error"><?= htmlspecialchars($_SESSION['reg_error']) ?></div>
      <?php unset($_SESSION['reg_error']); endif; ?>
    <div class="content__loading" id="loading-box"></div>
    <div class="content__modal content__modal--inactive" id="modal-window-content"></div>
    <div class="content__inner content__inner--filled container" id="component-box"></div>
  </main>
  <script src="components/admin/ajax-admin-buttons.js"></script>
</body>

</html>