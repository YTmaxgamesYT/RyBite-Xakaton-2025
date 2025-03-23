<?php
session_start();
// Проверка, залогинен ли пользователь
if (!isset($_SESSION['user_login'])) {
    header('Location: autorization.php');
    exit;
}

// Подключение к базе данных
require 'config.php';

try {
    // Получение данных пользователя
    $stmt = $pdo->prepare('SELECT login, email, post FROM users WHERE login = :login');
    $stmt->bindParam(':login', $_SESSION['user_login'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    echo 'Ошибка подключения к базе данных: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
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
            flex-direction:row;
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
            flex-direction:row;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width:96vw;
            margin-right:auto;
            margin-left:auto;
            background-color: #fff;
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
        .profile {
            display:flex;
            flex-direction:column;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile h1 {
            margin: 10px 0;
        }
        .profile p {
            margin: 5px 0;
        }
        .edit-form {
            display: none;
            margin-top: 20px;
        }
        .edit-icon {
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
        }
        .nickname-container, .describtion-container {
            display: flex;
            justify-content: center;
        }
        .nickname-container h1, .describtion-container p {
            margin: 0;
        }
        .UserPhoto{
            width:10vw;
            margin:auto;
        }
        .UsP{
            width:10vw;
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
        <div class="HalfHeader" style="justify-content:right;">
            <button class="HalfHeaderButton" id="enterbtn" onClick="window.location.href='main.php'">Главная</button>
        </div>
    </div>
    
    <div class="Menu">
        <button id="Subordinates" onClick="window.location.href='subordinates.php'">Подчинённые</button>
        <button id="DateTable" onClick="window.location.href='allFilesTable.php'">Все отчёты</button>
        <button id="Alerts">Уведомления</button>
    </div>
    <div class="BlurMenu"></div>
    
    <div class="cont1">
        <div class="Full">
            <div class="profile">
                <div class="UserPhoto">
                    <img src="https://dobrovnytru.ru/img/reviewsphoto.png" class="UsP" style="margin-bottom:1vw">
                </div>
                <p style="margin-bottom:1vw">Логин: <?php echo htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p style="margin-bottom:1vw">Email: <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p style="margin-bottom:1vw">Должность: <?php echo htmlspecialchars($user['post'], ENT_QUOTES, 'UTF-8'); ?></p>
                
                <button onclick="window.location.href='logout.php'" style="margin:auto">Выйти из профиля</button>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@latest/bundled/lenis.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
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
</body>
</html>
