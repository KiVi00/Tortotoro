<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'tortotoro';

try {
  $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo 'Подключение выполнено успешно';
} catch(PDOException $e) {
  echo 'Ошибка подключения: ' . $e->getMessage();
}
?>