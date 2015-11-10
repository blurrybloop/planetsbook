<?php

require_once 'MenuController.php';

class AdminController extends MenuController
{
    public $action;

    function __construct($db, array $data = NULL){
        parent::validateRights([USER_REGISTERED]);
        parent::__construct($db, $data);
    }

    function messages(){
        $this->showErrorPage = TRUE;
        parent::validateRights([USER_ADMIN]);
        $res = $this->db->fetch('SELECT sections.title as section_title, data_folder, articles.id AS article_id, articles.title as article_title, users.id as user_id, login, DATE_FORMAT(pub_date, \'%e.%m.%Y %H:%i\') AS pub_date FROM articles INNER JOIN users ON articles.author_id=users.id INNER JOIN sections ON sections.id = articles.author_id WHERE verifier_id IS NULL');
        $this->data['messages'] = $res;
    }

    function preview($text){
        require_once '/include/TagsParser.php';
        $parser = new TagsParser(strip_tags($text));
        $this->data['parsed_text'] = $parser->parse();
    }

    function addArticle(){
        if (!isset($_POST['section_id']) || !is_numeric($_POST['section_id']) || !isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['contents']))
            throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if (!preg_match('#^.{1,100}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
        if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
        if (!preg_match('#^.+$#m', $_POST['contents'])) throw new ControllerException('Неправильный формат содержания.');
        require_once '/include/TagsParser.php';

        $parser = new TagsParser(strip_tags(trim($_POST['contents'])));

        $fields = [
                'section_id' => $_POST['section_id'],
                'author_id' => $this->data['user']['id'],
                'title' => strip_tags($_POST['title']),
            ];

        if ($this->data['user']['is_admin']) $fields['verifier_id'] = $this->data['user']['id'];

        $this->db->insert('articles', $fields);
        $lid = $this->db->lastInsertId();

        if (!($res = $this->db->fetch('SELECT data_folder FROM sections WHERE id=' . $_POST['section_id'])))
            throw new ControllerException('Статья не существует.');

        $article_path = "{$_SERVER['DOCUMENT_ROOT']}/sections/{$res[0]['data_folder']}/$lid";

        mkdir($article_path);

        file_put_contents($article_path . '/description.txt' , preg_replace('#^(.+)$#m', '<p>$1</p>', strip_tags(trim($_POST['description']))));

        $parsed = $parser->parse();

        $parsed = preg_replace_callback('#(?<=<img src=")/res/\d+_\d+\.\w+(?=")#', function($match) use($article_path){
            $newfile = $article_path . strrchr($match[0], '/');
            rename($_SERVER['DOCUMENT_ROOT'] . $match[0], $newfile);
            return str_replace($_SERVER['DOCUMENT_ROOT'], '', $newfile);
        }, $parsed);

        file_put_contents($article_path . '/text.txt', $parsed);

        $this->data['article_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $article_path) . '/';
    }

    function publicate(){
        $res = $this->db->fetch('SELECT id, title FROM sections' . ($this->data['user']['is_admin'] ? '' : ' WHERE allow_user_articles=1'));
        $this->data['sections'] = $res;
    }

    function uploadImg($page_id = 0){
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/res/';

        if (empty($page_id)){
            $page_id = str_replace('.', '', $_SERVER['REQUEST_TIME_FLOAT']);
            $this->db->query('INSERT INTO temp_pages VALUES (' . $page_id . ', NOW())');
        }

        foreach ($_FILES['images']['name'] as $i => $name){
           
            if ($_FILES['images']['error'][$i] != 0)
                continue;

            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i]))
                continue;

            if (!getimagesize($_FILES['images']['tmp_name'][$i]))
                continue;

            $now = time();
            while (file_exists($upload_filename = $upload_dir . $page_id . '_' .  $now . '.png'))
                $now++;
            
            move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_filename);

            //$_SESSION['temp_images'][] = $upload_filename;
            echo '<div class="page_id">' . $page_id . '</div>';
            echo '<div class="path">' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $upload_filename) . '</div>';
        }
    }

    function removeImg($path){
        if (is_string($path)){
            $path = $_SERVER['DOCUMENT_ROOT'] . $path;

            if (($i = array_search($path, $_SESSION['temp_images'])) !== FALSE && file_exists($path)){
                if (!@unlink($path))
                    throw new ControllerException('Не удалось удалить изображение</br>Повторите действие позже');
                unset($_SESSION['temp_images'][$i]);
            }
        }
    }

    function process(){
        parent::process();
        if (empty($_REQUEST['param1'])) $this->action = 'messages';
        else  $this->action = strtolower($_REQUEST['param1']);
        $args = [];
        if (isset($_REQUEST['args'])) $args = (array)$_REQUEST['args'];
        call_user_func_array(get_class($this) . '::' . $this->action, $args);
    }

    function render(){
        if ($this->action == 'preview'){
            echo $this->data['parsed_text'] . '<div class="clearfix"></div>';
        }
        else if ($this->action == 'addarticle'){
            echo $this->data['article_path'];
        }
        else if ($this->action == 'uploadimg' || $this->action == 'removeimg'){
            
        }
        else{
            $this->data['menu'] = parent::render();
            $this->renderView('admin_' . $this->action);
        }
    }
}
