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
$projects_id = array_column($projects, 'id');

    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$required = ['title', 'project'];
    $errors = [];

    $rules = [
        
        'project' => function ($task) use ( $projects_id) { 
            return validateProject(intval($task), $projects_id);
        },
        'title' => function (string $task) {
            return validateLength($task, 5, 100);
         }
    ];

    $fields = 
    [
        'title' => FILTER_DEFAULT,
        'project' => FILTER_DEFAULT,
        'date' => FILTER_DEFAULT,
        'file' => FILTER_DEFAULT
    ];

    $task = filter_input_array(INPUT_POST, $fields, true);

    foreach ($task as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key не заполнено";
        }
    }

    $errors = array_filter($errors);

    if (!empty($_FILES['file']['name'])) {

        $filePath = __DIR__ . '/uploads/';
        $fileTmp = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $fileTmp);
        $fileName = uniqid() . '.' . mimeToText($fileType);
        $fileUrl = '/uploads/' . $fileName;
        if ($fileSize > 500000) {
            $errors["file"] = "Максимальный размер файла: 500Кб";
        } else {
            move_uploaded_file($_FILES['file']['tmp_name'], $filePath . $fileName);
            $task['file'] = $fileName;
        }
    }

    if (isset($_POST["date"])) {
        $data = $_POST["date"];
        if ($data < date("Y-m-d")) {
            $errors["date"] = "Дата выполнения задачи должна быть больше или равна текущей";
        }
    }

    if (count($errors)) {
        $page_content = include_template($tpl_path . "form-task.php",
            [
                'errors' => $errors,
                'projects' => $projects,
                'task' => $task,
                
            ]);
        $layout_content = include_template($tpl_path . "layout.php", [
            "content" => $page_content,
            "users" => $users,
            "title" => "Дела в порядке | Добавление задачи"
        ]);
        print($layout_content);
        exit;
    }
    else {
        $sql = "INSERT INTO task (creation_date, user_id, task_name, project_id, deadline, file_link, status) VALUES (NOW(), $user_id, ?, ?, ?, ?, 0)";
        $stmt = db_get_prepare_stmt($connect, $sql, $task);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            $error = mysqli_error($connect);
            print("MySQL error: ". $errors);
        } else {
            header("Location: index.php");
        }
    }
}
    
    $page_content = include_template($tpl_path . "form-task.php", [
            "projects" => $projects,
            "id" => $id,
    ]);
    $layout_content = include_template($tpl_path . "layout.php", [
        "content" => $page_content,
        "users" => $users,
        "title" => "Дела в порядке | Добавление задачи"
    ]);
    print($layout_content);

            
        

