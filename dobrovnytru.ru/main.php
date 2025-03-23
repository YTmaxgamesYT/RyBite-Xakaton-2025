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

// Получаем сегодняшнюю дату
$today = date("Y-m-d");

// Запрос для получения данных за сегодняшнюю дату (без учета времени)
$query = "
    SELECT tg_id, 
           DATE_FORMAT(date_start, '%H:%i') as start_time, 
           DATE_FORMAT(date_end, '%H:%i') as end_time, 
           CONCAT(geo_shir_start, ', ', geo_dol_start) as location, 
           path_file 
    FROM tasks 
    WHERE DATE(date_start) = '$today'
";
$result = mysqli_query($connect, $query);

// Массив для хранения данных
$tasks = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Employ Moni</title>
</head>
<body>
    <style>
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
        body {
            font: Matter, sans-serif;
            margin: 0;
            overflow-x: hidden;
            user-select: none;
            overscroll-behavior: none;
            background-color: #f0f0f0
        }
        .inp {
            background: var(--color-light);
            padding: 0 1.6rem;
            margin:calc(var(--index)*0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            width: 30vw;
            height: calc(var(--index)*2);
            border: 1px solid;
            border-color:#272727;
            font: clamp(0.5em, var(--index), 1em) Arial, sans-serif;
        }
        .subi {
            font: clamp(0.6em, var(--index)*1.2, 1.2em) Arial, sans-serif;
            color:#222222;
            background: var(--color-light);
            padding: 0 1.6rem;
            margin:calc(var(--index)*0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            width: 12.5vw;
            height: calc(var(--index)*2);
            border: 1px solid;
            border-color:#272727;
        }
        .forma{
            display: flex;
            flex-direction:row;
            align-items: center;
            justify-content: center;
            margin-bottom:(var(--index)*0.5);
        }
        .Full{
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            width:96vw;
            margin-right:auto;
            margin-left:auto;
            background-color: #fff;
            border-radius: var(--rad);
        }
        .Blo{
            margin-right:auto;
            margin-left:auto;
            margin-bottom:auto;
            margin-top:calc(var(--index)*.5);
        }
        .cont1{
            padding-top:calc(var(--index)*4.5)
        }
        .btn-menu {
            position: relative;
            width: 40px;
            height: 24px;
            cursor: pointer;
            margin-left:var(--index);
            margin-right:var(--index);
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
        
        .btn-menu i:before,.btn-menu i:after {
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
        .Menu{
            display:flex;
            flex-direction:column;
            position:fixed;
            width:calc(var(--index)*20);
            height:200vw;
            background-color:#3e3e3e;
            transform: translateX(calc(var(--index)*(-20)));
            transition: 0s;
            z-index:9;
            padding-top:calc(var(--index)*4);
            margin-left:auto;
        }
        .Menu button{
            background-color:rgba(0,0,0,0);
            border: 0;
            border-color: #e3e3e3;
            color:#e3e3e3;
            padding-top:calc(var(--index)*.25);
            padding-left:calc(var(--index)*1.5);
            font: clamp(1em, 1.7vw + 1em, 2em) Matter, sans-serif;
            font-weight:600;
            text-align:left;
            cursor:pointer;
        }
        .BlurMenu{
            position:fixed;
            background-color:rgba(0,0,0,0);
            z-index:-8;
            width:100vw;
            height:100vh;
            transition: 0.25s;
        }
        .Header{
            width: 100vw;
            height:calc(var(--index)*3);
            background-color:#3e3e3e;
            color:#e3e3e3;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            border-bottom-left-radius:.85em;
            border-bottom-right-radius:.85em;
            position: fixed;
            z-index: 10;
        }
        .HalfHeader{
            display: flex;
            flex-direction: row;
            margin: auto;
            flex-basis:33.33%;
            font: clamp(.6em, .8vw + .6em, 1.2em) Matter, sans-serif;
            font-weight:600;
        }
        .HalfHeaderItem{
            padding-left:calc(1vw + 1vh);
            padding-right:calc(1vw + 1vh);
            color:#FFA632;
            scale: 1.4;
            Letter-spacing:.05em;
        }
        .HalfHeaderButton{
            margin-left:calc(1vw + 1vh);
            margin-right:calc(1vw + 1vh);
            padding-top:calc(.3vw + .3vh);
            padding-bottom:calc(.3vw + .3vh);
            padding-left:calc(1vw + 1vh);
            padding-right:calc(1vw + 1vh);
            border: 0.1em solid transparent;
            border-color: #e3e3e3;
            border-radius:calc(1vw + 1vh);
            background-color: rgba(0,0,0,0);
            color:#e3e3e3;
            font: clamp(.5em, .7vw + .5em, 1em) Matter, sans-serif;
            font-weight:600;
            cursor: pointer;
            transition: 0.25s;
        }
        .HalfHeaderButton:hover {
            transform: scale(1.05);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-header button {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap:7.5px;
        }
        .calendar-grid div {
            text-align: center;
            padding: 0;
            border-radius: 5px;
        }
        .calendar-grid div.other-month {
            color: #ccc;
        }
        .calendar-grid div.selected-day {
            background-color: red; /* Красный фон */
            color: white; /* Белый текст для контраста */
            border-radius: 50%; /* Круглая форма */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer; /* Курсор в виде указателя */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .Block{
            margin-right:auto;
            margin-left:auto;
            margin-bottom:auto;
            margin-top:calc(var(--index)*2);
        }
        #MainTable{
            margin-right:auto;
            margin-left:auto;
            max-width:60vw;
        }
        @media (max-width: 768px) {
            .mobile{display:block;} 
            .pk{display:none;}
            .Full{flex-direction:column;}
            .cont{
                background-color: #fff;
                padding: 20px;
                width:45vw;
                min-height:35vw;
                margin-right:auto;
                margin-left:auto;
                border: 1px solid #ddd;
                margin-bottom:calc(var(--index));
            }
        }
        @media (min-width: 768px) {
            .mobile{display:none;} 
            .pk{display:block;}
            .Full{flex-direction:row;}
            .cont{
                background-color: #fff;
                padding: 20px;
                width:20vw;
                min-height:20vw;
                margin-right:auto;
                margin-left:auto;
                border: 1px solid #ddd;
            }
        }
        .scale {
            transform: scale(0.635);
            transform-origin: top left;
        }
        
        
    </style>
    
    <div class="Header">
        <div class="HalfHeader" style="justify-content:left;">
            <div class="btn-menu"><i></i></div>
        </div>
        <div class="HalfHeader" style="justify-content:center;">
            <div class="HalfHeaderItem">Employ Moni</div>
        </div>
        <div class="HalfHeader" style="justify-content:right;">
            <button class="HalfHeaderButton" id="enterbtn" onClick="window.location.href='profile.php'">Мой профиль</button>
        </div>
    </div>
    
    <div class="Menu">
        <button id="MoiProfil" onClick="window.location.href='profile.php'">Профиль</button>
        <button id="Subordinates" onClick="window.location.href='subordinates.php'">Подчинённые</button>
        <button id="DateTable" onClick="window.location.href='allFilesTable.php'">Все отчёты</button>
        <button id="Alerts">Уведомления</button>
    </div>
    <div class="BlurMenu"></div>
    
    <div class="cont1">
        <div class="Full">
            <div class="Block pk">
                <table id="MainTable">
                    <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Отметка начала работы</th>
                            <th>Отметка конца работы</th>
                            <th>Геолокация</th>
                            <th>Ссылка на файл ежедневного отчёта</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Сюда будут подгружаться данные через AJAX -->
                    </tbody>
                </table>
            </div>
            <div class="Block">
                <div class="cont">
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
            </div>
            <div class="Block mobile scale">
                <table id="MainTable">
                    <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Отметка начала работы</th>
                            <th>Отметка конца работы</th>
                            <th>Геолокация</th>
                            <th>Ссылка на файл ежедневного отчёта</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Сюда будут подгружаться данные через AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@latest/bundled/lenis.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
function updateReportsTable(date) {
    $.ajax({
        url: 'get_reports.php',
        type: 'GET',
        data: { date: date },
        success: function(response) {
            $('#MainTable tbody').html(response);
        },
        error: function(xhr, status, error) {
            console.error("Ошибка при загрузке данных: " + error);
        }
    });
}

document.getElementById('calendar-grid').addEventListener('click', function(event) {
    if (event.target.tagName === 'DIV' && !isNaN(event.target.textContent)) {
        const allDays = document.querySelectorAll('#calendar-grid div');
        allDays.forEach(day => {
            day.classList.remove('selected-day');
        });

        event.target.classList.add('selected-day');

        const selectedDay = event.target.textContent;
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        const selectedDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(selectedDay).padStart(2, '0')}`;

        updateReportsTable(selectedDate);
    }
});
    </script>
    <script>
function highlightToday() {
    const today = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    if (today.getMonth() === currentMonth && today.getFullYear() === currentYear) {
        const todayDay = today.getDate();
        const calendarDays = document.querySelectorAll('#calendar-grid div');

        calendarDays.forEach(day => {
            if (day.textContent == todayDay && !isNaN(day.textContent)) {
                day.classList.add('selected-day');
                updateReportsTable(`${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(todayDay).padStart(2, '0')}`);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    highlightToday();
});</script>
    <script>
        let currentDate = new Date(2025, 2, 1);

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
        updateCalendar();
    </script>
    
    <script>
document.getElementById('calendar-grid').addEventListener('click', function(event) {
    if (event.target.tagName === 'DIV' && !isNaN(event.target.textContent)) {
        const allDays = document.querySelectorAll('#calendar-grid div');
        allDays.forEach(day => {
            day.classList.remove('selected-day');
        });
        event.target.classList.add('selected-day');
    }
});
    </script>

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
        });
    </script>

</html>