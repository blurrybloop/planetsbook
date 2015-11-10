<?php
require 'include/config.php';
header('Content-type: text/html; charset=' . $config['page_charset']);
if (!defined('PATH_CONTROLLER')) define('PATH_CONTROLLER','controller/');
if (!defined('PATH_VIEW')) define('PATH_VIEW','view/');
require_once('include/database.php');
require_once('include/app.php');
$app=new Application($config);
$app->Run();
?>




