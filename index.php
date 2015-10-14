<?php
header('Content-type: text/html; charset=utf-8');
define('PATH_CONTROLLER','controller/');
define('PATH_VIEW','view/');
include('include/mysql.php');
include('include/app.php');
$app=new Application();
$app->Run();
?>




