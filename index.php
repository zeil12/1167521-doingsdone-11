<?php
require_once('helpers.php');

$show_complete_tasks = rand(0, 1);
$categories = ["Входящие", "Учёба", "Работа", "Домашние дела", "Авто"];
$tasks = [
    [
        "task" => "Собеседование в IT компании",
        "date" => "04.11.2019",
        "category" => "Работа",
        "completed" => false
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date" => "25.12.2019",
        "category" => "Работа",
        "completed" => false
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date" => "21.12.2019",
        "category" => "Учёба",
        "completed" => true
    ],
    [
        "task" => "Встреча с другом",
        "date" => "22.12.2019",
        "category" => "Входящие",
        "completed" => false
    ],
    [
        "task" => "Купить корм для кота",
        "date" => null,
        "category" => "Домашние дела",
        "completed" => false
    ],
    [
        "task" => "Заказать пиццу",
        "date" => null,
        "category" => "Домашние дела",
        "completed" => false
    ]
];

function count_tasks(array $tasks, string $category): int
{
    $count = 0;
    
    foreach ($tasks as $item) {
        
        if ($item["category"] === $category) {
          $count ++;
        } 
    }
    
    return $count;
}

function is_task_urgent(?string $date): int
{
    $sec_in_hours = 3600;
    $end_ts = strtotime($date);
    $ts_diff = $end_ts - time();
    $time = floor($ts_diff / $sec_in_hours);

    return $time <= 24;
    
}
    
$page_content = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
    'categories' => $categories
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Дела в порядке"
]);

print($layout_content);

?>
