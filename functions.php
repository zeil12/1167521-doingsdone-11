<?php

function currentProjects($connect, $user_id) {
    $sql = "SELECT p.title, p.id, COUNT(t.id) AS task_count FROM project p
    LEFT JOIN task t ON t.project_id = p.id WHERE t.user_id = $user_id GROUP BY p.id";
    $result = mysqli_query($connect, $sql);

      if (!$result) {
         $error = mysqli_error($connect);
         print("MySQL error: " . $error);
      }

    return $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);

};

function allProjects($connect) {
    $sql = "SELECT p.title, p.id, COUNT(t.id) AS task_count FROM project p
    LEFT JOIN task t ON t.project_id = p.id GROUP BY p.id";
    $result = mysqli_query($connect, $sql);

      if (!$result) {
         $error = mysqli_error($connect);
         print("MySQL error: " . $error);
      }

    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $projects;

};

function allTasks($connect, $user_id) {
    $sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id 
    FROM task WHERE user_id = $user_id";
    $result = mysqli_query($connect, $sql);

      if (!$result) {
        $error = mysqli_error($connect);
        print("MySQL error: ". $error);
     }

    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $tasks;

};

function currentTask ($connect, $project_id, $user_id) {
    $sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id FROM task WHERE user_id = $user_id  AND project_id = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $project_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
        if (!$result) {
           $error = mysqli_error($connect);
           print("MySQL error: ". $error);
        }

    $current_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $current_tasks;
};

function count_tasks($tasks, $projects, $show_complete_tasks):int
{
    $count = 0;
    
    foreach ($tasks as $item) {
        
        if ($item["project_id"] == $projects["id"]) {
            if ($item['status']==0) {
                $count++; 
            } elseif ($item['status']==1 && $show_complete_tasks==1) { 
                $count++; 
            }
        }
    }
    
    return $count;
}; 
function getUserId($connect, $typedEmail)
{
    $sqlUserId =
        "SELECT id FROM user
    WHERE email = ?";
    $stmt = mysqli_prepare($connect, $sqlUserId);
    mysqli_stmt_bind_param($stmt, 's', $typedEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $id = mysqli_fetch_all($result);
    return $id;
};

function getCurrentUserId($connect, $id)
{
    $sql = "SELECT id FROM  user WHERE id = $id";
    $result = mysqli_query($connect, $sql);

      if (!$result) {
         $error = mysqli_error($connect);
         print("MySQL error: " . $error);
      }

    $user_id = mysqli_fetch_all($result);

    return $user_id;
};


function idCheck($connect, $id)
{
    $sql = "SELECT id FROM  project WHERE id = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = mysqli_fetch_all($result);
    
    return empty(!$list) ? true : false;
};


function is_task_urgent(?string $date): int
{
    $sec_in_hours = 3600;
    $end_ts = strtotime($date);
    $ts_diff = $end_ts - time();
    $time = floor($ts_diff / $sec_in_hours);

    return $time <= 24;
    
};

function getPostVal($title) {
    return filter_input(INPUT_POST, $title);
};

function validateFilled(string $title)
{
    if (empty($_POST[$title])) {
        return "Это поле должно быть заполнено";
    }
}

function validateDate($date)
{
    $currentDay = date('d.m.Y');
    $date = date_format(date_create($date), 'd.m.Y');
    if ($date < $currentDay) {
        
        return 'Дата должна быть больше или равна текущей';
    }
    return null;
};

function validateProject( $id, $allowedList)
{
    if (!in_array($id, $allowedList)) {
        return "Проект не выбран";
    }
    return null;
};

function validateLength(string $value, int $min, int $max)
{
    if ($value) {
        $len = strlen($value);
        if ($len < $min or $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }
    return null;
};

function validateEmail($value) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return "E-mail введён некорректно";
    }
    return null;
}

