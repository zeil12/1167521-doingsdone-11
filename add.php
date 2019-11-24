<?php
require_once('helpers.php');
require('functions.php');
$user_id = 2;

$connect = mysqli_connect('127.0.0.1', 'root', '', 'doingsdone');
if (!$connect) {
    print("Ошибка соединения: " . mysqli_connect_error());
    exit();
}
mysqli_set_charset($connect, "utf8");

$id = filter_input(INPUT_GET,'project', FILTER_SANITIZE_NUMBER_INT);
$projects = allProjects($connect);
$projects_id = array_column($projects, 'id');
    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$required = ['title', 'project'];
    $errors = [];

    $rules = [
        'date' => function ($value) {
            return validateDate($value);
        },
        'project' => function ($value) use ($projects_id) { 
            return validateProject(intval($value), $projects_id);
        },
        'title' => function ($value) {
                return validateLength($value, 5, 100);
        }
    ];

    $task = filter_input_array(INPUT_POST,
        [
            'title' => FILTER_DEFAULT,
            'project' => FILTER_DEFAULT,
            'date' => FILTER_DEFAULT,
            'file' => FILTER_DEFAULT
        ], true);

        foreach ($task as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule($value);
            }
    
            if (in_array($key, $required) && empty($value)) {
                $errors[$key] = "Поле $key надо заполнить";
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

        if (count($errors)) {
            $page_content = include_template($tpl_path . "form-task.php",
                [
                    'errors' => $errors,
                    'projects' => $projects,
                    'task' => $task
                ]);
                $layout_content = include_template($tpl_path . "layout.php", [
                    "content" => $page_content,
                    "user" => "Jack",
                    "title" => "Дела в порядке | Добавление задачи"
                ]);
                print($layout_content);
                exit;
                }
                else {
                    $task['date'] = date_format(date_create($task['date']), 'd.m.Y');
                    $sql = "INSERT INTO task (user_id, task_name, project_id, deadline, file_link, status) VALUES (2, ?, ?, ?, ?, 0)";
                    $stmt = db_get_prepare_stmt($connect, $sql, $task);
                    $result = mysqli_stmt_execute($stmt);

                    if (!$result) {
                        $error = mysqli_error($connect);
                        print("MySQL error: ". $error);
                     } else
                        header("Location: index.php");
                    }
                }

            $page_content = include_template($tpl_path . "form-task.php", [
                    "projects" => $projects,
                    "id" => $id

            ]);
            $layout_content = include_template($tpl_path . "layout.php", [
                "content" => $page_content,
                "user" => "Jack",
                "title" => "Дела в порядке | Добавление задачи"
            ]);
            print($layout_content);
            
        

