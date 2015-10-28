<?php

require_once 'MenuController.php';

class UsersController extends MenuController
{
    private $outPage = FALSE;

    function register(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');
        if (!preg_match('#^[\w]{5,100}$#', $_POST['login'])) throw new ControllerException('Неправильный формат логина.');
        if (!preg_match('#^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\\\]{6,}$#', $_POST['password'])) throw new ControllerException('Неправильный формат пароля.');
        if (!empty($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) throw new ControllerException('Неправильный формат электронной почты');
            $parts = explode("@",$_POST['email']);
            $domain = array_pop($parts);
            if (!checkdnsrr($domain)) throw new ControllerException('Сервер электронной почты не существует.');
        }
        if (!empty($_POST['real_name']) && !preg_match('#^[A-Za-zА-ЯЁІЇЄа-яёіїє\s]+$#u', $_POST['real_name'])) throw new ControllerException('Неправильный формат имени.'); 
        $res = $this->db->insert('users', 
            [
                'login'         =>      $_POST['login'], 
                'psw_hash'      =>      crypt($_POST['password'], $_POST['login']),
                'email'         =>      empty($_POST['email']) ? NULL : $_POST['email'],
                'real_name'     =>      empty($_POST['real_name']) ? NULL : $_POST['real_name'],
            ]);

        if (!$res) {
            if ($this->db->last_error_code() == 1062) throw new ControllerException('Логин уже занят.');
            else throw new ControllerException($this->db->last_error());
        }
    }

    function login(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');
        if (!preg_match('#^[\w]{5,100}$#', $_POST['login'])) throw new ControllerException('Неправильный формат логина.');
        if (!preg_match('#^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\\\]{6,}$#', $_POST['password'])) throw new ControllerException('Неправильный формат пароля.');

        $sql = 'SELECT id FROM users ' . 
               'WHERE login=' . $this->db->escapeString($_POST['login']) . 
               'AND psw_hash=' . $this->db->escapeString(crypt($_POST['password'], $_POST['login']));

        $res = $this->db->fetch($sql);
        if ($res === FALSE) throw new ControllerException($this->db->last_error());
        if (count($res) == 0) throw new ControllerException('Неправильный логин и/или пароль.');

        $this->db->query('UPDATE users SET last_visit=now() WHERE id=' . $res[0]['id']);

        $_SESSION['user_id'] = $res[0]['id'];
        $_SESSION['login_success'] = 1;
    }

    function logout(){
        if (isset($_SESSION['user_id']))
            $this->db->query('UPDATE users SET last_visit=now() WHERE id=' . $_SESSION['user_id']);
        unset($_SESSION['user_id']);
        $_SESSION['logout_success'] = 1;
    }

    function profile($id){
        parent::process();
        $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
        if (!$res) throw new ControllerException('Произошла ошибка при загрузке профиля.<br/>Попробуйте повторить действие позже', $this->db->last_error());
        $this->data['profile'] = $res[0];
        $this->outPage = TRUE;
    }

    function process(){
        
        //if (!is_array($args = $_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        $action = strtolower($_REQUEST['param1']);
        if ($action == 'profile'){
            if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) throw new ControllerException('Неправильные параметры запроса');
            $this->$action($_REQUEST['id']);
        }
        else $this->$action();
    }

    function render(){
        if ($this->outPage){
            $this->data['menu'] = parent::render();
            $this->renderView('profile');
        }
    }
}
