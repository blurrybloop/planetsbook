<?php 
header('Content-type: text/html; charset=utf-8');
if (!defined('PATH_CONTROLLER')) define('PATH_CONTROLLER','controller/');
if (!defined('PATH_VIEW')) define('PATH_VIEW','view/');
include_once('include/mysql.php');
include_once('include/app.php');
$app=new Application();
$app->Run();
?>




