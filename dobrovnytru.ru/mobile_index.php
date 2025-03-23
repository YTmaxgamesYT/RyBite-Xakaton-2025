<?php
session_start();

$servername = 'localhost';
$username = 'u2931124_default';
$password = 'XDlYkr74kOU823LK';
$dbname = 'u2931124_default';
$connect = new mysqli($servername, $username, $password, $dbname);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$USER_TG_ID = $_SESSION['USER_TG_ID'];
echo "ID: " . $USER_TG_ID . "<br>";

// Инициализация переменной
$button_two = true;

// Проверяем статус последней задачи пользователя
$sql = "SELECT date_end FROM tasks WHERE tg_id = ? ORDER BY date_start DESC LIMIT 1";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $USER_TG_ID);
$stmt->execute();
$result2 = $stmt->get_result();

if ($result2) {
    if ($result2->num_rows > 0) {
        // Получаем последний элемент
        $row = $result2->fetch_assoc();
        $date_end = $row["date_end"];

        // Устанавливаем значение $button_two в зависимости от статуса
        $button_two = ($date_end !== null);
    }
} else {
    echo "Error executing query: " . $connect->error;
}

$formSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start_work'])) {
        $latitude = $_POST['latitude']; // Широта
        $longitude = $_POST['longitude']; // Долгота

        // Вставляем данные в таблицу tasks
        $sql = "INSERT INTO tasks (tg_id, geo_shir_start, geo_dol_start, date_start, geo_shir_end, geo_dol_end, date_end, path_file)
                VALUES (?, ?, ?, NOW(), NULL, NULL, NULL, NULL)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("idd", $USER_TG_ID, $latitude, $longitude);

        if ($stmt->execute()) {
            // Сохраняем date_start в сессии
            $sql = "SELECT date_start FROM tasks WHERE tg_id = ? ORDER BY date_start DESC LIMIT 1";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $USER_TG_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $_SESSION['date_start'] = $row['date_start']; // Сохраняем дату начала в сессии

            $formSubmitted = true;
            echo "Работа успешно начата! Запись создана в базе данных.";
            // После успешного добавления записи обновляем статус кнопки
            $button_two = false;
        } else {
            echo "Ошибка при добавлении записи: " . $stmt->error;
        }
    } elseif (isset($_POST['end_work'])) {
        $latitude = $_POST['latitude']; // Широта
        $longitude = $_POST['longitude']; // Долгота

        // Получаем date_start из сессии
        $date_start = $_SESSION['date_start'];

        // Обновляем запись по date_start
        $sql = "UPDATE tasks 
                SET geo_shir_end = ?, 
                    geo_dol_end = ?, 
                    date_end = NOW(),
                    path_file = 'C:/'
                WHERE tg_id = ? 
                AND date_start = ? 
                AND date_end IS NULL 
                LIMIT 1";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ddis", $latitude, $longitude, $USER_TG_ID, $date_start);

        if ($stmt->execute()) {
            $formSubmitted = true;
            echo "Работа успешно завершена! Запись обновлена в базе данных.";
            // После успешного завершения задачи обновляем статус кнопки
            $button_two = true;
        } else {
            echo "Ошибка при обновлении записи: " . $stmt->error;
        }
    }
}

#echo "Button Two: " . var_export($button_two, true);
$connect->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmployMoni</title>
    <?php if ($formSubmitted): ?>
        <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <?php endif; ?>
    <style>
        /* Основные стили для страницы */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Минимальная высота равна высоте экрана */
            background-color: #f0f0f0; /* Светлый фон */
        }

        /* Основной контент */
        .content {
            flex: 1; /* Растягиваем контент на всё доступное пространство */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        /* Кнопки в основном контенте */
        .button {
            display: block;
            background-color: #9e9e9e;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            width: 250px;
            margin: 10px 0;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #FFA632;
        }
        .footer {
            display: flex;
            width:100vw;
            justify-content: space-around;
            background-color: #fff;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
        }
        .footer-button {
            background-color: #e3e3e3;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            color: #000;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .footer-button:hover {
            background-color: #777;
        }
    </style>
</head>
<body>

    <!-- Основной контент -->
<div class="content">
    <form method='POST' action='' id="startWorkForm">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <button class="button" type='submit' name="start_work">Начать работу</button>
    </form>
    <form method="POST" action="" id="endWorkForm" style="display: none;">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <button class="button" type="submit" name="end_work">Закончить работу</button>
    </form>
    <button class="button" onClick="window.location.href='mobile_forms.php'">Посмотреть свои отчёты</button>
</div>

    <!-- Футер с тремя кнопками -->
    <div class="footer">
        <button class="footer-button">Кнопка 1</button>
        <button class="footer-button" onClick="window.location.href='mobile_calendar.php'">Кнопка 2</button>
        <button class="footer-button" onClick="window.location.href='mobile_forms.php'">Кнопка 3</button>
    </div>

    <script>
// Функция для получения местоположения
function getLocation(callback) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                callback(latitude, longitude);
            },
            function(error) {
                alert("Ошибка при получении геолокации: " + error.message);
            }
        );
    } else {
        alert("Ваш браузер не поддерживает Geolocation API.");
    }
}

// Функция для отправки данных на сервер
function sendData(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            callback(xhr.responseText);
        }
    };
    xhr.send(data);
}

// Обработчик нажатия на кнопку "Начать работу"
document.getElementById('startWorkForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем стандартную отправку формы

    // Получаем местоположение
    getLocation(function(latitude, longitude) {
        // Меняем кнопку "Начать работу" на "Закончить работу"
        const startWorkForm = document.getElementById('startWorkForm');
        const endWorkForm = document.getElementById('endWorkForm');
        startWorkForm.style.display = "none"; // Скрываем кнопку "Начать работу"
        endWorkForm.style.display = "block"; // Показываем кнопку "Закончить работу"

        // Формируем данные для отправки
        const data = `latitude=${latitude}&longitude=${longitude}&start_work=1`;

        // Отправляем данные на сервер
        sendData(window.location.href, data, function(response) {
            console.log("Ответ сервера:", response);
        });
    });
});

// Обработчик нажатия на кнопку "Закончить работу"
document.getElementById('endWorkForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем стандартную отправку формы

    // Получаем местоположение
    getLocation(function(latitude, longitude) {
        // Меняем кнопку "Закончить работу" на "Начать работу"
        const startWorkForm = document.getElementById('startWorkForm');
        const endWorkForm = document.getElementById('endWorkForm');
        endWorkForm.style.display = "none"; // Скрываем кнопку "Закончить работу"
        startWorkForm.style.display = "block"; // Показываем кнопку "Начать работу"

        // Формируем данные для отправки
        const data = `latitude=${latitude}&longitude=${longitude}&end_work=1`;

        // Отправляем данные на сервер
        sendData(window.location.href, data, function(response) {
            console.log("Ответ сервера:", response);
            // Выводим сообщение об успешном завершении работы
            alert("Вы завершили работу! Не забудьте прикрепить отчёт!");
        });
    });
});
    </script>
</body>
</html>