<?php

require_once 'ControllerBase.php';

class UsersController extends ControllerBase
{
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
        $sql = 'INSERT INTO users(login, psw_hash, email, real_name) VALUES("'  . $_POST['login'] . '", "' . crypt($_POST['password'], $_POST['login']) . '"';
        if (!empty($_POST['email'])) $sql .= ', "' . $_POST['email'] . '"';
        else $sql .= ', NULL';
        if (!empty($_POST['real_name'])) $sql .= ', "' . $_POST['real_name'] . '"';
        else $sql .= ', NULL';
        $sql .= ')';
        if (!$this->db->query($sql)) 
        {
            if ($this->db->last_error_code() == 1062) throw new ControllerException('Логин уже занят.');
            else throw new ControllerException($this->db->last_error());
        }
    }

    function login(){
        if (!isset($_POST['login']) || !isset($_POST['password'])) throw new ControllerException('Не заполнено одно или несколько из обязательных полей.');
        if (!preg_match('#^[\w]{5,100}$#', $_POST['login'])) throw new ControllerException('Неправильный формат логина.');
        if (!preg_match('#^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\\\]{6,}$#', $_POST['password'])) throw new ControllerException('Неправильный формат пароля.');
        $sql = 'SELECT id FROM users WHERE login="' . $_POST['login'] . '" AND psw_hash="' . crypt($_POST['password'], $_POST['login']) . '"';
        $res = $this->db->fetch($sql);
        if ($res === FALSE) throw new ControllerException($this->db->last_error());
        if (count($res) == 0) throw new ControllerException('Неправильный логин и/или пароль.');
        $_SESSION['user_id'] = $res[0]['id'];
    }

    function logout(){
        unset($_SESSION['user_id']);
    }

    function process(){
        //if (!isset($_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        //if (!is_array($args = $_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        $action = strtolower($_REQUEST['param1']);
        $this->$action();
    }

    function render(){
        
    }
}
