<?php
require_once('helpers.php');
require_once('functions.php');
require('init.php');

if (!isset($_SESSION["user"])) {
    header("location: guests.php");
}


$user = $_SESSION["user"];
$user_id = $_SESSION["user"]["id"];

$sql = "SELECT * FROM user WHERE id= '$user_id'";
$result = mysqli_query($connect, $sql);
$users = mysqli_fetch_assoc($result);

$id = filter_input(INPUT_GET,'project', FILTER_SANITIZE_NUMBER_INT);

$projects = currentProjects($connect, $users['id']);

if (isset($id)) {
    $tasks = currentTask($connect, $id, $users['id']);
} else {
    $tasks = allTasks($connect, $users['id']);
};

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
    if ($search) {
    $tasks = TaskFinder($connect, $users['id'], $search);
    if (!$tasks) {
        $search_error = "Ничего не найдено по вашему запросу";
    };
}
};

    
if (idCheck($connect, $id) || !isset($id)) {
    $page_content = include_template('main.php', [
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $tasks,
        'projects' => $projects,
        'id' => $id,
        'search' => $search,
        'search_error' => $search_error

    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => "Дела Впорядке",
        'users' => $users    
    ]);

    print($layout_content);
} else {
    var_dump(http_response_code(404));
}


?>
