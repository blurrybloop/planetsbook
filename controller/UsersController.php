<?php

require_once 'MenuController.php';

class UsersController extends MenuController
{
    public $mode;

    function validateParam($paramName, $value){
        if ($paramName == 'login'){
            if (!preg_match('#^[\w]{5,100}$#', $value)) throw new ControllerException('Неправильный формат логина.');
        }
        else if ($paramName == 'password'){
            if (!preg_match('#^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\\\]{6,}$#', $value)) throw new ControllerException('Неправильный формат пароля.');
        }
        else if ($paramName == 'email'){
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) throw new ControllerException('Неправильный формат электронной почты');
            $parts = explode("@",$value);
            $domain = array_pop($parts);
            if (!checkdnsrr($domain)) throw new ControllerException('Сервер электронной почты не существует.');
        }
        else if ($paramName == 'real_name'){
            if (!preg_match('#^[A-Za-zА-ЯЁІЇЄа-яёіїє\s]+$#u', $value)) throw new ControllerException('Неправильный формат имени.'); 
        }
        else if ($paramName == 'skype') {
            if (!preg_match('#^[\w]{1,50}$#u', $value)) throw new ControllerException('Неправильный формат имени пользователя Skype.'); 
        }
        else if ($paramName == 'vk' || $paramName == 'facebook' || $paramName == 'twitter') {
            if (!preg_match('#^[\w]{1,100}$#u', $value)) throw new ControllerException('Неправильный формат имени пользователя ' . $paramName . '.'); 
        }
        else if ($paramName == 'site'){
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$value)) throw new ControllerException('Неправильный формат URL.<br/>Убедитесь, что адрес включает в себя название протокола (http/https/ftp).'); 
        }
        else if ($paramName == 'from_where'){
            if (!preg_match("#^.{1,150}$#",$value)) throw new ControllerException('Неправильный формат поля "Откуда".'); 
        }
    }

    function checkUser($login, $password){
        $sql = 'SELECT id FROM users ' . 
               'WHERE login=' . $this->db->escapeString($login) . 
               'AND psw_hash=' . $this->db->escapeString(crypt($password, $login));

        $res = $this->db->fetch($sql);
        if ($res === FALSE) throw new ControllerException($this->db->last_error());
        if (count($res) == 0) throw new ControllerException('Неправильный логин и/или пароль.');
        return $res[0]['id'];
    }

    function register(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');
        $this->validateParam('login', $_POST['login']);
        $this->validateParam('password', $_POST['password']);
        if (!empty($_POST['email'])) $this->validateParam('email', $_POST['email']);
        if (!empty($_POST['real_name'])) $this->validateParam('real_name', $_POST['real_name']);
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
        $this->validateParam('login', $_POST['login']);
        $this->validateParam('password', $_POST['password']);

        $id = $this->checkUser($_POST['login'], $_POST['password']);

        $this->db->query('UPDATE users SET last_visit=now() WHERE id=' . $id);

        $_SESSION['user_id'] = $id;
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
        $this->mode = 1;
    }

    function edit($id, $update = FALSE){
        parent::validateRights([$id]);
        if ($update){
            sleep(2);
            if (!empty($_POST['old_psw'])) {
                $this->validateParam('password', $_POST['old_psw']);
                if (empty($_POST['new_psw']) || $this->validateParam('password', $_POST['new_psw'])) throw new ControllerException('Неверный формат пароля.');
                $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
                if (!$res) throw new ControllerException('Произошла ошибка.<br/>Попробуйте повторить действие позже', $this->db->last_error());
                $this->checkUser($this->data['user']['login'], $_POST['old_psw']);
            }
            else if (!empty($_POST['new_psw']))throw new ControllerException('Для выполнения действия требуется старый пароль');
            foreach ($_POST as $key => $value){
                if (!empty($value))
                    $this->validateParam($key, $value);
            }

            $values = [];
            foreach ($_POST as $key => $value){
                if ($key != 'new_psw' && $key != 'email' && $key != 'real_name' && $key != 'skype' && $key != 'vk'  && $key != 'facebook' && $key != 'twitter' && $key != 'site' && $key != 'from_where') continue;
                if ($key == 'new_psw') {
                    if (!empty($value)) $values['psw_hash'] = crypt($value, $this->data['user']['login']);
                    continue;
                }
                $values[$key] = empty($value) ? NULL : $value;
            }
            if (!$this->db->update('users', $values, ['id' => $id]))
                throw new ControllerException('Произошла ошибка.<br/>Попробуйте повторить действие позже', $this->db->last_error());
        }
        else{
            parent::process();
            $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
            if (!$res) throw new ControllerException('Произошла ошибка при загрузке профиля.<br/>Попробуйте повторить действие позже', $this->db->last_error());
            $this->data['profile'] = $res[0];
            $this->mode = 2;
        }
    }

    function process(){
        
        //if (!is_array($args = $_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        $action = strtolower($_REQUEST['param1']);
        if ($action == 'profile' || $action == 'edit'){
            if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) throw new ControllerException('Неправильные параметры запроса');
            if ($action == 'edit' && isset($_REQUEST['update']) && $_REQUEST['update']==1)
                $this->$action($_REQUEST['id'], TRUE);
            else
                $this->$action($_REQUEST['id']);
        }
        else $this->$action();
    }

    function render(){
        if ($this->mode){
            $this->data['menu'] = parent::render();
            $this->renderView('profile');
        }
    }
}
