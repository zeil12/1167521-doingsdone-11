<?php

function allProjects( $connect, $user_id ) {
    $user_id = mysqli_real_escape_string($connect, $user_id);
    $sql = "SELECT p.title, p.id, p.user_id, COUNT(t.id) AS task_count FROM project p 
    LEFT JOIN task t ON t.project_id = p.id WHERE p.user_id = $user_id GROUP BY p.id";
    $result = mysqli_query( $connect, $sql );
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    $projects = mysqli_fetch_all( $result, MYSQLI_ASSOC );
    
    return $projects;
};

function allTasks( $connect, $user_id ) {
    $user_id = mysqli_real_escape_string($connect, $user_id);
    $sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id 
    FROM task WHERE user_id = $user_id";
    $result = mysqli_query( $connect, $sql );
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    $tasks = mysqli_fetch_all( $result, MYSQLI_ASSOC );
    
    return $tasks;
};

function currentTask( $connect, $project_id, $user_id ) {
    $sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id 
    FROM task WHERE user_id = $user_id  AND project_id = ?";
    $stmt = mysqli_prepare( $connect, $sql );
    mysqli_stmt_bind_param( $stmt, 'i', $project_id );
    mysqli_stmt_execute( $stmt );
    $result = mysqli_stmt_get_result( $stmt );
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    $current_tasks = mysqli_fetch_all( $result, MYSQLI_ASSOC );
    
    return $current_tasks;
};

function count_tasks( $tasks, $projects, $show_complete_tasks ):
int {
    $count = 0;
    foreach ( $tasks as $item ) {
        if ( $item['project_id'] == $projects['id'] ) {
            if ( $item['status'] == 0 ) {
                $count++;
            } elseif ( $item['status'] == 1 && $show_complete_tasks == 1 ) {
                $count++;
            }
        }
    }
    
    return $count;
};

function idCheck($connect, $id) {
    $sql = "SELECT id FROM  project WHERE id = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        $error = mysqli_error($connect);
        print("MySQL error: ". $error);
    }
    $list = mysqli_fetch_all($result);
    
    return empty(!$list) ? true : false;
};

function userCheck( $connect, $user_id ) {
    $user_id = mysqli_real_escape_string($connect, $user_id);
    $sql = "SELECT * FROM user WHERE id= '$user_id'";
    $result = mysqli_query( $connect, $sql );;
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    $users = mysqli_fetch_assoc( $result );
    
    return $users;
};

function changeTask( $connect, $task_id ) {
    $task_id = mysqli_real_escape_string($connect, $task_id);
    $sql = 'UPDATE task SET status=ABS(status-1) WHERE id=' . $task_id;
    $result = mysqli_query( $connect, $sql );
    
    return $result;
};

function changeFilter( $connect, $filter, $user_id ) {
    $user_id = mysqli_real_escape_string($connect, $user_id);
    if ( $_GET['filter'] === 'past' ) {
        $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
          FROM task t
          WHERE DATE(t.deadline) < DATE(NOW()) and t.user_id =" . $user_id;
    }
    if ( $_GET['filter'] === 'tomorrow' ) {
        $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
              FROM task t
              WHERE DATE(t.deadline) = (CURDATE() + INTERVAL 1 DAY) and t.user_id =" . $user_id;
    }
    if ( $_GET['filter'] === 'today' ) {
        $sql = "SELECT t.id, t.user_id, t.task_name, t.creation_date, t.deadline, t.status
          FROM task t
          WHERE DATE(t.deadline) = DATE(NOW()) and t.user_id =" . $user_id;
    }
    $result = mysqli_query( $connect, $sql );
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    $tasks = mysqli_fetch_all( $result, MYSQLI_ASSOC );
    
    return $tasks;
};

function is_task_urgent( string $date ):
bool {
    $sec_in_hours = 3600;
    $end_ts = strtotime( $date );
    $ts_diff = $end_ts - time();
    $time = floor( $ts_diff / $sec_in_hours );
    
    return $time <= 24;
};

function getPostVal( $title ) {
    
    return filter_input( INPUT_POST, $title );
};

function validateFilled($title ) {
    if ( empty( $_POST[$title] ) ) {
        
        return 'Это поле должно быть заполнено';
    }
};

function validateProject( $id, $list ) {
    if ( !in_array( $id, $list ) ) {
        return 'Проект не выбран';
    }
    
    return null;
};

function validateLength( string $value, int $min, int $max ) {
    if ( $value ) {
        $len = strlen( $value );
        if ( $len < $min or $len > $max ) {
            
            return "Значение должно быть от $min до $max символов";
        }
    }
    
    return null;
};

function validateEmail( $email ) {
    if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
        return 'email введён некорректно';
    }
    
    return null;
};

function validateDate(string $date)
{
    $currentDay = date('d.m.Y');
    $date = date_format(date_create($date), 'd.m.Y');
    if (strtotime($date) < strtotime($currentDay)) {
        return 'Дата должна быть больше или равна текущей';
    }
    return null;
};

function taskFinder( $connect, $user_id, $search ):
array {
    $sql = "SELECT t.id, t.creation_date, t.deadline, t.task_name, t.user_id, t.status FROM task t 
    LEFT JOIN user u ON t.user_id = u.id WHERE t.user_id = $user_id AND MATCH(task_name) AGAINST(?)";
    $stmt = mysqli_prepare( $connect, $sql );
    mysqli_stmt_bind_param( $stmt, 's', $search );
    mysqli_stmt_execute( $stmt );
    $result = mysqli_stmt_get_result( $stmt );
    if ( !$result ) {
        $error = mysqli_error( $connect );
        print ( 'MySQL error: ' . $error );
    }
    
    return mysqli_fetch_all( $result, MYSQLI_ASSOC );
};
