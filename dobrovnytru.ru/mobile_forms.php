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

$sql = "SELECT * FROM tasks WHERE tg_id = ? AND date_end IS NOT NULL ORDER BY date_end DESC";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $USER_TG_ID);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Календарь</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap"
      rel="stylesheet"
    />
    <style>
    body{
        margin:0;
        padding:0;
        font: Matter, sans-serif;
        overflow-x: hidden;
        user-select: none;
        overscroll-behavior: none;
        background-color: #f0f0f0
    }
    .Full{
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction:column;
        justify-content: center;
        align-items: center;
        width:100vw;
        margin-right:auto;
        margin-left:auto;
    }
    .task-block {
        background-color: #fff;
        padding: 15px;
        margin: 10px 0;
        border-radius: 10px;
        width: 80vw;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .task-block h3 {
        margin: 0;
    }
    .task-block p {
        margin: 5px 0;
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

<div class="Full">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tg_id = $row["tg_id"];
            $date_start = $row["date_start"];
            $date_end = $row["date_end"];
            $geo_shir_start = $row["geo_shir_start"];  // Широта
            $geo_dol_start = $row["geo_dol_start"];   // Долгота
            $geo_shir_end = $row['geo_shir_end'];
            $geo_dol_end = $row['geo_dol_end'];
            $path_file = $row["path_file"];

            // Формируем ссылку на Яндекс.Карты
            $yandex_start_map_url = "https://yandex.ru/maps/?ll=$geo_dol_start,$geo_shir_start&z=108&pt=$geo_dol_start,$geo_shir_start~ym";
            $yandex_end_map_url = "https://yandex.ru/maps/?ll=$geo_dol_end,$geo_shir_end&z=108&pt=$geo_dol_end,$geo_shir_end~ym";

            // Создание блока для каждой задачи
            echo '<div class="task-block">';
            echo '<h3>Задача</h3>';
            echo '<p><strong>Дата начала:</strong> ' . $date_start . '</p>';
            echo '<p><strong>Дата окончания:</strong> ' . $date_end . '</p>';
            echo '<p><strong>Отчет:</strong> ' . $path_file . '</p>';
            echo '<p><strong>Местоположение старта:</strong> <a href="' . $yandex_start_map_url . '" target="_blank">Посмотреть на Яндекс.Картах</a></p>';
            echo '<p><strong>Местоположение конца:</strong> <a href="' . $yandex_end_map_url . '" target="_blank">Посмотреть на Яндекс.Картах</a></p>';
            echo '</div>';
        }
    } else {
        echo "Нет задач для отображения.";
    }
    ?>
</div>

<div class="footer">
    <button class="footer-button" onClick="window.location.href='mobile_index.php'">Кнопка 1</button>
    <button class="footer-button" onClick="window.location.href='mobile_calendar.php'">Кнопка 2</button>
    <button class="footer-button">Кнопка 3</button>
</div>

</body>
</html>

<?php
$stmt->close();
$connect->close();
?>
