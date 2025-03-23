<?php
// Начало сессии
session_start();

// Удаление всех данных сессии
session_unset();

// Уничтожение сессии
session_destroy();

// Перенаправление на главную страницу
header("Location: main.php");
exit();
?>