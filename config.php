<?php

/*Общие параметры*/

$config['path']['controller'] = '/controller/';
$config['path']['view'] = '/view/';
$config['path']['section'] = '/sections/';
$config['path']['temp'] = '/res/';
$config['path']['avatar'] = '/avatars/';
$config['path']['include'] = '/include/';

$config['pulse']['frequency'] = 20;
$config['pulse']['max_diff'] = 10;

/* Параметры базы данных */

//Хост
$config['db']['host'] = 'localhost';
//Имя пользователя
$config['db']['user'] = 'root';
//Пароль
$config['db']['pass'] = '71295';
//Имя базы
$config['db']['db'] = 'planetsbook';
//Кодировка запросов
$config['db']['charset'] = 'utf8';
//Указывает поведение функций при ошибках. TRUE - выбрасывает исключение, FALSE - возвращает FALSE
$config['db']['throwable'] = TRUE;

/* Параметры страниц */

//Кодировка страницы
$config['page_charset'] = 'utf-8';
