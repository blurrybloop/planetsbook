<?php

require_once 'MenuController.php';

const OUT_NONE = 0;
const OUT_SHOW_PROFILE = 1;
const OUT_EDIT_PROFILE = 2;

class UsersController extends MenuController
{
    public $outputMode = OUT_NONE;

    function setActions(){
        $this->actions = ['register', 'login', 'logout', 'profile', 'edit'];
    }

    function validateParam($paramName, $value){
        if ($paramName == 'login'){
            if (!preg_match('#^[A-Za-z0-9_]{5,100}$#', $value)) throw new ControllerException('Неправильный формат логина.');
        }
        else if ($paramName == 'password'){
            if (!preg_match('#^[A-Za-z0-9_\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\\\]{6,}$#', $value)) throw new ControllerException('Неправильный формат пароля.');
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
            if (!preg_match('#^[A-Za-z0-9_]{1,50}$#', $value)) throw new ControllerException('Неправильный формат имени пользователя Skype.'); 
        }
        else if ($paramName == 'vk' || $paramName == 'facebook' || $paramName == 'twitter') {
            if (!preg_match('#^[A-Za-z0-9_]{1,100}$#u', $value)) throw new ControllerException('Неправильный формат имени пользователя ' . $paramName . '.'); 
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

        if (!($res = $this->db->fetch($sql)))
            throw new ControllerException('Неправильный логин и/или пароль.');
        return $res[0]['id'];
    }

    function register(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');
        $this->validateParam('login', $_POST['login']);
        $this->validateParam('password', $_POST['password']);
        if (!empty($_POST['email'])) $this->validateParam('email', $_POST['email']);
        if (!empty($_POST['real_name'])) $this->validateParam('real_name', $_POST['real_name']);

        try {
            $this->db->insert('users', 
                [
                    'login'         =>      $_POST['login'], 
                    'psw_hash'      =>      crypt($_POST['password'], $_POST['login']),
                    'email'         =>      empty($_POST['email']) ? NULL : $_POST['email'],
                    'real_name'     =>      empty($_POST['real_name']) ? NULL : $_POST['real_name'],
                ]);
        }
        catch (DatabaseException $ex){
            if ($ex->getCode() == 1062) throw new ControllerException('Логин уже занят.');
            else throw $ex;
        }
    }

    function login(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');

        $this->validateParam('login', $_POST['login']);
        $this->validateParam('password', $_POST['password']);

        $id = $this->checkUser($_POST['login'], $_POST['password']);

        try {$this->db->query('UPDATE users SET last_visit=now() WHERE id=' . $id);}
        catch (DatabaseException $ex) {} 

        $_SESSION['user_id'] = $id;
        $_SESSION['login_success'] = 1;
    }

    function logout(){
        if (isset($this->data['user'])){
            try {$this->db->query('UPDATE users SET last_visit=now() WHERE id=' . $this->data['user']['id']);}
            catch (DatabaseException $ex) {} 
        }
        unset($_SESSION['user_id']);
        $_SESSION['logout_success'] = 1;
    }

    function profile(){
        $this->showErrorPage = TRUE;
        $this->validateArgs($_GET, [['id', 'numeric']]);
        $id = $_GET['id'];

        parent::process('');
        if (!($res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id)))
            throw new ControllerException('Пользователь не существует.');
        $this->data['profile'] = $res[0];
        $this->outputMode = OUT_SHOW_PROFILE;
    }

    function edit(){
        $this->validateArgs($_GET, [['id', 'numeric']]);
        $id = $_GET['id'];

        $update = isset($_GET['update']) ? $_GET['update'] : 0;

        if ($update){
            $this->validateRights([$id]);

            if (!empty($_POST['old_psw'])) {
                $this->validateParam('password', $_POST['old_psw']);
                if (empty($_POST['new_psw']) || $this->validateParam('password', $_POST['new_psw'])) throw new ControllerException('Неверный формат пароля.');
                if (!($res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id)))
                    throw new ControllerException('Пользователь не существует.');
                $this->checkUser($this->data['user']['login'], $_POST['old_psw']);
            }
            else if (!empty($_POST['new_psw'])) 
                throw new ControllerException('Для выполнения действия требуется старый пароль.');

            foreach ($_POST as $key => $value){
                if (!empty($value))
                    $this->validateParam($key, $value);
            }

            $values = [];
            foreach ($_POST as $key => $value){
                if (!in_array($key, ['new_psw', 'email', 'real_name', 'skype', 'vk', 'facebook', 'twitter', 'site', 'from_where', 'avatar_action'])) 
                    continue;
                
                if ($key == 'avatar_action'){
                    if ($value == 1 && isset($_POST['avatar_path'])){
                        require_once PATH_INCLUDE . 'GDExtensions.php';
                        if (!GDExtensions::fitToRect(PATH_TEMP . pathinfo($_POST['avatar_path'], PATHINFO_BASENAME), 100,100, PATH_AVATAR . $id . '.png'))
                            throw new ControllerException('Произошла ошибка при изменении аватара.<br/>Попробуйте повторить действие позже.', GDExtensions::lastError());
                        $values['avatar'] = $id;
                    }
                    else if ($value == 2){
                        @unlink(PATH_AVATAR . $id . '.png');
                        $values['avatar'] = 0;
                    }
                    continue;
                }
                if ($key == 'new_psw') {
                    if (!empty($value)) $values['psw_hash'] = crypt($value, $this->data['user']['login']);
                    continue;
                }
                $values[$key] = empty($value) ? NULL : $value;
            }
            $this->db->update('users', $values, ['id' => $id]);  
        }
        else{
            $this->showErrorPage = TRUE;
            parent::validateRights([$id]);
            parent::process('');
            if (!($res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id)))
                throw new ControllerException('Пользователь не существует.');
            $this->data['profile'] = $res[0];
            $this->outputMode = OUT_EDIT_PROFILE;
        }
    }

    function process($action){
        $this->showErrorPage = FALSE;
        if (!empty($action)) $this->$action();
    }

    function render(){
        if ($this->outputMode){
            $this->data['menu'] = parent::render();
            $this->renderView('profile');
        }
    }
}
