<?php
// Включение буферизации вывода
ob_start();

// Подключение к базе данных
require 'config.php';

// Функция для очистки ввода
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из POST-запроса
    $email = sanitize_input($_POST['email']); // Поле для email
    $password = sanitize_input($_POST['password']);

    // Проверка, что все поля заполнены
    if (empty($email) || empty($password)) {
        $error_message = "Все поля обязательны для заполнения.";
    } else {
        // Проверка существования записи с введенным email
        try {
            $check_sql = "SELECT * FROM users WHERE email = :email";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            $user = $check_stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Пароль верный, начинаем сессию
                session_start();
                $_SESSION['user_login'] = $user['login'];

                // Перенаправление на страницу main.php после успешной авторизации
                header("Location: main.php");
                exit();
            } else {
                $error_message = "Неверный email или пароль.";
            }
        } catch (PDOException $e) {
            $error_message = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}

// Очистка буфера вывода
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<style>
    :root {
        --index: calc(1vw + 1vh);
    }
    .Authorization {
        position: fixed;
        display: flex;
        justify-content: center;
        width: 100vw;
        height: 100vh;
        z-index: 8;
        background-color: #fff;
        color: #3e3e3e;
    }
    .AuthorizationContainer {
        display: flex;
        flex-direction: column;
        width: 55vw;
        height: 55vh;
        margin: auto;
        background-color: #fff;
        border: 0.1em solid transparent;
        border-color: #3e3e3e;
        border-radius: calc(1.5vw + 1.5vh);
    }
    .AuthorizationName {
        font: clamp(1.4em, 1.5vh + 1vw + 1em, 2.8em) Matter, sans-serif;
        margin-top: calc(var(--index));
        margin-bottom: calc(var(--index));
        margin-right: auto;
        margin-left: auto;
        font-weight: 650;
    }
    .inp {
        background: #fff;
        padding: 0 1.6rem;
        width: 40vw;
        height: calc(var(--index) * 2);
        border: 0.1em solid transparent;
        border-color: #3e3e3e;
        border-radius: calc(1.5vw + 1.5vh);
        font: clamp(0.5em, var(--index), 1em) Arial, sans-serif;
        margin-right: auto;
        margin-left: auto;
    }
    .subi {
        font: clamp(0.6em, var(--index) * 1.2, 1.2em) Arial, sans-serif;
        color: #222222;
        background: #fff;
        padding: 0 1.6rem;
        margin: auto;
        margin-top: calc(var(--index) * 0.6);
        height: calc(var(--index) * 2);
        border: 0.1em solid transparent;
        border-color: #3e3e3e;
        border-radius: calc(1.5vw + 1.5vh);
    }
    .AuthorizationInput {
        display: flex;
        justify-content: center;
        margin: calc(var(--index) * 0.4);
    }
</style>
<body>
    <div class="Authorization">
        <div class="AuthorizationContainer">
            <div class="AuthorizationName">Авторизация</div>
            <?php if (!empty($error_message)): ?>
                <div style="color: red; text-align: center"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="autorization.php">
                <div class="AuthorizationInput">
                    <input placeholder="Введите email" type="email" id="email" class="inp" name="email" required value="<?php echo htmlspecialchars(isset($email) ? $email : '', ENT_QUOTES, 'UTF-8'); ?>"><br><br>
                </div>
                <div class="AuthorizationInput">
                    <input placeholder="Введите пароль" type="password" class="inp" id="password" name="password" required><br><br>
                </div>
                <div class="AuthorizationInput">
                    <input type="submit" class="subi" value="Войти">
                </div>
            </form>
        </div>
    </div>
</body>
</html>