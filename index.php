<?php
require_once('helpers.php');
require('functions.php');

$show_complete_tasks = rand(0, 1);

$connect = mysqli_connect('127.0.0.1', 'root', '', 'doingsdone');
if (!$connect) {
    print("Ошибка соединения: " . mysqli_connect_error());
    exit();
}
mysqli_set_charset($connect, "utf8");

$id = filter_input(INPUT_GET,'project', FILTER_SANITIZE_NUMBER_INT);

$categories = allProjects($connect);

if (isset($id)) {
    $tasks = currentTask($connect, $id);
} else {
    $tasks = allTasks($connect);
};
    
$page_content = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
    'categories' => $categories,
    'id' => $id
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Дела в порядке",
    'user'  => "Jack"
]);

print($layout_content);

?>
