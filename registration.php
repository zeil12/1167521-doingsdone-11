<?php
require_once( 'helpers.php' );
require_once( 'functions.php' );
require( 'init.php' );

if ( isset( $_SESSION['user'] ) )  {
    header( 'location: index.php' );
}

$errors = [];
$form = [];

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    if (isset($_POST)) {
    $form = $_POST;
    }
    $req_fields = ['email', 'password', 'name'];

    foreach ( $req_fields as $field ) {
        if ( empty( $form[$field] ) ) {
            $errors[$field] = 'Не заполнено поле ' . $field;
        }
        if ( isset( $form[$field] ) ) {
            $len = strlen( $form[$field] );
        }
        if ( $len < 3 or $len > 30 ) {
            $errors[$field] = 'Значение должно быть от 3 до 30 символов';
        }
    }

    if ( isset( $form['email'] ) ) {
        if ( !filter_var( $form['email'], FILTER_VALIDATE_EMAIL ) ) {
            $errors['email'] = 'email введён некорректно';
        }
    }

    if ( empty( $errors ) ) {
        $email = mysqli_real_escape_string( $connect, $form['email'] );
        $sql = "SELECT id FROM user WHERE email = '$email'";
        $result = mysqli_query( $connect, $sql);
        

        if ( mysqli_num_rows( $result ) > 0 ) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else {
            $password = password_hash( $form['password'], PASSWORD_DEFAULT );
            $sql = 'INSERT INTO user (registration_date, email, user_name, password) VALUES (NOW(), ?, ?, ?)';
            $stmt = db_get_prepare_stmt( $connect, $sql, [$form['email'], $form['name'], $password] );
            $result = mysqli_stmt_execute( $stmt );
        }
        if ( $result && empty( $errors ) ) {
            $user = mysqli_real_escape_string( $connect, $form['name'] );
            $sql = "SELECT * FROM user WHERE user_name = '$user'";
            $result = mysqli_query( $connect, $sql );
            $users = mysqli_fetch_assoc( $result );
            $_SESSION['user'] = $users;
            header( 'Location: index.php' );
            exit();
        }
    }

}


$page_content = include_template( 'register.php', [
     'form' => $form,
     'errors' => $errors
]);
$layout_content = include_template( 'layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке | Регистрация нового пользователя'
] );
print( $layout_content );

