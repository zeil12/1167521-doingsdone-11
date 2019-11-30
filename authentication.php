<?php
require_once('helpers.php');
require_once('functions.php');
require('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $form = $_POST;
    $errors = [];
    $req_fields = ['email', 'password'];

    foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Введен неверный формат email";
        } else {
            $email = mysqli_real_escape_string($connect, $form['email']);
            $sql = "SELECT * FROM user WHERE email= '$email'";
            $result = mysqli_query($connect, $sql);
            
            if (mysqli_num_rows($result) != 1) {
                $errors['email'] = "Данный email не найден в системе";
                $email = '';
            }
        }
        if (empty($errors)) {
        
            $users = mysqli_fetch_assoc($result);
            
            if ( password_verify( $form['password'] , $users['password'] ) ) {
                
                $_SESSION["user"] = $users;
                header("Location: index.php");
            } else {
                $errors['password'] = "Неверно введен пароль";
            }
            
        }
}
$page_content = include_template("auth.php", [
    'errors' => $errors,
    'email' => $email
]);
$layout_content = include_template("layout.php", [
    'content' => $page_content,
    'title' => 'Дела в порядке | Вход на сайт'
]);
print($layout_content);
?>
    