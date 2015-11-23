<?php

require_once PATH_INCLUDE . 'ControllerException.php';
require_once PATH_INCLUDE . 'DatabaseException.php';
require_once PATH_INCLUDE . 'HttpException.php';

const USER_ANY = 0;
const USER_REGISTERED = -1;
const USER_ADMIN = -2;

abstract class ControllerBase
{
    public $app;
	public $db;
	public $data = [];
    public $showErrorPage = FALSE;
    public $actions = [];

    function __construct($app, $db, array $data = NULL) {
        $this->app = $app;
        $this->db = $db;
        $this->data =$data;
        $this->showErrorPage = isset($data['showErrorPage']) ? $data['showErrorPage'] : TRUE;
        $this->setActions();
        
        //контроллеры должны обращатся к $data['user'] вместо $_SESSION['user_id']
        if (isset($_SESSION['user_id']))
            if ($res = $this->db->fetch('SELECT id, login, is_admin, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt FROM users WHERE id=' . $_SESSION['user_id']))
                $this->data['user'] = $res[0];
    }

    //проверка посетителя на соответствие указанной группе пользователей
    function validateRights(array $users = NULL, $throw = TRUE){
        if ($users === NULL) $users = [];
        foreach ($users as $user){
            if ($user == USER_ANY) return TRUE; //любой пользователь
            if (!isset($this->data['user'])) continue;
            if ($user == USER_REGISTERED) return TRUE; //зарегистрированый
            if ($user == USER_ADMIN){
                if (!$this->data['user']['is_admin']) continue; //админ
                return TRUE;
            }
            if ($user>0 && $user == $this->data['user']['id']) return TRUE; //конкретный
        }
        if ($throw) throw new ControllerException(!isset($this->data['user']) ? 'Эта функция недоступна гостям.<br/>Зарегистрируйтесь или войдите с помощью существующей учетной записи.' : 'У вас недостаточно прав для выполнения этого действия.');
        else return FALSE;
    }

    function validateArgs(array $args, array $required, $throw = TRUE){
        foreach ($required as $arg){
            if (!isset($args[$arg[0]]) ||
                (isset($arg[1]) && !@call_user_func('is_' . $arg[1], $args[$arg[0]]))) {
                if ($throw) throw new ControllerException($arg[0] . 'Неправильные параметры запроса.');
                else return FALSE;
            }
        }
        return TRUE;
    }

    //методы для реализации
    abstract function setActions();
    abstract function process($action);
    abstract function render();

    //подключение файла вида - для использования в контроллерах
	function renderView($view) {
		include(PATH_VIEW . $view . '.php');
    }

    //запуск контроллера
    function show() {
        try{
            $action = isset($_REQUEST['param1']) ? strtolower($_REQUEST['param1']) : '';
            if (!empty($action) && !empty($this->actions) && !in_array($action, $this->actions))
                throw new ControllerException('Неправильные параметры запроса.');

            $ret = $this->process($action);
        }
        catch (DatabaseException $ex){
            throw new ControllerException('Произошла ошибка.<br/>Повторите действие позже.', $ex);
        }
        if ($ret !== FALSE) $this->render();
    }
}