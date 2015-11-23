<?php

require_once 'MenuController.php';

class AdminController extends MenuController
{
    public $action;

    function __construct($db, array $data = NULL){
        parent::validateRights([USER_REGISTERED]);
        parent::__construct($db, $data);
    }
   
    private function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
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
	if (!isset($_POST['article_action']) ||
            !in_array($_POST['article_action'], ['add', 'edit']))
		throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');
	if ($_POST['article_action'] == 'edit'){
		$this->validateRights([USER_ADMIN]);
		if (!isset($_POST['article_id']) || !is_numeric($_POST['article_id']))
			throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');
	}

        if (!isset($_POST['section_id']) || !is_numeric($_POST['section_id']) || !isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['contents']))
            throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if (!$this->db->fetch('SELECT id FROM sections WHERE id=' . $_POST['section_id'] . (empty($this->data['user']['is_admin']) ? ' AND allow_user_articles=1' : '')))
            throw new ControllerException('Невозможно создать/изменить статью в этом разделе.<br/>Возможно, он не существует, или у вас нет прав на публикацию в нем.');


        if (!preg_match('#^.{1,100}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
        if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
        if (!preg_match('#^.+$#m', $_POST['contents'])) throw new ControllerException('Неправильный формат содержания.');
        require_once '/include/TagsParser.php';

        $text = strip_tags(trim($_POST['contents']));
        $parser = new TagsParser($text);

        if ($_POST['article_action'] == 'edit'){
            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $_POST['article_id'])))
                throw new ControllerException('Раздел не существует.');
            $old_folder = "{$_SERVER['DOCUMENT_ROOT']}/sections/{$res[0]['data_folder']}/{$_POST['article_id']}";
        }

        $fields = [
                'section_id' => $_POST['section_id'],
                'title' => strip_tags($_POST['title']),
                'verifier_id' => $this->data['user']['is_admin'] ? $this->data['user']['id'] : NULL,
            ];

        if ($_POST['article_action'] == 'add') $fields['author_id'] = $this->data['user']['id'];
        
        if (!($res = $this->db->fetch('SELECT data_folder FROM sections WHERE id=' . $_POST['section_id'])))
            throw new ControllerException('Раздел не существует.');
	
	if ($_POST['article_action'] == 'add'){
        	$this->db->insert('articles', $fields);
            $article_path = "{$_SERVER['DOCUMENT_ROOT']}/sections/{$res[0]['data_folder']}/" . $this->db->lastInsertId();
	}
	else {
        $article_path = "{$_SERVER['DOCUMENT_ROOT']}/sections/{$res[0]['data_folder']}/" . $_POST['article_id'];
        if (!rename($old_folder . '/', $article_path . '/'))
            throw new ControllerException('Не удалось изменить папку с данными.<br/>Повторите действие позже.');

		$this->db->update('articles', $fields, ['id' => $_POST['article_id']]);
	}

	if (!file_exists($article_path)){
        	if (!@mkdir($article_path))
			    throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
	}

        file_put_contents($article_path . '/description.txt' , preg_replace('#^(.+)$#m', '<p>$1</p>', strip_tags(trim($_POST['description']))));

	$used = [];
    if (preg_match_all('#(\[img(?:=[\'"\w\.:\/\\\\&\?\#\-\+\=\*]*)?(?:\swidth=\d+)?(?:\sheight=\d+)?\]\s*)(\d+_\d+\.\w+)(\s*\[\/img\])#', $text, $match)){
        foreach ($match[0] as $i => $m){
            if (!@getimagesize($oldfile = $article_path . '/' . $match[2][$i])) {
                if (!@getimagesize($oldfile = $_SERVER['DOCUMENT_ROOT'] . '/res/' . $match[2][$i])){
                    continue;
                }
            }
            $newfile = $article_path . '/' . $match[2][$i];
            rename($oldfile, $newfile);
            $used[] = $match[2][$i];
        }
    }

	$unused = array_diff(scandir($article_path), $used, ['.', '..']);
	foreach ($unused as $u){
		if (@getimagesize($article_path . '/' . $u)){
			@unlink($article_path . '/' . $u);
		}
	}
	
        file_put_contents($article_path . '/text.txt', $text);

        $this->data['article_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $article_path) . '/';
    }

    function addSection(){
        $this->validateRights([USER_ADMIN]);
        if (!isset($_POST['section_action']) ||
            !in_array($_POST['section_action'], ['add', 'edit']) ||
            !isset($_POST['title']) || 
            !isset($_POST['description']) || 
            !isset($_POST['data_folder']) || 
            !isset($_POST['cat_id']) ||
            !is_numeric($_POST['cat_id']) || 
            !isset($_POST['big_image_action']) || 
            !in_array($_POST['big_image_action'], [0,1,2]) || 
            !isset($_POST['small_image_action']) ||
            !in_array($_POST['small_image_action'], [0,1,2]))
                throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if ($_POST['cat_id'] == 2 && (!isset($_POST['planet_id']) || !is_numeric($_POST['planet_id'])))
            throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if (($_POST['big_image_action'] == 1 && empty($_POST['big_image_path'])) || ($_POST['small_image_action'] == 1 && empty($_POST['small_image_action'])))
           throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

        if ($_POST['section_action'] == 'add' && $_POST['big_image_action'] == 0 && $_POST['small_image_action'] == 0)
            throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');
        else if ($_POST['section_action'] == 'edit' && $_POST['big_image_action'] == 2 && $_POST['small_image_action'] == 2)
            throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');

        if (!preg_match('#^.{1,50}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
        if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
        if (!preg_match('#^\w{1,255}$#', $_POST['data_folder'])) throw new ControllerException('Неправильный формат папки.');

        $_POST['description'] = strip_tags(trim($_POST['description']));

        if (preg_match('#^.+$#m', $_POST['description'], $match)){
            $_POST['description'] = '<h3>' . $match[0] . '</h3>' . preg_replace('#^(.+)$#m', '$1<br/>', substr($_POST['description'], strlen($match[0])));
            $_POST['description'] = preg_replace('#[\r\n]#', '', $_POST['description']);
        }

         $_POST['data_folder'] = strip_tags(trim($_POST['data_folder']));

         $_POST['show_main'] = (!empty($_POST['show_main']) && strtolower($_POST['show_main']) == 'on') ? 1 : 0;
         $_POST['allow_user_articles'] = (!empty($_POST['allow_user_articles']) && strtolower($_POST['allow_user_articles']) == 'on') ? 1 : 0;

         if ($_POST['section_action'] == 'edit'){
             if (!isset($_POST['section_id'])) throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');
             if (!($res = $this->db->fetch('SELECT data_folder from sections WHERE id=' . $_POST['section_id'])))
                 throw new ControllerException('Раздел еще не существует.');
             $section_folder = $_SERVER['DOCUMENT_ROOT'] . '/sections/' . $_POST['data_folder'];
             if (!@rename($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $res[0]['data_folder'], $section_folder))
                 throw new ControllerException('Не удалось переместить папку с данными.<br/>Повторите действие позже.');
         }
         else {
             $section_folder = $_SERVER['DOCUMENT_ROOT'] . '/sections/' . $_POST['data_folder'];
             if (!@mkdir($section_folder))
                 throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
         }
       
         if ($_POST['big_image_action'] == 1){
            require_once '/include/GDExtensions.php';
            if (!GDExtensions::fitToRect($_SERVER['DOCUMENT_ROOT'] . $_POST['big_image_path'], 500, 500, $section_folder . '/main', TRUE))
            {   
                @rmdir($section_folder);
                throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());
            }
         }
         else if ($_POST['big_image_action'] == 2){
             if (!@unlink($section_folder . '/main.png'))
                 throw new ControllerException('Не удалось удалить изображение.', GDExtensions::lastError());
         }

         if ($_POST['section_action'] == 'add'){
             if ($_POST['big_image_action'] == 1 && $_POST['small_image_action'] != 1) {
                 $_POST['small_image_action'] = 1;
                 $_POST['small_image_path'] = $_POST['big_image_path'];
             }
         }
         else {
             if ($_POST['big_image_action'] != 2 && $_POST['small_image_action'] == 2) {
                 $_POST['small_image_action'] = 1;
                 $_POST['small_image_path'] = ($_POST['big_image_action'] == 0 ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $section_folder) . '/main.png' : $_POST['big_image_path']);
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
        else if ($_POST['small_image_action'] == 2){
            if (!@unlink($section_folder . '/main_small.png'))
                throw new ControllerException('Не удалось удалить изображение.', GDExtensions::lastError());
        }

        $values = [
                'title'                 =>      strip_tags(trim($_POST['title'])),
                'description'           =>      $_POST['description'],
                'data_folder'           =>      strip_tags(trim($_POST['data_folder'])),
                'creator_id'            =>      $this->data['user']['id'],
                'parent_id'             =>      $_POST['cat_id'] == 2 ? $_POST['planet_id'] : NULL,
                'type'                  =>      $_POST['cat_id'],
                'show_main'             =>      $_POST['show_main'],
                'allow_user_articles'   =>      $_POST['allow_user_articles'],
             ];

        if ($_POST['section_action'] == 'edit')
            $this->db->update('sections', $values, ['id' => $_POST['section_id']]);
        else
            $this->db->insert('sections', $values);

         $this->data['section_id'] = $this->db->lastInsertId();
    }

    function sections($action = NULL, $id = 0){
        $this->showErrorPage = TRUE;
        $this->validateRights([USER_ADMIN]);

        if (empty($action)){
            $res = $this->db->fetch('SELECT sections.id AS id, title, data_folder, parent_id, DATE_FORMAT(creation_date, "%e.%m.%Y") AS creation_date, users.id AS user_id, login FROM sections INNER JOIN users ON creator_id=users.id');
            foreach ($res as $s){
                $s['data_folder'] = '/sections/' . $s['data_folder'];
                if (!empty($s['parent_id']))
                    $this->data['sections'][$s['parent_id']]['children'][] = $s;
                else $this->data['sections'][$s['id']] = $s;
            }
        }
        else if ($action == 'add'){
            $res = $this->db->fetch('SELECT id, title FROM sections WHERE type=1');
            $this->data['planets'] = $res;
            $this->data['subaction'] = 'add';
        }
        else if ($action == 'edit'){
            if (!is_numeric($id) || $id == 0) throw new ControllerException('Неправильные параметры запроса.');
            $res = $this->db->fetch('SELECT * FROM sections WHERE id=' . $id);
            if (!$res) throw new ControllerException('Этот раздел еще не существует.');
            $this->data['section'] = $res[0];
            
            $this->data['section']['description'] = strip_tags(preg_replace('#(<\/h3>|<br\/>)#', "\n", $this->data['section']['description']));

            $res = $this->db->fetch('SELECT id, title FROM sections WHERE type=1');
            $this->data['planets'] = $res;

            $this->data['subaction'] = 'edit';
        }
        else if ($action == 'delete'){
            if (!is_numeric($id) || $id == 0) throw new ControllerException('Неправильные параметры запроса.');
            if (!($res = $this->db->fetch('SELECT data_folder FROM sections WHERE id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            $this->delTree($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $res[0]['data_folder']);
            $this->db->delete('sections', ['id' => $id]);
            header('Location: /admin/sections/');
        }
    }

    function articles($action = NULL, $id = 0){
        $this->showErrorPage = true;
        if (empty($action)){
            $this->validateRights([USER_ADMIN]);

            if (!isset($_REQUEST['sort']) || $_REQUEST['sort'] == 0 || $_REQUEST['sort'] > 3) $sort_col = 'articles.pub_date DESC';
            else if ($_REQUEST['sort'] == 1) $sort_col = 'articles.views DESC';
            else if ($_REQUEST['sort'] == 2) $sort_col = 'articles.title';
            else $sort_col = 'articles.verifier_id';

            if (!empty($_REQUEST['split'])){
                $sort_col = 'sections.title, ' . $sort_col;
                $this->data['split'] = 1;
            }
            else $this->data['split'] = 0;

            $res = $this->db->fetch('SELECT articles.id AS article_id, if(verifier_id IS NULL, CONCAT("[Не проверено] ",articles.title), articles.title) AS title, DATE_FORMAT(pub_date, "%e.%m.%Y") AS pub_date, verifier_id, views, users.id AS user_id, login, data_folder, sections.title AS section_title FROM articles INNER JOIN users ON articles.author_id = users.id INNER JOIN sections ON articles.section_id = sections.id ORDER BY ' . $sort_col);
            $this->data['articles'] = $res;
            $this->data['splitter_href'] = '?' . (isset($_REQUEST['sort']) ? 'sort=' . $_REQUEST['sort'] . '&' : '') . 'split=' . !$this->data['split'];
        }
        else if ($action == 'add'){
            $res = $this->db->fetch('SELECT id, title FROM sections' . ($this->data['user']['is_admin'] ? '' : ' WHERE allow_user_articles=1'));
            $this->data['sections'] = $res;
            $this->data['subaction'] = 'add';
        }
        else if ($action == 'edit'){
            $this->validateRights([USER_ADMIN]);
            if (!is_numeric($id) || $id == 0) throw new ControllerException('Неправильные параметры запроса.');
            $res = $this->db->fetch('SELECT articles.id AS id, section_id, articles.title AS title, data_folder, verifier_id FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id);
            if (!$res) throw new ControllerException('Этот раздел еще не существует.');
            $this->data['article'] = $res[0];
            $this->data['article']['description'] = file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$this->data['article']['data_folder']}/{$this->data['article']['id']}/description.txt");
            $this->data['article']['description'] = preg_replace('#<p>(.*)<\/p>#U', "$1", $this->data['article']['description']);

            $folder = "{$_SERVER['DOCUMENT_ROOT']}/sections/{$this->data['article']['data_folder']}/{$this->data['article']['id']}/";

            $this->data['article']['contents'] = file_get_contents($folder . 'text.txt');

            $res = $this->db->fetch('SELECT id, title FROM sections WHERE allow_user_articles=1');
            $this->data['sections'] = $res;

            $this->data['article']['images'] = scandir($folder);

            $this->data['article']['images'] = array_map(function($val) use ($folder) {
                return str_replace($_SERVER['DOCUMENT_ROOT'], '',  $folder) . $val;
            }, array_filter($this->data['article']['images'], function($val) use ($folder) {
                return $val != '.' && $val != '..' && @getimagesize($folder . $val);
            }));

            $this->data['subaction'] = 'edit';
        }
        else if ($action == 'deleteimg'){

            $this->showErrorPage = FALSE;
            $this->validateRights([USER_ADMIN]);
            if (!is_numeric($id) || $id == 0 || !isset($_POST['img'])) throw new ControllerException('Неправильные параметры запроса.');
            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $res[0]['data_folder'] . '/' . $id . '/' . $_POST['img']);
            $this->data['subaction'] = 'deleteimg';
        }
        else if ($action == 'delete'){
            $this->showErrorPage = FALSE;
            $this->data['subaction'] = 'delete';
            if (!is_numeric($id) || $id == 0) throw new ControllerException('Неправильные параметры запроса.');
            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id=sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            @$this->delTree($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $res[0]['data_folder'] . '/' . $id);
            $this->db->delete('articles', ['id' => $id]);             
        }
    }

    function process(){        
        parent::process();
        if (empty($_REQUEST['param1'])) $this->action = 'messages';
        else  $this->action = strtolower($_REQUEST['param1']);
        $args = [];
        if (!empty($_REQUEST['param2'])) $args[] = $_REQUEST['param2'];
        if (isset($_REQUEST['args'])) $args = array_merge($args, (array)$_REQUEST['args']);  
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
        else if (isset($this->data['subaction']) && ($this->data['subaction'] == 'deleteimg' || $this->data['subaction'] == 'delete')){
        }
        else{
            $this->data['menu'] = parent::render();
            $this->renderView('admin_' . $this->action);
        }
    }
}
