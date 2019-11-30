<?php
require_once('helpers.php');
require_once('functions.php');
require('init.php');

$id = filter_input(INPUT_GET,'project', FILTER_SANITIZE_NUMBER_INT);
$projects = allProjects($connect);
$projects_id = array_column($projects, 'id');
$user_id = getCurrentUserId($connect, 2);

$tpl_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $errors = [];
    $req_fields = ['email', 'password', 'name'];

    
     foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    } 
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Введен неверный формат email!";
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($connect, $form['email']);
        $sql = "SELECT id, email FROM user WHERE email = '$email'";
        $result = mysqli_query($connect, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = "Пользователь с этим email уже зарегистрирован";
        }else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (registration_date, email, user_name, password) VALUES (NOW(), ?, ?, ?)";
            $stmt = db_get_prepare_stmt($connect, $sql, [$form['email'], $form['name'], $password]);
            $result = mysqli_stmt_execute($stmt);
        }
        if (!$result) {
            $error = mysqli_error($connect);
            print("MySQL error: ". $error);
         } else
            header("Location: index.php");
        }
    }

        $tpl_data['errors'] = $errors;
        $tpl_data['values'] = $form;

    $page_content = include_template("register.php", $tpl_data, [
            "projects" => $projects,
            "id" => $id

    ]);
    $layout_content = include_template("layout.php", [
        "content" => $page_content,
        "user" => "Jack",
        "title" => "Дела в порядке | Регистрация нового пользователя"
    ]);
    print($layout_content);