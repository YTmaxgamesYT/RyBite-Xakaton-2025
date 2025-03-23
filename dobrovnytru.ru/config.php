<?php

$host = 'localhost'; 
$dbname = 'u2931124_default'; 
$username = 'u2931124_default';
$password = 'XDlYkr74kOU823LK';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//    echo "Подключение к базе данных успешно!";
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?>