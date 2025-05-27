<?php
session_start();

$_SESSION = [];
session_destroy();

header('Location: /Tortotoro/index.php');
exit;