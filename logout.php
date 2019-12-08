<?php
require('init.php');
unset($_SESSION['user']);
header("Location: index.php");
?>