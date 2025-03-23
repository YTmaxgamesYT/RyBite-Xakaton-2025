<?php
// Подключение к базе данных
$connect = mysqli_connect("localhost", "u2931124_default", "XDlYkr74kOU823LK", "u2931124_default") or die("Ошибка подключения к базе данных");
mysqli_set_charset($connect, "utf8");

// Получаем выбранную дату из запроса или используем сегодняшнюю дату
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Запрос для получения данных за выбранную дату
$query = "
    SELECT tg_id, 
           DATE_FORMAT(date_start, '%H:%i') as start_time, 
           DATE_FORMAT(date_end, '%H:%i') as end_time, 
           CONCAT(geo_shir_start, ', ', geo_dol_start) as location, 
           path_file 
    FROM tasks 
    WHERE DATE(date_start) = '$selectedDate'
";
$result = mysqli_query($connect, $query);

// Формируем HTML для таблицы
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['tg_id']) . "</td>
                <td>" . htmlspecialchars($row['start_time']) . "</td>
                <td>" . htmlspecialchars($row['end_time']) . "</td>
                <td>" . htmlspecialchars($row['location']) . "</td>
                <td>
                    " . (!empty($row['path_file']) ? "<a href='" . htmlspecialchars($row['path_file']) . "' target='_blank'>Скачать отчёт</a>" : "Нет файла") . "
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>На выбранную дату задач нет.</td></tr>";
}

// Закрываем соединение с базой данных
mysqli_close($connect);
?>