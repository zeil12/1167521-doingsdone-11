<?php
require_once('helpers.php');

$show_complete_tasks = rand(0, 1);

$con = mysqli_connect('127.0.0.1', 'root', '', 'doingsdone');
if (!$con) {
    print("Ошибка соединения: " . mysqli_connect_error());
    exit();
}
mysqli_set_charset($con, "utf8");

$sql = "SELECT id, title FROM project WHERE user_id = 2";
$result = mysqli_query($con, $sql);

if (!$result) {
    $error = mysqli_error($con);
    print("MySQL error: ". $error);
}

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id FROM task WHERE user_id = 2";
$result = mysqli_query($con, $sql);

if (!$result) {
    $error = mysqli_error($con);
    print("MySQL error: ". $error);
}

$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);


function count_tasks(array $tasks, $categories): int
{
    $count = 0;
    
    foreach ($tasks as $item) {
        
        if ($item["project_id"] === $categories["id"]) {
          $count ++;
        } 
    }
    
    return $count;
}

function is_task_urgent(?string $date): int
{
    $sec_in_hours = 3600;
    $end_ts = strtotime($date);
    $ts_diff = $end_ts - time();
    $time = floor($ts_diff / $sec_in_hours);

    return $time <= 24;
    
}
    
$page_content = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
    'categories' => $categories
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Дела в порядке",
    'user'  => "Jack"
]);

print($layout_content);

?>
