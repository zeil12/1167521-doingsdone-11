<?php
require_once( 'helpers.php' );
require_once( 'functions.php' );
require( 'init.php' );

if ( !isset( $_SESSION['user'] ) ) {
    header( 'location: guests.php' );
}

$user = $_SESSION['user'];
$user_id = mysqli_real_escape_string($connect, $_SESSION['user']['id']);

$sql = "SELECT * FROM user WHERE id= '$user_id'";
$result = mysqli_query( $connect, $sql );
$users = mysqli_fetch_assoc( $result );

$id = filter_input( INPUT_GET, 'project', FILTER_SANITIZE_NUMBER_INT );
$projects = allProjects( $connect, $users['id'] );
$projects_id = array_column( $projects, 'id' );

$errors = [];

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $required = ['title', 'project'];

    $rules = [

        'project' => function ( $task ) use ( $projects_id ) {

            return validateProject( intval( $task ), $projects_id );
        },
        'title' => function ( string $task ) {
            return validateLength( $task, 5, 100 );
        },
        'date' => function ($value) {
            return validateDate($value);
        }
    ];

    $fields =
    [
        'title' => FILTER_DEFAULT,
        'project' => FILTER_DEFAULT,
        'date' => FILTER_DEFAULT,
        'file' => FILTER_DEFAULT
    ];

    $task = filter_input_array( INPUT_POST, $fields, true );

   

    foreach ( $task as $key => $value ) {
        if ( isset( $rules[$key] ) ) {
            $rule = $rules[$key];
            $errors[$key] = $rule( $value );
        }

        if ( in_array( $key, $required ) && empty( $value ) ) {
            $errors[$key] = "Поле $key не заполнено";
        }
    }

    if ( !empty( $_FILES['file']['name'] ) ) {

        $file_name = $_FILES['file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        $file_size = $_FILES['file']['size'];
        if ( $file_size > 500000 ) {
            $errors['file'] = 'Максимальный размер файла: 500Кб';
        } else {
            move_uploaded_file( $_FILES['file']['tmp_name'], $file_path . $file_name );
            $task['file'] = $file_name;
        }
    }

    $errors = array_filter( $errors );

    if ( count( $errors ) ) {
        if (!empty($task['date'])) 
        {
            $task['date'] = date_format(date_create($task['date']), 'Y.m.d'); 
        }else {
            $task['date'] = null;
        }
    $page_content = include_template( 'form-task.php',
    [
        'errors' => $errors,
        'projects' => $projects,
        'task' => $task,

    ] );
    $layout_content = include_template( 'layout.php', [
        'content' => $page_content,
        'users' => $users,
        'title' => 'Дела в порядке | Добавление задачи'
    ] );
    print( $layout_content );
    exit;

    } else {
        $sql = "INSERT INTO task (creation_date, user_id, task_name, project_id, deadline, file_link, status) VALUES (NOW(), $user_id, ?, ?, ?, ?, 0)";
        $stmt = db_get_prepare_stmt( $connect, $sql, $task );
        $result = mysqli_stmt_execute( $stmt );

        if ( !$result ) {
            $error = mysqli_error( $connect );
            print( $task );
        } else {
            header( 'Location: index.php' );
        }
    }
}

$page_content = include_template( 'form-task.php', [
    'projects' => $projects,
    'id' => $id,
    'errors' => $errors
] );
$layout_content = include_template( 'layout.php', [
    'content' => $page_content,
    'users' => $users,
    'title' => 'Дела в порядке | Добавление задачи'
] );
print( $layout_content );