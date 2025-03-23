<?php
// Включаем буферизацию вывода
ob_start();

// Начало сессии
session_start();

// Подключение к базе данных
$host = 'localhost';
$dbname = 'u2931124_default';
$username = 'u2931124_default';
$password = 'XDlYkr74kOU823LK';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем Telegram ID из параметра URL
$tg_id = isset($_GET['tg_id']) ? (int)$_GET['tg_id'] : null;

if ($tg_id) {
    // Поиск пользователя в базе данных по Telegram ID
    $stmt = $pdo->prepare("SELECT * FROM users WHERE tg_id = :tg_id");
    $stmt->execute(['tg_id' => $tg_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Сохраняем Telegram ID в сессии
        $_SESSION['USER_TG_ID'] = $tg_id;

        // Проверяем роль пользователя
        if ($user['post'] === 'admin') {
            // Роль admin
            $_SESSION['user_login'] = $user['login']; // Сохраняем логин в сессии
            header("Location: main.php");
            exit();
        } elseif ($user['post'] === 'worker') {
            // Роль worker
            header("Location: mobile_index.php");
            exit();
        } elseif ($user['post'] === null) {
            // Роль не определена (post = null)
            header("Location: autorization.php");
            exit();
        }
    } else {
        // Пользователь не найден, перенаправляем на страницу авторизации
        header("Location: autorization.php");
        exit();
    }
} else {
    // tg_id не передан, перенаправляем на страницу авторизации
    header("Location: autorization.php");
    exit();
}

// Очищаем буфер вывода
ob_end_flush();
?>