<?php

define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/res/');
require_once 'ControllerBase.php';

class ImageController extends ControllerBase
{
    public $uploaded = [];
    private $img_errors = [1 => 'Превышен максимальный размер файла, указанный в php.ini',
                       2 => 'Превышен максимальный размер файла, указанный в форме HTML',
                       3 => 'Была отправлена только часть файла',
                       4 => 'Файл для отправки не был выбран.'];
    public $errors = [];
    public $page_id = 0;

    function __construct($db, array $data = NULL){
        parent::__construct($db, $data);
        $this->validateRights([USER_REGISTERED]);
    }

    function processImages($page_id = 0){
        $names = [];
        if (empty($page_id)){
            $page_id = str_replace('.', '', $_SERVER['REQUEST_TIME_FLOAT']);
            $this->db->query('INSERT INTO temp_pages VALUES (' . $page_id . ',' . $this->data['user']['id'] . ',NOW())');
        }
        else {
            if (!$this->db->fetch('SELECT id FROM temp_pages WHERE id=' . $page_id . ' AND user_id=' . $this->data['user']['id']))
                throw new ControllerException('Неправильный идентификатор страницы.');
        }

        $this->page_id = $page_id;
        $now = time();
        foreach ($_FILES['images']['name'] as $i => $name){

            if ($_FILES['images']['error'][$i] != 0){
                $this->errors[] =  $this->img_errors[$_FILES['images']['error'][$i]];
                continue;
            }

            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i])){
                $this->errors[] = 'Выбранный файл не является загруженным.';
                continue;
            }

            if (!getimagesize($_FILES['images']['tmp_name'][$i])){
                $this->errors[] = 'Выбранный файл не является допустимым форматом изображения.';
                continue;
            }
            do $now++;
            while (file_exists($upload_filename = UPLOAD_DIR . $page_id . '_' .  $now . '.png'));
                    
            $names[] = ['temp_name'         =>      $_FILES['images']['tmp_name'][$i], 
                        'permanent_name'    =>      $upload_filename
                        ];
        }
        return $names;
    }

    function upload($page_id = 0, $replace = FALSE){
        $names = $this->processImages($page_id, $replace);

        foreach ($names as $img){
            if (move_uploaded_file($img['temp_name'], $img['permanent_name']))
                $this->uploaded[] = $img['permanent_name'];
        }

            if (!empty($this->uploaded) && $replace){
                $old = array_diff(glob(UPLOAD_DIR . $page_id . '_*.*', GLOB_NOSORT), $this->uploaded);
                foreach ($old as $file)
                    @unlink($file);
            }
        $this->throwErrors();
    }

    function delete($path){
        
        if (is_string($path)){
            $info = pathinfo($path);
            if ($info['dirname'] . '/' != str_replace($_SERVER['DOCUMENT_ROOT'], '', UPLOAD_DIR))
                throw new ControllerException('Не удалось удалить изображение</br>Повторите действие позже');
            if (!preg_match('#^\d+(?=_\d+$)#', $info['filename'], $match))
                throw new ControllerException('Не удалось удалить изображение</br>Повторите действие позже');

            if (!$this->db->fetch('SELECT id FROM temp_pages WHERE id=' . $match[0] . ' AND user_id=' . $this->data['user']['id']))
                throw new ControllerException('Не удалось удалить изображение</br>Повторите действие позже');

            $path = $_SERVER['DOCUMENT_ROOT'] . $path;
            if (!@unlink($path))
                throw new ControllerException('Не удалось удалить изображение</br>Повторите действие позже');
        }
    }

    //function fit($page_id, $width, $height){
    //    $names = $this->processImages($page_id);
    //    require_once '/include/GDExtensions.php';
    //    foreach ($names as $img){
    //        if (($file = GDExtensions::fitToRect($img['temp_name'], $width, $height, $img['permanent_name'], TRUE)) !== FALSE)
    //            $this->uploaded[] = $file;
    //        else $this->errors[] = GDExtensions::lastError();
    //        @unlink($img['temp_name']);
    //    }
    //    $this->throwErrors();
    //}

    function throwErrors(){
        $txt = '';
        foreach ($this->errors as $err){
            $txt .= '<p>' . $err . '</p>';
        }
        if (!empty($txt)) throw new ControllerException('Не удалось загрузить одно или несколько изображений.', $txt);
    }

    function process(){
        //ob_start();
        //var_dump($_REQUEST);
        //throw new ControllerException(ob_get_clean());
        if (!isset($_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
       // if (!is_array($args = $_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        $args = (array)$_REQUEST['args'];
        $action = strtolower($_REQUEST['param1']);
        call_user_func_array(get_class($this) . '::' . $action, $args);
    }

    function render(){
        echo '<div class="page_id">' . $this->page_id . '</div>';
        foreach ($this->uploaded as $img)
            echo '<div class="path">' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $img) . '</div>';
    }
}
