<?php
require_once('helpers.php');
require_once('functions.php');
require('init.php');

$id = filter_input(INPUT_GET,'project', FILTER_SANITIZE_NUMBER_INT);
$user_id = 2;

$projects = currentProjects($connect, $user_id);

if (isset($id)) {
    $tasks = currentTask($connect, $id, $user_id);
} else {
    $tasks = allTasks($connect, $user_id);
};
    
if (idCheck($connect, $id) || !isset($id)) {
$page_content = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
    'projects' => $projects,
    'id' => $id
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Дела в порядке",
    "user" => "Jack"
]);

print($layout_content);
} else {
    var_dump(http_response_code(404));
}


?>
