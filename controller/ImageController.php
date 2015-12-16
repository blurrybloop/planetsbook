<?php

require_once 'ControllerBase.php';

class ImageController extends ControllerBase
{
    public $uploaded = [];
    private $img_errors = [
                        0 => 'Неизвестная ошибка',
                        1 => 'Превышен максимальный размер файла, указанный в php.ini',
                        2 => 'Превышен максимальный размер файла, указанный в форме HTML',
                        3 => 'Была отправлена только часть файла',
                        4 => 'Файл для отправки не был выбран.'];
    public $errors = [];
    public $page_id = 0;

    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->validateRights([USER_REGISTERED]);
        $this->actions = ['upload', 'delete'];
    }

    function processImages($page_id = 0){
        $this->page_id = $page_id;
        $names = [];
        if (empty($page_id)){

            /* id страницы
             * уникальное при условии, что несколько ImageController-ов
             * не запущены одновременно с точностью до миллисекунды
             */

            $ms = (defined(PHP_VERSION_ID) && PHP_VERSION_ID >= 50400 ?
                $_SERVER['REQUEST_TIME_FLOAT'] :
                microtime(true));

            $this->page_id = str_replace('.', '', $ms);
            $this->db->query('INSERT INTO temp_pages VALUES (' . $this->page_id . ',' . $this->data['user']['id'] . ',NOW())');
        }
        else {
            if (!$this->db->fetch('SELECT id FROM temp_pages WHERE id=' . $this->page_id . ' AND user_id=' . $this->data['user']['id']))
                throw new ControllerException('Неправильный идентификатор страницы.');
        }

        $temp_files = glob(PATH_TEMP . $page_id . '_*.*', GLOB_NOSORT);
        $total_size = 0;
        foreach ($temp_files as $tmp) 
            $total_size += filesize($tmp);

        $now = time();

        foreach ($_FILES['images']['name'] as $i => $name){

            if ($_FILES['images']['error'][$i] != 0){
                if (!array_key_exists($_FILES['images']['error'][$i], $this->img_errors)) $k = 0;
                else $k = $_FILES['images']['error'][$i];

                $this->errors[] =  $name . ' - ' . $this->img_errors[$k];
                continue;
            }

            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i])){
                $this->errors[] = $name . ' - Выбранный файл не является загруженным.';
                continue;
            }

            if (!getimagesize($_FILES['images']['tmp_name'][$i])){
                $this->errors[] = $name . ' - Выбранный файл не является допустимым форматом изображения.';
                continue;
            }

            if (filesize($_FILES['images']['tmp_name'][$i]) + $total_size > $this->app->config['temp']['max_user_upload']*1024*1024){
                $this->errors[] = $name . ' - Превышен маскимальный объем загружаемых файлов (' . $this->app->config['temp']['max_user_upload'] . 'МБ).';
                continue;
            }
            $total_size += filesize($_FILES['images']['tmp_name'][$i]);

            do $now++;
            while (file_exists($upload_filename = PATH_TEMP . $this->page_id . '_' .  $now . '.png'));

            $names[] = ['temp_name'         =>      $_FILES['images']['tmp_name'][$i],
                        'permanent_name'    =>      $upload_filename
                        ];
        }
        return $names;
    }

    function upload(){
        $page_id = !empty($_REQUEST['page_id']) && is_numeric($_REQUEST['page_id']) ? $_REQUEST['page_id'] : 0;
        $replace = !empty($_REQUEST['image_replace']);

        $names = $this->processImages($page_id, $replace);

        foreach ($names as $img){
            if (move_uploaded_file($img['temp_name'], $img['permanent_name']))
                $this->uploaded[] = $img['permanent_name'];
        }

        if (!empty($this->uploaded) && $replace){
            $old = array_diff(glob(PATH_TEMP . $page_id . '_*.*', GLOB_NOSORT), $this->uploaded);
            foreach ($old as $file)
                @unlink($file);
        }
    }

    function delete(){

       
    }

    function throwErrors(){
        $txt = '';
        foreach ($this->errors as $err){
            $txt .= '<p>' . $err . '</p>';
        }
        if (!empty($txt)) throw new ControllerException('Не удалось загрузить одно или несколько изображений.', $txt);
    }

    function process($action){
        $this->showErrorPage = FALSE;
        if (!empty($action)) $this->$action();
    }

    function render(){
        if ($this->page_id != 0)
            echo '<div class="page_id">' . $this->page_id . '</div>';
        foreach ($this->uploaded as $img)
            echo '<div class="path">' . $this->app->config['path']['temp'] . pathinfo($img, PATHINFO_BASENAME) . '</div>';
        $this->throwErrors();
    }
}
