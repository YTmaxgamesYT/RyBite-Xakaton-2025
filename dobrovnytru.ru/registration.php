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
    $login = sanitize_input($_POST['login']);
    $password = sanitize_input($_POST['password']);
    $email = sanitize_input($_POST['email']);

    // Проверка, что все поля заполнены
    if (empty($login) || empty($password) || empty($email)) {
        $error_message = "Все поля обязательны для заполнения.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Некорректный формат email.";
    } elseif (strlen($password) < 8) {
        $error_message = "Пароль должен содержать минимум 8 символов.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error_message = "Пароль должен содержать хотя бы одну большую букву.";
    } else {
        // Проверка существования записи с введенным логином или email
        try {
            $check_sql = "SELECT COUNT(*) FROM users WHERE login = :login OR email = :email";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->bindParam(':login', $login);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            $count = $check_stmt->fetchColumn();

            if ($count > 0) {
                $error_message = "Пользователь с таким логином или email уже существует.";
            } else {
                // Хеширование пароля
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Подготовка SQL-запроса для вставки данных
                $sql = "INSERT INTO users (login, password, email, post) VALUES (:login, :password, :email, NULL)";
                $stmt = $pdo->prepare($sql);

                // Привязка параметров
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':email', $email);
                
                session_start();
                
                	
                $_SESSION["user_login"] = $login;

                // Выполнение запроса
                if ($stmt->execute()) {
                    // Перенаправление на страницу index.php после успешной регистрации
                    header("Location: main.php");
                    exit();
                } else {
                    $error_message = "Ошибка при регистрации.";
                }
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
    <title>Регистрация</title>
</head>
<style>
        :root {
	        --index: calc(1vw + 1vh);
            
        }
        .Register{
            position:fixed;
            display:flex;
            justify-content:center;
            width:100vw;
            height:100vh;
            z-index:8;
            background-color:#fff;
            color:#3e3e3e;
        }
        .RegisterContainer{
            display:flex;
            flex-direction:column;
            width:55vw;
            height:55vh;
            margin:auto;
            background-color:#fff;
            border: 0.1em solid transparent;
            border-color: #3e3e3e;
            border-radius:calc(1.5vw + 1.5vh);
        }
        .RegisterName{
            font: clamp(1.4em, 1.5vh + 1vw + 1em, 2.8em) Matter, sans-serif;
            margin-top:calc(var(--index));
            margin-bottom:calc(var(--index));
            margin-right:auto;
            margin-left:auto;
            font-weight:650;
        }
        .inp {
            background: #fff;
            padding: 0 1.6rem;
            width: 40vw;
            height: calc(var(--index)*2);
            border: 0.1em solid transparent;
            border-color: #3e3e3e;
            border-radius:calc(1.5vw + 1.5vh);
            font: clamp(0.5em, var(--index), 1em) Arial, sans-serif;
            margin-right:auto;
            margin-left:auto;
        }
        .subi {
            font: clamp(0.6em, var(--index)*1.2, 1.2em) Arial, sans-serif;
            color:#222222;
            background: #fff;
            padding: 0 1.6rem;
            margin:auto;
            margin-top:calc(var(--index)*.6);;
            height: calc(var(--index)*2);
            border: 0.1em solid transparent;
            border-color: #3e3e3e;
            border-radius:calc(1.5vw + 1.5vh);
        }
        .RegisterInput{
            display:flex;
            justify-content:center;
            margin:calc(var(--index)*.4);
        }
    </style>
<body>
    <div class="Register">
        <div class="RegisterContainer">
            <div class="RegisterName">Регистрация</div>
            <?php if (!empty($error_message)): ?>
                <div style="color: red; text-align: center;"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
                <form class ="" method="post" action="registration.php">
                <!--<label for="email">Email:</label><br>-->
                <div class="RegisterInput"><input type="email" class="inp" id="email" name="email" placeholder="Введите почту" required value="<?php echo htmlspecialchars(isset($email) ? $email : '', ENT_QUOTES, 'UTF-8'); ?>"><br><br>
                </div>
                <!--<label for="login">Логин:</label><br>-->
                <div class="RegisterInput"><input type="text" class="inp" id="login"  name="login" placeholder="Придумайте логин" required value="<?php echo htmlspecialchars(isset($login) ? $login : '', ENT_QUOTES, 'UTF-8'); ?>"><br><br>
                </div>
                <!--<label for="password">Пароль:</label><br>-->
                <div class="RegisterInput"><input type="password" class="inp" id="password" placeholder="Придумайте пароль" name="password" required><br><br>
                </div>
                <div class="RegisterInput"><input type="submit" class="subi" value="Зарегистрироваться"></div>
            </form>
        </div>
    </div>
    
</body>
</html>
