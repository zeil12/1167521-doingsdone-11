<?php
require_once ('helpers.php');
require_once ('functions.php');
require ('init.php');

$page_content = include_template('guest.php', [

]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке | Гостевая страница']);
print ($layout_content);
?>
