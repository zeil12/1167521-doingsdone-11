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

$projects = allProjects($connect, $users['id']);

if (isset($id)) {
    $tasks = currentTask($connect, $id, $users['id']);
} else {
    $tasks = allTasks($connect, $users['id']);
};

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
    if ($search) {
    $tasks = taskFinder($connect, $users['id'], $search);
    if (!$tasks) {
        $search_error = "Ничего не найдено по вашему запросу";
    };
}
};

if (isset($_GET['task_id'])) {
    
    $sql = "UPDATE task SET status=ABS(status-1) WHERE id=".$_GET['task_id'];
    $result = mysqli_query($connect, $sql);
    
    if (!$result) {
        $error = mysqli_error($connect);
        print("MySQL error: ". $error);
     }else
        header('Location: index.php');
        exit();
}

if (isset($_GET['filter'])) {
    if ($_GET['filter'] === "past") {
          $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
            FROM task t
            WHERE DATE(t.deadline) < DATE(NOW()) and t.user_id =" .$user_id;
        }
        if ($_GET['filter'] === "tomorrow") {
                $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
                FROM task t
                WHERE DATE(t.deadline) = (CURDATE() + INTERVAL 1 DAY) and t.user_id =" .$user_id;
            }
        if ($_GET['filter'] === "today") {
            $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
            FROM task t
            WHERE DATE(t.deadline) = DATE(NOW()) and t.user_id =" .$user_id;
            }
    $result = mysqli_query($connect, $sql);
    
        if (!$result) {
        $error = mysqli_error($connect);
        print("MySQL error: ". $error);
     }
    
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

}

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
