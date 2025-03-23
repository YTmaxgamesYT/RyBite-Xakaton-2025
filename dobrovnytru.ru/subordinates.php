<?php
// Подключение к базе данных
$connect = mysqli_connect("localhost", "u2931124_default", "XDlYkr74kOU823LK", "u2931124_default") or die("Ошибка подключения к базе данных");
mysqli_set_charset($connect, "utf8");

// Начало сессии
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_login'])) {
    // Если пользователь не авторизован, перенаправляем на autorization.php
    header("Location: autorization.php");
    exit(); // Завершаем выполнение скрипта
}

// Если пользователь авторизован, продолжаем выполнение скрипта
$is_logged_in = true;

// Обработка добавления сотрудника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addEmployee'])) {
    $fullName = mysqli_real_escape_string($connect, $_POST['fullName']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = password_hash(mysqli_real_escape_string($connect, $_POST['password']), PASSWORD_DEFAULT); // Хешируем пароль
    $telegramId = mysqli_real_escape_string($connect, $_POST['telegramId']);
    $role = mysqli_real_escape_string($connect, $_POST['role']);

    // Определяем значение post
    $post = ($role === "Сотрудник") ? "worker" : "admin";

    if ($fullName && $email && $password && $telegramId && $role) {
        // Вставка данных в таблицу users
        $sql = "INSERT INTO users (login, full_name, email, password, tg_id, post) 
                VALUES (NULL, '$fullName', '$email', '$password', '$telegramId', '$post')";
        if (mysqli_query($connect, $sql)) {
            // Перенаправляем на страницу subordinates.php
            header("Location: subordinates.php");
            exit(); // Завершаем выполнение скрипта
        } else {
            echo "<script>alert('Ошибка при добавлении сотрудника: " . mysqli_error($connect) . "');</script>";
        }
    } else {
        echo "<script>alert('Заполните все поля!');</script>";
    }
}

// Обработка удаления сотрудника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['removeEmployee'])) {
    $fullNameToRemove = mysqli_real_escape_string($connect, $_POST['fullNameToRemove']);

    if ($fullNameToRemove) {
        // Удаление сотрудника из таблицы users по full_name
        $sql = "DELETE FROM users WHERE full_name = '$fullNameToRemove'";
        if (mysqli_query($connect, $sql)) {
            // Перенаправляем на страницу subordinates.php
            header("Location: subordinates.php");
            exit(); // Завершаем выполнение скрипта
        } else {
            echo "<script>alert('Ошибка при удалении сотрудника: " . mysqli_error($connect) . "');</script>";
        }
    } else {
        echo "<script>alert('Выберите ФИО сотрудника!');</script>";
    }
}

// Получаем данные из таблицы users, где full_name не равно NULL
$sql = "SELECT full_name, email, post FROM users WHERE full_name IS NOT NULL";
$result = mysqli_query($connect, $sql);

