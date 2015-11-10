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
        if (count($res) == 0) throw new ControllerException('Неправильный логин и/или пароль.');
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
        $this->showErrorPage = TRUE;
        parent::process();
        $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
        if (!$res) throw new ControllerException('Пользователь не существует.');
        $this->data['profile'] = $res[0];
        $this->mode = 1;
    }

    function edit($id, $update = FALSE){
        if ($update){
            parent::validateRights([$id]);
            if (!empty($_POST['old_psw'])) {
                $this->validateParam('password', $_POST['old_psw']);
                if (empty($_POST['new_psw']) || $this->validateParam('password', $_POST['new_psw'])) throw new ControllerException('Неверный формат пароля.');
                $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
                if (!$res) throw new ControllerException('Пользователь не существует.');
                $this->checkUser($this->data['user']['login'], $_POST['old_psw']);
            }
            else if (!empty($_POST['new_psw'])) throw new ControllerException('Для выполнения действия требуется старый пароль');
            foreach ($_POST as $key => $value){
                if (!empty($value))
                    $this->validateParam($key, $value);
            }

            $values = [];
            foreach ($_POST as $key => $value){
                if ($key != 'new_psw' && $key != 'email' && $key != 'real_name' && $key != 'skype' && $key != 'vk'  && $key != 'facebook' && $key != 'twitter' && $key != 'site' && $key != 'from_where' && $key != 'avatar_action') continue;
                if ($key == 'avatar_action'){
                    if ($value == 1){
                        if (@rename($_SERVER['DOCUMENT_ROOT'] . '/tmp_avatar/' . $id . '.png', $_SERVER['DOCUMENT_ROOT'] . '/avatars/' . $id . '.png') === FALSE)
                            throw new ControllerException('Произошла ошибка при изменении аватара.<br/>Попробуйте повторить действие позже.');
                        $values['avatar'] = $id;
                    }
                    else if ($value == 2){
                        @unlink($_SERVER['DOCUMENT_ROOT'] . '/avatars/' . $id . '.png');
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
            parent::process();
            $res = $this->db->fetch('SELECT id, login, is_admin, email, real_name, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date, DATE_FORMAT(last_visit, \'%e.%m.%Y %H:%i\') AS last_visit, avatar, rating, comments_cnt, skype, vk, facebook, twitter, site, from_where FROM users WHERE id=' . $id);
            if (!$res) throw new ControllerException('Пользователь не существует.');
            $this->data['profile'] = $res[0];
            $this->mode = 2;
        }
    }

    function uploadImg($id){
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/tmp_avatar/';

        $php_errors = [1 => 'Превышен максимальный размер файла, указанный в php.ini',
                       2 => 'Превышен максимальный размер файла, указанный в форме HTML',
                       3 => 'Была отправлена только часть файла',
                       4 => 'Файл для отправки не был выбран.'];
        
        if (!isset($_FILES['avatar']))
            throw new ControllerException('Сервер не может получить выбранный вами файл.', trim($php_errors[4], '.') . ' или ' . $php_errors[1]);

        if ($_FILES['avatar']['error'] != 0)
            throw new ControllerException('Сервер не может получить выбранный вами файл.', $php_errors[$_FILES['avatar']['error']]);

        if (!is_uploaded_file($_FILES['avatar']['tmp_name']))
            throw new ControllerException("Файл не является загруженным.", $php_errors[$_FILES['avatar']['name']]);

        if (!getimagesize($_FILES['avatar']['tmp_name']))
            throw new ControllerException("Вы выбрали файл, который не является изображением.", $_FILES['avatar']['name'] . ' не является настоящим файлом изображения.');

        $upload_filename = $upload_dir . $id;

        require_once '/include/GDExtensions.php';

        if (($path = GDExtensions::fitToRect($_FILES['avatar']['tmp_name'], 100, 100, $upload_filename, TRUE)) === FALSE)
            throw new ControllerException('Возникла проблема сохранения вашего изображения.', GDExtensions::lastError());

        echo '<div id="path">' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path) . '</div>';
    }

    function process(){
        $action = strtolower($_REQUEST['param1']);
        if ($action == 'profile' || $action == 'edit' || $action == 'uploadimg'){
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
