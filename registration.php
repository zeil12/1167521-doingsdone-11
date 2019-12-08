<?php
require_once('helpers.php');
require_once('functions.php');
require('init.php');

$tpl_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $errors = [];
    $req_fields = ['email', 'password', 'name'];
    $rules = [
        "email" => function($value) {
            return validateEmail($value);
        }
        ];

    foreach ($form as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        if (in_array($key, $req_fields) && empty($value)) {
           $errors[$key] = "Данное поле нужно заполнить";
        }
            

    }

    $errors = array_filter($errors);

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
         } else {
            $email = mysqli_real_escape_string($connect, $form['email']);
            $sql = "SELECT * FROM user WHERE email= '$email'";
            $result = mysqli_query($connect, $sql);
            $users = mysqli_fetch_assoc($result);
            $_SESSION["user"] = $users;
            
            header("Location: index.php");
        }
    }
}
        $tpl_data['errors'] = $errors;
        $tpl_data['values'] = $form;
        

    $page_content = include_template("register.php", $tpl_data, [
            "id" => $id,
            

    ]);
    $layout_content = include_template("layout.php", [
        "content" => $page_content,
        "user" => "Jack",
        "title" => "Дела в порядке | Регистрация нового пользователя"
    ]);
    print($layout_content);