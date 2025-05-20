<?php
$title = "Tortotoro";
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
  <main class="content">
    <div class="content__container container">
      <?php require_once 'components/forms/authorization-form/authorization-form.php' ?>
    </div>
  </main>
</body>
</html>