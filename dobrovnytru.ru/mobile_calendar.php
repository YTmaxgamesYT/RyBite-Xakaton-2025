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

$sql = "SELECT DATE(date_end) as date_end FROM tasks WHERE tg_id = ? AND date_end IS NOT NULL";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $USER_TG_ID);
$stmt->execute();
$result2 = $stmt->get_result();

// Массив для хранения дат, на которых есть отчет
$dates_with_report = [];
while ($row = $result2->fetch_assoc()) {
    $dates_with_report[] = $row['date_end']; // Добавляем только дату без времени
}

// Преобразуем в JSON и передаем в JS
$dates_json = json_encode($dates_with_report);
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
        /* Основные стили */
        body {
            margin: 0;
            padding: 0;
            font-family: Matter, sans-serif;
            background-color: #f0f0f0;
        }
        .calendar {
            margin-top: 10vh;
            background-color: #fff;
            padding: 10px;
            border-radius: 10px;
            width: 80vw;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        .calendar-grid div {
            text-align: center;
            padding: 1px;
            border-radius: 5px;
        }
        .calendar-grid div.other-month {
            color: #ccc;
        }
        .footer {
            display: flex;
            width: 100vw;
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
        #download-section {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px; /* Отступ сверху */
    padding: 10px;
}
        .download-btn {
            background-color: #FFFFFF;
            border-radius: 5px;
            padding: 15px 30px;
            color: #FFFFF1F;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .download-btn:hover {
            background-color: #45a049;
        }
    </style>

</head>
<body>

    <div class="Full">
        <div class="calendar">
            <div class="calendar-header">
                <button id="prev-month">&lt;</button>
                <h2 id="current-month-year">Март 2025</h2>
                <button id="next-month">&gt;</button>
            </div>
            <div class="calendar-grid" id="calendar-grid">
                <div>Пн</div>
                <div>Вт</div>
                <div>Ср</div>
                <div>Чт</div>
                <div>Пт</div>
                <div>Сб</div>
                <div>Вс</div>
            </div>
        </div>

        <!-- Кнопка скачивания отчета -->
        <div id="download-section" style="display: none;">
            <button id="download-report" class="download-btn">Скачать отчет за этот день</button>
        </div>
    </div>

    <div class="footer">
        <button class="footer-button" onClick="window.location.href='mobile_index.php'">Кнопка 1</button>
        <button class="footer-button">Кнопка 2</button>
        <button class="footer-button" onClick="window.location.href='mobile_forms.php'">Кнопка 3</button>
    </div>  

    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@latest/bundled/lenis.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        let markedDates = <?php echo $dates_json; ?>; // получаем даты из PHP в JS

        let currentDate = new Date(); // Текущая дата

        // Функция для форматирования даты в строку YYYY-MM-DD
        function formatDateString(year, month, day) {
            return year + '-' + ('0' + (month + 1)).slice(-2) + '-' + ('0' + day).slice(-2);
        }

        function updateCalendar() {
            const monthYearElement = document.getElementById('current-month-year');
            const calendarGrid = document.getElementById('calendar-grid');

            while (calendarGrid.children.length > 7) {
                calendarGrid.removeChild(calendarGrid.lastChild);
            }

            const monthNames = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
            monthYearElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

            const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
            const startingDay = firstDayOfMonth.getDay() === 0 ? 6 : firstDayOfMonth.getDay() - 1;

            for (let i = 0; i < startingDay; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.classList.add('other-month');
                calendarGrid.appendChild(emptyDiv);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                dayDiv.textContent = day;

                let fullDateStr = formatDateString(currentDate.getFullYear(), currentDate.getMonth(), day);

                // Проверяем, если дата есть в массиве markedDates — выделяем её желтым
                if (markedDates.includes(fullDateStr)) {
                    dayDiv.style.backgroundColor = 'yellow';
                    dayDiv.style.cursor = 'pointer';

                    // При клике на дату, проверяем, если отчет существует для этого дня
                    dayDiv.addEventListener('click', function() {
                        document.getElementById('download-section').style.display = 'block'; // Показать кнопку скачивания

                        // Логика для проверки, если отчет есть
                        if (markedDates.includes(fullDateStr)) {
                            document.getElementById('download-report').style.display = 'block'; // Показываем кнопку скачать отчет
                        } else {
                            document.getElementById('download-report').style.display = 'none'; // Скрываем кнопку
                        }
                    });
                }

                calendarGrid.appendChild(dayDiv);
            }

            const totalCells = startingDay + daysInMonth;
            const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
            for (let i = 0; i < remainingCells; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.classList.add('other-month');
                calendarGrid.appendChild(emptyDiv);
            }
        }

        document.getElementById('prev-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });

        document.getElementById('next-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });

        // Логика скачивания отчета
        document.getElementById('download-report').addEventListener('click', function() {
            // Замените URL на путь к вашему скрипту, который генерирует отчет
            window.location.href = 'mobile_forms.php'; 
        });

        // Инициализация календаря
        updateCalendar();
    </script>

</body>
</html>

<?php
$connect->close();
?>
