<?php

function allProjects($connect) {
    $sql = "SELECT id, title FROM project WHERE user_id = 2";
$result = mysqli_query($connect, $sql);
if (!$result) {
    $error = mysqli_error($connect);
    print("MySQL error: ". $error);
}

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

return $categories;

};

function allTasks($connect) {
$sql = "SELECT id, creation_date, status, task_name, file_link, deadline, user_id, project_id FROM task WHERE user_id = 2";
$result = mysqli_query($connect, $sql);
if (!$result) {
    $error = mysqli_error($connect);
    print("MySQL error: ". $error);
}

$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

return $tasks;

};

function currentTask ($connect, $project_id) {
$sql = "SELECT id, task_name, deadline, user_id, project_id status FROM task WHERE user_id = 2 AND project_id = ?";
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

function count_tasks(array $tasks, $categories): int
{
    $count = 0;
    
    foreach ($tasks as $item) {
        
        if ($item["project_id"] === $categories["id"]) {
          $count ++;
        } 
    }
    
    return $count;
};

function is_task_urgent(?string $date): int
{
    $sec_in_hours = 3600;
    $end_ts = strtotime($date);
    $ts_diff = $end_ts - time();
    $time = floor($ts_diff / $sec_in_hours);

    return $time <= 24;
    
};

