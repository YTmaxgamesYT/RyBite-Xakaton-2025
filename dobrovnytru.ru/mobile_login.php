<?php
// Начало сессии
session_start();

// Подключение к базе данных
$host = 'localhost'; // Хост базы данных
$dbname = 'u2931124_default'; // Имя базы данных
$username = 'u2931124_default'; // Имя пользователя базы данных
$password = 'XDlYkr74kOU823LK'; // Пароль базы данных

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Обработка формы авторизации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginOrEmail = $_POST['login_or_email'];
    $password = $_POST['password'];

    // Поиск пользователя в базе данных по логину или почте
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :loginOrEmail OR email = :loginOrEmail");
    $stmt->execute(['loginOrEmail' => $loginOrEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Пользователь найден, проверяем роль
        if ($user['post'] === 'admin') {
            // Роль admin
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_login'] = $user['login']; // Сохраняем логин в сессии
            header("Location: main.php");
            exit();
        } elseif ($user['post'] === 'worker') {
            // Роль worker
            $_SESSION['user_role'] = 'worker';
            $_SESSION['user_login'] = $user['login']; // Сохраняем логин в сессии
            header("Location: mobile_index.php");
            exit();
        }
    } else {
        // Неверный логин, почта или пароль
        echo "Неверный логин, почта или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .login-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-form input {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Авторизация</h2>
        <form method="POST" action="">
            <input type="text" name="login_or_email" placeholder="Логин или почта" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>