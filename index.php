<?php

error_reporting(E_ALL);
require_once 'config.php';

header('Content-type: text/html; charset=' . $config['page_charset']);

define('PATH_ROOT',         $_SERVER['DOCUMENT_ROOT']);
define('PATH_CONTROLLER',   PATH_ROOT . $config['path']['controller']   . '/');
define('PATH_VIEW',         PATH_ROOT . $config['path']['view']         . '/');
define('PATH_INCLUDE',      PATH_ROOT . $config['path']['include']      . '/');
define('PATH_SECTION',      PATH_ROOT . $config['path']['section']      . '/');
define('PATH_STORAGE',      PATH_ROOT . $config['path']['storage']      . '/');

require_once(PATH_INCLUDE . 'app.php');
$app=new Application($config);
$app->Run();