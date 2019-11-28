<?php
session_start();

$show_complete_tasks = rand(0, 1);

$connect = mysqli_connect('127.0.0.1', 'root', '', 'doingsdone');
if (!$connect) {
    print("Ошибка соединения: " . mysqli_connect_error());
    exit();
}
mysqli_set_charset($connect, "utf8");