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

        if (!$this->db->fetch('SELECT id FROM sections WHERE id=' . $_POST['section_id'] . (empty($this->data['user']['is_admin']) ? ' AND allow_user_articles=1' : '')))
            throw new ControllerException('Невозможно создать статью в этом разделе.<br/>Возможно, он не существует, или у вас нет прав на публикацию в нем.');


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

    function addSection(){
        $this->validateRights([USER_ADMIN]);
        if (!isset($_POST['title']) || 
            !isset($_POST['description']) || 
            !isset($_POST['data_folder']) || 
            !isset($_POST['cat_id']) ||
            !is_numeric($_POST['cat_id']) || 
            !isset($_POST['big_image_action']) || 
            !is_numeric($_POST['big_image_action']) || 
            !isset($_POST['small_image_action']) ||
            !is_numeric($_POST['small_image_action']))
                throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if ($_POST['cat_id'] == 2 && (!isset($_POST['planet_id']) || !is_numeric($_POST['planet_id'])))
            throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if (($_POST['big_image_action'] == 1 && empty($_POST['big_image_path'])) || ($_POST['small_image_action'] == 1 && empty($_POST['small_image_action'])))
           throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if ($_POST['big_image_action'] == 0 && $_POST['small_image_action'] == 0)
            throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');

        if (!preg_match('#^.{1,50}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
        if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
        if (!preg_match('#^\w{1,255}$#', $_POST['data_folder'])) throw new ControllerException('Неправильный формат папки.');

        $_POST['description'] = strip_tags(trim($_POST['description']));

        if (preg_match('#^.+$#m', $_POST['description'], $match)){
            $_POST['description'] = '<h3>' . $match[0] . '</h3>' . preg_replace('#^(.+)$#m', '$1<br/>', substr($_POST['description'], strlen($match[0])));
        }

         $_POST['data_folder'] = strip_tags(trim($_POST['data_folder']));

         $_POST['show_main'] = (!empty($_POST['show_main']) && strtolower($_POST['show_main']) == 'on') ? 1 : 0;
         $_POST['allow_user_articles'] = (!empty($_POST['allow_user_articles']) && strtolower($_POST['allow_user_articles']) == 'on') ? 1 : 0;

         $section_folder = $_SERVER['DOCUMENT_ROOT'] . '/sections/' . $_POST['data_folder'];
         if (!@mkdir($section_folder))
             throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
       
         if ($_POST['big_image_action'] == 1){
            require_once '/include/GDExtensions.php';
            if (!GDExtensions::fitToRect($_SERVER['DOCUMENT_ROOT'] . $_POST['big_image_path'], 500, 500, $section_folder . '/main', TRUE))
            {   
                @rmdir($section_folder);
                throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());
            }

            if ($_POST['small_image_action'] == 0) {
                $_POST['small_image_action'] = 1;
                $_POST['small_image_path'] = $_POST['big_image_path'];
            }
         }

        if ($_POST['small_image_action'] == 1){
            require_once '/include/GDExtensions.php';
            if (!GDExtensions::fitToRect($_SERVER['DOCUMENT_ROOT'] . $_POST['small_image_path'], 25, 25, $section_folder . '/main_small', TRUE))
            {   
                @rmdir($section_folder);
                throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());
            }
         }

         $this->db->insert('sections', [
                'title'                 =>      strip_tags(trim($_POST['title'])),
                'description'           =>      $_POST['description'],
                'data_folder'           =>      strip_tags(trim($_POST['data_folder'])),
                'creator_id'            =>      $this->data['user']['id'],
                'parent_id'             =>      $_POST['cat_id'] == 2 ? $_POST['planet_id'] : NULL,
                'type'                  =>      $_POST['cat_id'],
                'show_main'             =>      $_POST['show_main'],
                'allow_user_articles'   =>      $_POST['allow_user_articles'],
             ]);

         $this->data['section_id'] = $this->db->lastInsertId();
    }


    function publicate(){
        $res = $this->db->fetch('SELECT id, title FROM sections' . ($this->data['user']['is_admin'] ? '' : ' WHERE allow_user_articles=1'));
        $this->data['sections'] = $res;
    }

    function sections(){
        $this->showErrorPage = TRUE;
        $this->validateRights([USER_ADMIN]);
        $res = $this->db->fetch('SELECT id, title FROM sections WHERE type=1');
        $this->data['planets'] = $res;
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
        else if ($this->action == 'addsection'){
            echo '/admin/publicate/?section=' . $this->data['section_id'];
        }
        else{
            $this->data['menu'] = parent::render();
            $this->renderView('admin_' . $this->action);
        }
    }
}
