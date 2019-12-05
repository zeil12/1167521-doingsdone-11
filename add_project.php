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
$projects_id = array_column($projects, 'id');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$required = ['name'];
    $errors = [];

    $rules = [
        
        'name' => function (string $project) {
            return validateLength($project, 5, 100);
         }
    ];

    $field = [
        'name' => FILTER_DEFAULT
    ];

    $add_project = filter_input_array(INPUT_POST, $field, true);

    foreach ($add_project as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key не заполнено";
        }
    }

    

    if (isset($_POST['name'])) {
        $project_title = mysqli_real_escape_string($connect, $_POST['name']);
        $sql = "SELECT * FROM project WHERE title ='$project_title' AND user_id='$user_id'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result)) {
            $errors['name'] = 'Данный проект уже существует';
        }
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        $sql = "INSERT INTO project (title, user_id) VALUES (?, $user_id)";
        $stmt = db_get_prepare_stmt($connect, $sql, $add_project);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            $error = mysqli_error($connect);
            print("MySQL error: ". $errors);
        } else {
            header("Location: index.php");
        }
    }
}
$page_content = include_template($tpl_path . "form-project.php", [
    "projects" => $projects,
    "id" => $id,
    "errors" => $errors
]);
$layout_content = include_template($tpl_path . "layout.php", [
"content" => $page_content,
"users" => $users,
"title" => "Дела в порядке | Добавление задачи"
]);
print($layout_content);