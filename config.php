<?php

$handle = fopen('./.env', "r");
$env = [];
if ($handle) {
    while (($line = fgets($handle)) !== false) {
    	list($name, $val) = explode('=', $line, 2);
    	$env[trim($name)] = trim($val);
    }

    fclose($handle);
}

function get(&$var, $default=null) {
    return !empty($var) ? $var : $default;
}

/* Параметры страниц */

//Кодировка страницы
$config['page_charset'] = 'utf-8';


/*Настройки путей*/

//Контроллеры
$config['path']['controller'] = '/controller/';
//Виды
$config['path']['view'] = '/view/';
//Разделы
$config['path']['section'] = '/sections/';
//Включения
$config['path']['include'] = '/include/';
//Хранилище
$config['path']['storage'] = '/storage/';


/* Настройки хранилища*/

//Максимальный размер персонального хранилища пользователя в МБ (-1 - неограничено)
$config['storage']['user_max_size'] = 50;
//Максимальный размер персонального хранилища администратора в МБ (-1 - неограничено)
$config['storage']['admin_max_size'] = -1;
//Максимальный размер общего хранилища МБ (-1 - неограничено)
$config['storage']['max_size'] = 5 * 1024;


/* Настройки разделов */

//Количество публикаций на странице
$config['section']['page_size'] = 5;
//Количество видимых ссылок на страницы с публикациями
$config['section']['pages_per_page'] = 3;

/* Настройки публикаций */

//Максимальный размер ресурсов статьи (рисунки и т.д.) в мегабайтах
$config['article']['max_resources'] = 50;
//Максимальное количество непроверенных публикаций для одного пользователя
$config['article']['max_unverified_articles'] = 2;


/* Настройки временной папки */

//Максимальный объем файлов, загруженных с одной страницы в мегабайтах
$config['temp']['max_user_upload'] = 25;


/* Параметры базы данных */

//Хост
$config['db']['host'] = get($env['DB_HOST'], 'localhost');
//Имя пользователя
$config['db']['user'] = get($env['DB_USER'], 'root');
//Пароль
$config['db']['pass'] = get($env['DB_PASSWORD'], 'password');
//Имя базы
$config['db']['db'] = get($env['DB_NAME'], 'planetsbook');
//Кодировка запросов
$config['db']['charset'] = get($env['DB_CHARSET'], 'utf8');
//Указывает поведение функций при ошибках. TRUE - выбрасывает исключение, FALSE - возвращает FALSE
$config['db']['throwable'] = TRUE;
