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
        .Full{
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction:column;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            width:96vw;
            margin-right:auto;
            margin-left:auto;
            background-color: #fff;
            border-radius: var(--rad);
        }
        .cont1{
            padding-top:calc(var(--index)*4.5)
        }
        body {
            font: Matter, sans-serif;
            margin: 0;
            overflow-x: hidden;
            user-select: none;
            overscroll-behavior: none;
            background-color: #f0f0f0
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
            height:100vw;
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
        .inp {
            background: var(--color-light);
            padding: 0 1.6rem;
            margin-top:calc(var(--index));
            margin-right:calc(var(--index)*0.2);
            margin-left:calc(var(--index)*0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            position: relative;
            width: 54.25vw;
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
            margin-top:calc(var(--index));
            margin-right:calc(var(--index)*0.2);
            margin-left:calc(var(--index)*0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            position: relative;
            width: 10vw;
            height: calc(var(--index)*2);
            border: 1px solid;
            border-color:#272727;
        }
        .sort {
            font: clamp(0.6em, var(--index)*1.2, 1.2em) Arial, sans-serif;
            color:#222222;
            background: var(--color-light);
            padding: 0 1.6rem;
            margin-top:calc(var(--index));
            margin-right:calc(var(--index)*0.2);
            margin-left:calc(var(--index)*0.2);
            border-radius: var(--rad);
            transition: all var(--dur) var(--bez);
            transition-property: width, border-radius;
            z-index: 1;
            width: 17.5vw;
            height: calc(var(--index)*2);
            border: 1px solid;
            border-color:#272727;
        }
        .forma{
            display: flex;
            flex-direction:row;
            align-items: center;
            justify-content: center;
            position:relative;
            margin-bottom:(var(--index)*0.5);
        }
        .DisplayTable{ 
            width:90%;
            border-collapse: collapse;
            margin-bottom: auto;
            margin-right: calc(var(--index)*2);
            margin-left: calc(var(--index)*2);
            margin-top: calc(var(--index));
        }
        
    </style>
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
            <button id="DateTable" onClick="window.location.href='main.php'">Главная</button>
            <button id="Subordinates" onClick="window.location.href='subordinates.php'">Подчинённые</button>
            <button id="Alerts">Уведомления</button>
        </div>
        <div class="BlurMenu"></div>
        
        <div class=cont1>
            <div class="Full">
                <form method="post" class="forma" action ="allFilesTable.php">
                    <input type="text" class="inp" name="search" placeholder="Введите запрос">
                    <button type="submit" class="subi" name="submit" placeholder="Поиск">Найти</button>
                    <button type="submit" class="sort" name="sort" placeholder="Поиск">Сортировать по</button>
                </form>
                <table class="DisplayTable">
            <thead>
                <tr style="background-color: #3e3e3e; color: #fff;">
                    <th style="padding: 10px; border: 1px solid #ccc;">ТГ_айди</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Широта начала</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Долгота начала</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Широта конца</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Долга конца</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Время начала</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Дата конца</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Файл</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">ФИО</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $connect = mysqli_connect("localhost", "u2931124_default", "XDlYkr74kOU823LK", "u2931124_default") or die("Ошибка подключения: " . mysqli_connect_error());
                    mysqli_set_charset($connect, "utf8");
                    
                    function fuzzy_compare($x, $y, $minLen = 1, $maxLen = 0) {
                        if (empty($x) || empty($y)) {
                            return 0;
                        }
                        $l1 = strlen($x);
                        $l2 = strlen($y);
                        if ($l1 < $minLen || $l2 < $minLen) {
                            return 0;
                        }
                        if ($maxLen == 0) $maxLen = min($l1, $l2);
                        $summ = 0;
                        $count1 = 0;
                        for ($l = $minLen; $l <= $maxLen; $l++) {
                            for ($i1 = 0; $i1 <= ($l1 - $l); $i1++) {
                                $part = substr($x, $i1, $l);
                                $count1++;
                                if (strpos($y, $part) !== false) $summ++;
                            }
                        }
                        if ($count1 == 0) {
                            return 0;
                        }
                        return 100 * $summ / $count1;
                    }
                    if (isset($_POST['submit'])) {
                        $search = trim($_POST['search']);
                        if (strlen($search) > 1) {
                            $search = mysqli_real_escape_string($connect, $search);
                            $columns = [];
                            $result = mysqli_query($connect, "SHOW COLUMNS FROM tasks");
                            while ($row = mysqli_fetch_assoc($result)) {
                                $columns[] = $row['Field'];
                            }
                            $conditions = [];
                            foreach ($columns as $column) {
                                $conditions[] = "t.`$column` LIKE '%$search%'";
                            }
                            $sql = "
                                SELECT t.*, u.full_name 
                                FROM tasks t
                                LEFT JOIN users u ON t.tg_id = u.tg_id
                                WHERE " . implode(" OR ", $conditions);
                            $query = mysqli_query($connect, $sql);
                            $results = [];
                            while ($row = mysqli_fetch_assoc($query)) {
                                $score = 0;
                                foreach ($columns as $column) {
                                    $score += fuzzy_compare($search, $row[$column]);
                                }
                                $results[] = [
                                    'score' => $score,
                                    'data' => $row
                                ];
                            }
                            usort($results, function($a, $b) {
                                return $b['score'] <=> $a['score'];
                            });
                            foreach ($results as $result) {
                                echo "<tr>";
                                foreach ($result['data'] as $key => $value) {
                                    if ($key === 'user_name') {
                                        echo "<tdс style='padding: 10px; border: 1px solid #ccc;'>$value</td>";
                                    } else {
                                        echo "<td style='padding: 10px; border: 1px solid #ccc;'>$value</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } 
                        else {
                            echo "<h2>Слишком короткий запрос (минимум 2 символа)</h2>";
                        }
                    }
                ?>
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