$employees = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }
} else {
    echo "<script>alert('Ошибка при получении данных из базы данных: " . mysqli_error($connect) . "');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employ Moni</title>
    <style>
        /* Ваши стили */
        :root {
            --index: calc(1vw + 1vh);
            --rad: 0.55rem;
            --dur: 0.3s;
            --color-dark: #2f2f2f;
            --color-light: #fff;
            --color-brand: #57bd84;
            --font-fam: 'Lato', sans-serif;
            --height: 5rem;
            --btn-width: 6rem;
        }
        body::-webkit-scrollbar {
            width: 10px;
        }
        body::-webkit-scrollbar-track {
            background: rgba(0,0,0,1);
        }
        body::-webkit-scrollbar-thumb {
            background-color: #3e3e3e;
            border-radius: 20px;
        }
        .Full {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            width: 96vw;
            margin-right: auto;
            margin-left: auto;
            background-color: #fff;
            border-radius: var(--rad);
            padding-bottom:calc(var(--index)*2);
        }
        .cont1 {
            padding-top: calc(var(--index) * 4.5);
        }
        body {
            font: Matter, sans-serif;
            margin: 0;
            overflow-x: hidden;
            user-select: none;
            overscroll-behavior: none;
            background-color: #f0f0f0;
        }
        .inp {
            background: var(--color-light);
            padding: 0 1.6rem;
            margin: calc(var(--index) * 0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            width: 30vw;
            height: calc(var(--index) * 2);
            border: 1px solid;
            border-color: #272727;
            font: clamp(0.5em, var(--index), 1em) Arial, sans-serif;
        }
        .subi {
            font: clamp(0.6em, var(--index) * 1.2, 1.2em) Arial, sans-serif;
            color: #222222;
            background: var(--color-light);
            padding: 0 1.6rem;
            margin: calc(var(--index) * 0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            width: 12.5vw;
            height: calc(var(--index) * 2);
            border: 1px solid;
            border-color: #272727;
        }
        .forma {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            margin-bottom: (var(--index) * 0.5);
        }
        .btn-menu {
            position: relative;
            width: 40px;
            height: 24px;
            cursor: pointer;
            margin-left: var(--index);
            margin-right: var(--index);
        }
        .btn-menu i {
            position: absolute;
            top: 50%;
            left: 0;
            display: block;
            width: 40px;
            height: 4px;
            margin-top: -2px;
            -webkit-transition-timing-function: cubic-bezier(.55, .055, .675, .19);
            transition-timing-function: cubic-bezier(.55, .055, .675, .19);
            -webkit-transition-duration: 75ms;
            transition-duration: 75ms;
            background-color: #e3e3e3;
            border-radius: calc(1vw + 1vh);
        }
        .btn-menu i:before, .btn-menu i:after {
            position: relative;
            display: block;
            width: 40px;
            height: 4px;
            content: '';
            -webkit-transition: top 75ms ease .12s, opacity 75ms ease;
            transition: top 75ms ease .12s, opacity 75ms ease;
            -webkit-transition-timing-function: ease;
            transition-timing-function: ease;
            -webkit-transition-duration: .15s;
            transition-duration: .15s;
            -webkit-transition-property: -webkit-transform;
            transition-property: -webkit-transform;
            transition-property: transform;
            transition-property: transform, -webkit-transform;
            background-color: #e3e3e3;
            border-radius: calc(1vw + 1vh);
        }
        .btn-menu i:before {
            top: -10px;
        }
        .btn-menu i:after {
            bottom: -6px;
        }
        .btn-menu.is-active i {
            -webkit-transition-delay: .12s;
            transition-delay: .12s;
            -webkit-transition-timing-function: cubic-bezier(.215, .61, .355, 1);
            transition-timing-function: cubic-bezier(.215, .61, .355, 1);
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }
        .btn-menu.is-active i:before {
            top: 0;
            -webkit-transition: top 75ms ease, opacity 75ms ease .12s;
            transition: top 75ms ease, opacity 75ms ease .12s;
            opacity: 0;
        }
        .btn-menu.is-active i:after {
            bottom: 4px;
            -webkit-transition: bottom 75ms ease, -webkit-transform 75ms cubic-bezier(.215, .61, .355, 1) .12s;
            transition: bottom 75ms ease, -webkit-transform 75ms cubic-bezier(.215, .61, .355, 1) .12s;
            transition: bottom 75ms ease, transform 75ms cubic-bezier(.215, .61, .355, 1) .12s;
            transition: bottom 75ms ease, transform 75ms cubic-bezier(.215, .61, .355, 1) .12s, -webkit-transform 75ms cubic-bezier(.215, .61, .355, 1) .12s;
            -webkit-transform: rotate(-90deg);
            transform: rotate(-90deg);
        }
        .Menu {
            display: flex;
            flex-direction: column;
            position: fixed;
            width: calc(var(--index) * 20);
            height: 100vw;
            background-color: #3e3e3e;
            transform: translateX(calc(var(--index) * (-20)));
            transition: 0s;
            z-index: 9;
            padding-top: calc(var(--index) * 4);
            margin-left: auto;
        }
        .Menu button {
            background-color: rgba(0,0,0,0);
            border: 0;
            border-color: #e3e3e3;
            color: #e3e3e3;
            padding-top: calc(var(--index) * .25);
            padding-left: calc(var(--index) * 1.5);
            font: clamp(1em, 1.7vw + 1em, 2em) Matter, sans-serif;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
        }
        .BlurMenu {
            position: fixed;
            background-color: rgba(0,0,0,0);
            z-index: -8;
            width: 100vw;
            height: 100vh;
            transition: 0.25s;
        }
        .Header {
            width: 100vw;
            height: calc(var(--index) * 3);
            background-color: #3e3e3e;
            color: #e3e3e3;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            border-bottom-left-radius: .85em;
            border-bottom-right-radius: .85em;
            position: fixed;
            z-index: 10;
        }
        .HalfHeader {
            display: flex;
            flex-direction: row;
            margin: auto;
            flex-basis: 33.33%;
            font: clamp(.6em, .8vw + .6em, 1.2em) Matter, sans-serif;
            font-weight: 600;
        }
        .HalfHeaderItem {
            padding-left: calc(1vw + 1vh);
            padding-right: calc(1vw + 1vh);
            color: #FFA632;
            scale: 1.4;
            Letter-spacing: .05em;
        }
        .HalfHeaderButton {
            margin-left: calc(1vw + 1vh);
            margin-right: calc(1vw + 1vh);
            padding-top: calc(.3vw + .3vh);
            padding-bottom: calc(.3vw + .3vh);
            padding-left: calc(1vw + 1vh);
            padding-right: calc(1vw + 1vh);
            border: 0.1em solid transparent;
            border-color: #e3e3e3;
            border-radius: calc(1vw + 1vh);
            background-color: rgba(0,0,0,0);
            color: #e3e3e3;
            font: clamp(.5em, .7vw + .5em, 1em) Matter, sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: 0.25s;
        }
        .HalfHeaderButton:hover {
            transform: scale(1.05);
        }
        /* Стили для модального окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            width: 30vw;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }
        .modal-content input, .modal-content select {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 0.15em solid #7a7a7a;
            border-radius: 10px;
            margin-right:auto;
            margin-left:auto;
        }
        .modal-content button {
            padding: 8px 16px;
            background-color: #FFA632;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .modalForm{
            display:flex;
            flex-direction:column;
        }
        .DisplayTable{ 
            width:100%;
            border-collapse: collapse;
            margin-bottom: auto;
            margin-right: calc(var(--index)*2);
            margin-left: calc(var(--index)*2);
            margin-top: calc(var(--index)*2);
        }
    </style>
</head>
<body>
    <div class="Header">
        <div class="HalfHeader" style="justify-content:left;">
            <div class="btn-menu"><i></i></div>
        </div>
        <div class="HalfHeader" style="justify-content:center;">
            <div class="HalfHeaderItem">Employ Moni</div>
        </div>
        <?php if (!($is_logged_in)): ?>
            <div class="HalfHeader" style="justify-content:right;">
                <button class="HalfHeaderButton" id="enterbtn" onClick="window.location.href='autorization.php'">Вход</button>
                <button class="HalfHeaderButton" id="enterbtn" onClick="window.location.href='registration.php'">Регистрация</button>
            </div>
        <?php else: ?>
            <div class="HalfHeader" style="justify-content:right;">
                <button class="HalfHeaderButton" id="enterbtn" onClick="window.location.href='main.php'">Главная</button>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="Menu">
        <button id="MoiProfil" onClick="window.location.href='profile.php'">Профиль</button>
        <button id="Subordinates" onClick="window.location.href='main.php'">Главная</button>
        <button id="DateTable" onClick="window.location.href='allFilesTable.php'">Все отчёты</button>
        <button id="Alerts">Уведомления</button>
        <br>
        <button id="addSubordinateBtn">Добавить сотрудника</button>
        <button id="removeSubordinateBtn">Удалить сотрудника</button>
    </div>
    <div class="BlurMenu"></div>
    
    <div id="addSubordinateModal" class="modal">
        <div class="modal-content">
            <div style="font: clamp(1em, 1vw + 1em, 1.5em) Matter, sans-serif; text-align:center; font-weight: 600; color:#5a5a5a; padding-bottom: 10px;">Добавить сотрудника</div>
            <form method="POST" action="" class="modalForm">
                <input  type="text" id="fullName" name="fullName" placeholder="ФИО" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Пароль" required>
                <input type="text" id="telegramId" name="telegramId" placeholder="Telegram ID" required>
                <select id="role" name="role" required>
                    <option value="Начальник">Начальник</option>
                    <option value="Администратор">Администратор</option>
                    <option value="Сотрудник">Сотрудник</option>
                </select>
                <button type="submit" name="addEmployee" style="width:33%;margin: 8px 0; margin-right:auto; margin-left:auto">Добавить</button>
            </form>
        </div>
    </div>
    
    <div id="removeSubordinateModal" class="modal">
        <div class="modal-content">
            <div style="font: clamp(1em, 1vw + 1em, 1.5em) Matter, sans-serif; text-align:center; font-weight: 600; color:#5a5a5a; padding-bottom: 10px;">Удалить сотрудника</div>
            <form method="POST" action="" class="modalForm">
                <select id="fullNameToRemove" name="fullNameToRemove" required>
                    <option value="" disabled selected>Выберите ФИО сотрудника</option>
                    <?php
                    // Запрос всех full_name из базы данных
                    $sql = "SELECT full_name FROM users WHERE full_name IS NOT NULL";
                    $result = mysqli_query($connect, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['full_name']}'>{$row['full_name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="removeEmployee" style="width:33%; margin: 8px 0; margin-right:auto; margin-left:auto">Удалить</button>
            </form>
        </div>
    </div>
        
    <div class="cont1">
    <div class="Full">
        <table class="DisplayTable">
            <thead>
                <tr style="background-color: #3e3e3e; color: #fff;">
                    <th style="padding: 10px; border: 1px solid #ccc;">ФИО</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Почта</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Должность</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr style="background-color: #fff; color: #000;">
                            <td style="padding: 10px; border: 1px solid #ccc;"><?php echo htmlspecialchars($employee['full_name']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ccc;"><?php echo htmlspecialchars($employee['email']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ccc;">
                                <?php
                                // Преобразуем post в читаемый формат
                                if ($employee['post'] === 'worker') {
                                    echo 'Сотрудник';
                                } elseif ($employee['post'] === 'admin') {
                                    echo 'Администратор/Начальник';
                                } else {
                                    echo 'Начальник';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 10px;">Нет данных для отображения.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@latest/bundled/lenis.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
    <script>
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        })
        function raf(time) {
          lenis.raf(time)
          requestAnimationFrame(raf)
        }
        
        requestAnimationFrame(raf)
    </script>
    
    <script>
// Открытие модального окна для удаления сотрудника
$('#removeSubordinateBtn').on('click', function() {
    $('#removeSubordinateModal').css('display', 'flex');
});

// Закрытие модального окна при клике вне его
$(window).on('click', function(event) {
    if ($(event.target).is('.modal')) {
        $('#removeSubordinateModal').css('display', 'none');
    }
});
    </script>

    <script>
        var schet = 0;
        $(document).ready(function() {
            "use strict";
            $('.btn-menu').on('click', function(e) {
                $(this).toggleClass('is-active');
                schet=schet+1;
                if(schet%2==1){
                    $('.Menu').css('transition','0.25s');
                    $('.Menu').css('transform','translateX(0vw)');
                    $('.BlurMenu').css('background-color','rgba(0,0,0,0.5)');
                    $('.BlurMenu').css('z-index','8');
                    setTimeout(function() {
                        $('.Menu').css('transition','0s');
                    }, (250));
                    
                }
                else{
                    $('.Menu').css('transform','translateX(calc(var(--index)*(-20)))');
                    $('.Menu').css('transition','0.25s');
                    $('.BlurMenu').css('background-color','rgba(0,0,0,0)');
                    $('.BlurMenu').css('z-index','-8');
                }
            });

            // Открытие модального окна
            $('#addSubordinateBtn').on('click', function() {
                $('#addSubordinateModal').css('display', 'flex');
            });

            // Закрытие модального окна при клике вне его
            $(window).on('click', function(event) {
                if ($(event.target).is('.modal')) {
                    $('#addSubordinateModal').css('display', 'none');
                }
            });
        });
    </script>
</body>
</html>