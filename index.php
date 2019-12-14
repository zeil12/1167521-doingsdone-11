<?php
require_once ( 'helpers.php' );
require_once ( 'functions.php' );
require ( 'init.php' );
if ( !isset( $_SESSION['user'] ) ) {
    header( 'location: guests.php' );
}
$user = $_SESSION['user'];
$user_id = $_SESSION['user']['id'];
$id = filter_input( INPUT_GET, 'project', FILTER_SANITIZE_NUMBER_INT );
$users = userCheck( $connect, $user_id );
$projects = allProjects( $connect, $users['id'] );
if ( isset( $id ) ) {
    $tasks = currentTask( $connect, $id, $users['id'] );
} else {
    $tasks = allTasks( $connect, $users['id'] );
}
;
$show_complete_tasks = 0;
if ( isset( $_GET['show_completed'] ) ) {
    $show_complete_tasks = $_GET['show_completed'];
}
if ( isset( $_GET['search'] ) ) {
    $search = trim( $_GET['search'] );
    if ( $search ) {
        $tasks = taskFinder( $connect, $users['id'], $search );
        if ( !$tasks ) {
            $search_error = 'Ничего не найдено по вашему запросу';
        }
        ;
    }
};
if ( isset( $_GET['task_id'] ) ) {
    changeTask( $connect, $_GET['task_id'] );
    header( 'Location: index.php' );
    exit();
}
if ( isset( $_GET['filter'] ) ) {
    $tasks = changeFilter( $connect, $_GET['filter'], $user_id );
}
if ( idCheck( $connect, $id ) || !isset( $id ) ) {
    $page_content = include_template( 'main.php', [
        'show_complete_tasks' => $show_complete_tasks, 'tasks' => $tasks, 'projects' => $projects, 'id' => $id, ] );
    $layout_content = include_template( 'layout.php', [
        'content' => $page_content, 'title' => 'Дела Впорядке', 'users' => $users] );
        print ( $layout_content );
    } else {
        var_dump( http_response_code( 404 ) );
}
?>
