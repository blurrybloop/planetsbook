<?php

require_once 'MenuController.php';

const IMAGE_NOACTION = 0;
const IMAGE_ADD = 1;
const IMAGE_DELETE = 2;

class AdminController extends MenuController
{
    public $action;

    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->validateRights([USER_REGISTERED]);
    }

    function setActions(){
        $this->actions = ['messages', 'preview', 'sections', 'articles'];
    }

    private function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    private function copyTree($src, $dst) { 
        $dir = opendir($src); 
        if (!@mkdir($dst)) return FALSE; 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->copyTree($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
        return TRUE;
    } 

    function messages(){
        $this->showErrorPage = TRUE;
        parent::validateRights([USER_ADMIN]);
        $res = $this->db->fetch('SELECT sections.title as section_title, data_folder, articles.id AS article_id, articles.title as article_title, users.id as user_id, login, DATE_FORMAT(pub_date, \'%e.%m.%Y %H:%i\') AS pub_date FROM articles INNER JOIN users ON articles.author_id=users.id INNER JOIN sections ON sections.id = articles.section_id WHERE verifier_id IS NULL');
        foreach ($res as &$r){
            $r['href'] = $this->app->config['path']['section'] . $r['data_folder'] . '/' . $r['article_id'];
            $r['description'] = @file_get_contents(PATH_SECTION . $r['data_folder'] . '/' . $r['article_id'] . '/description.txt');
        }
        unset($r);
        $this->data['messages'] = $res;
    }

    //предпросмотр статьи
    function preview(){
        $this->showErrorPage = FALSE;
        $this->validateArgs($_POST, [['text', 'string'], ['images', 'array']]);
        $text = strip_tags(trim($_POST['text']));
        $images = (array)$_POST['images']; //изображения, прикрепленные к статье
        $text = preg_replace_callback('#(\[img(?:=[\'"\w\.:\/\\\\&\?\#\-\+\=\*]*)?(?:\swidth=\d+)?(?:\sheight=\d+)?\]\s*)(\d+_\d+\.\w+)(\s*\[\/img\])#', function($match) use ($images){     
            foreach ($images as $img){
                if (pathinfo($img, PATHINFO_BASENAME) == $match[2]) //это прикреплено, меняем путь таким образом, чтобы браузер мог отобразить картинку
                    return $match[1] . $img . $match[3];
            }
            return $match[0];
        }, $text);

        require_once PATH_INCLUDE . 'TagsParser.php';
        $parser = new TagsParser($text);
        $this->data['parsed_text'] = $parser->parse();
    }

    /**
     * Сохранение статьи
     * Если $id==0 - добавление, иначе сохранение
     */
    function saveArticle($id = 0){
        $this->showErrorPage = FALSE;
	    if ($id) $this->validateRights([USER_ADMIN]);

        $this->validateArgs($_POST, [['section_id', 'numeric'], ['title'], ['description'], ['contents']]);

        //для начала проверим, в какой раздел лезет пользователь
        if (!$this->db->fetch('SELECT id FROM sections WHERE id=' . $_POST['section_id'] . (empty($this->data['user']['is_admin']) ? ' AND allow_user_articles=1' : '')))
            throw new ControllerException('Невозможно создать/изменить статью в этом разделе.<br/>Возможно, он не существует, или у вас нет прав на публикацию в нем.');

        //проверка формата
        if (!preg_match('#^.{1,100}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
        if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
        if (!preg_match('#^.+$#m', $_POST['contents'])) throw new ControllerException('Неправильный формат содержания.');

        $text = strip_tags(trim($_POST['contents'])); //это нам еще понадобится в конце

        //для редактирования нам нужно запомнить старую папку
        if ($id){
            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Раздел не существует.');
            $old_folder = PATH_SECTION . $res[0]['data_folder'] . '/' . $id . '/';
        }

        $fields = [
                'section_id' => $_POST['section_id'],
                'title' => strip_tags($_POST['title']),
                'verifier_id' => $this->data['user']['is_admin'] ? $this->data['user']['id'] : NULL, //если проверка админом
            ];

        if (!$id) $fields['author_id'] = $this->data['user']['id'];

        //путь к папке новго раздела
        if (!($res = $this->db->fetch('SELECT data_folder FROM sections WHERE id=' . $_POST['section_id'])))
            throw new ControllerException('Раздел не существует.');

        if (!$id){
        	$this->db->insert('articles', $fields);
            $article_path = PATH_SECTION . $res[0]['data_folder'] . '/' . $this->db->lastInsertId() . '/';
            if (!file_exists($article_path)){
                if (!@mkdir($article_path))
                    throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
            }
            //в базу добавили, новую папку получили
        }
        else {
            $article_path = PATH_SECTION . $res[0]['data_folder'] . '/' . $id . '/';
            if (!@rename($old_folder, $article_path))
                throw new ControllerException('Не удалось изменить папку с данными.<br/>Повторите действие позже.');

            $this->db->update('articles', $fields, ['id' => $id]); //базу обновили, папку переместили
        }

        file_put_contents($article_path . 'description.txt' , preg_replace('#^(.+)$#m', '<p>$1</p>', strip_tags(trim($_POST['description']))));
        file_put_contents($article_path . 'text.txt', $text);

        //избавляемся от загруженных, но неиспользованных картинок
        $used = [];
        if (preg_match_all('#(\[img(?:=[\'"\w\.:\/\\\\&\?\#\-\+\=\*]*)?(?:\swidth=\d+)?(?:\sheight=\d+)?\]\s*)(\d+_\d+\.\w+)(\s*\[\/img\])#', $text, $match)){
            foreach ($match[0] as $i => $m){
                if (!@getimagesize($oldfile = $article_path . '/' . $match[2][$i])) {
                    if (!@getimagesize($oldfile = PATH_TEMP . $match[2][$i])){
                        continue; //картинки нет ни во временных файлах, ни в папке со статьей
                    }
                }
                $newfile = $article_path . '/' . $match[2][$i];
                rename($oldfile, $newfile); //перемещаем временную картинку в постоянное место хранения
                $used[] = $match[2][$i]; //да, она использована
            }
        }

        //очищаем папку статьи от ненужного хлама
        $unused = array_diff(scandir($article_path), $used, ['.', '..']);
        foreach ($unused as $u){
            if (@getimagesize($article_path . '/' . $u)){
                @unlink($article_path . '/' . $u);
            }
        }

        //готово!

        $this->data['article_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $article_path) . '/';
    }

    function saveSection($id = 0){
        $this->showErrorPage = FALSE;
        try {
            $this->validateRights([USER_ADMIN]);
            $this->validateArgs($_POST, [
                [   'title',                'string'    ], 
                [   'description',          'string'    ],
                [   'data_folder',          'string'    ], 
                [   'cat_id',               'numeric'   ],
                [   'big_image_action',     'numeric'   ],
                [   'small_image_action',   'numeric'   ],
                ]);

            if (!in_array($_POST['big_image_action'], [IMAGE_NOACTION, IMAGE_ADD, IMAGE_DELETE]) || 
                !in_array($_POST['small_image_action'], [IMAGE_NOACTION, IMAGE_ADD, IMAGE_DELETE]))
                throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

            if ($_POST['cat_id'] == 2)
                $this->validateArgs($_POST, [['planet_id', 'numeric']]);

            if (($_POST['big_image_action'] == IMAGE_ADD && empty($_POST['big_image_path'])) || 
                ($_POST['small_image_action'] == IMAGE_ADD && empty($_POST['small_image_action'])))
                throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

            if (!$id && $_POST['big_image_action'] != IMAGE_ADD && $_POST['small_image_action'] != IMAGE_ADD) //нужно добавить хотя бы 1 изображение
                throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');
            else if ($id && $_POST['big_image_action'] == IMAGE_DELETE && $_POST['small_image_action'] == IMAGE_DELETE) //если оба изображения удалены, ничего не останется
                throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');

            if (!preg_match('#^.{1,50}$#', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
            if (!preg_match('#^.+$#m', $_POST['description'])) throw new ControllerException('Неправильный формат описания.');
            if (!preg_match('#^[\w\s\/]{1,255}$#', $_POST['data_folder'])) throw new ControllerException('Неправильный формат папки.');

            $_POST['description'] = strip_tags(trim($_POST['description']));

            if (preg_match('#^.+$#m', $_POST['description'], $match)){ //первая строка будет иметь другой стиль
                $_POST['description'] = '<h3>' . $match[0] . '</h3>' . preg_replace('#^(.+)$#m', '$1<br/>', substr($_POST['description'], strlen($match[0])));
                $_POST['description'] = preg_replace('#[\r\n]#', '', $_POST['description']);
            }

            $_POST['data_folder'] = strip_tags(trim($_POST['data_folder']));

            $_POST['show_main'] = (!empty($_POST['show_main']) && strtolower($_POST['show_main']) == 'on') ? 1 : 0;
            $_POST['allow_user_articles'] = (!empty($_POST['allow_user_articles']) && strtolower($_POST['allow_user_articles']) == 'on') ? 1 : 0;

            if ($id){
                if (!($res = $this->db->fetch('SELECT data_folder from sections WHERE id=' . $id)))
                    throw new ControllerException('Раздел еще не существует.');
                $old_folder = PATH_SECTION . $res[0]['data_folder'];

                if (!file_exists($old_folder . '/main.png') && $_POST['small_image_action'] != IMAGE_ADD && $_POST['small_image_action'] == IMAGE_DELETE)
                    throw new ControllerException('Вы должны выбрать хотя бы одно изображение.');

                $section_folder = PATH_SECTION . $_POST['data_folder'];
                if (strcasecmp($old_folder, $section_folder) != 0){
                    if (!@$this->copyTree($old_folder, $section_folder)) {//копируем папку
                        unset($section_folder);
                        throw new ControllerException('Не удалось переместить папку с данными.<br/>Повторите действие позже.');
                    }
                }
            }
            else {
                $section_folder = PATH_SECTION . $_POST['data_folder'];
                if (!@mkdir($section_folder)) { //создаем папку
                    unset($section_folder);
                    throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
                }
            } 

            if ($_POST['big_image_action'] == IMAGE_ADD){
                require_once PATH_INCLUDE . 'GDExtensions.php';
                if (!GDExtensions::fitToRect(PATH_TEMP . pathinfo($_POST['big_image_path'], PATHINFO_BASENAME), 500, 500, $section_folder . '/main.png'))
                    throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());  
            }
            else if ($_POST['big_image_action'] == IMAGE_DELETE){
                if (!@unlink($section_folder . '/main.png')) //пробуем удалить изображение
                    throw new ControllerException('Не удалось удалить изображение.');
            }

            if ($_POST['small_image_action'] == IMAGE_ADD){
                require_once PATH_INCLUDE . 'GDExtensions.php';
                if (!GDExtensions::fitToRect(PATH_TEMP . pathinfo($_POST['small_image_path'], PATHINFO_BASENAME), 25, 25, $section_folder . '/main_small.png'))
                    throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());
            }
            else if ($_POST['small_image_action'] == IMAGE_DELETE){
                if (!@unlink($section_folder . '/main_small.png'))
                    throw new ControllerException('Не удалось удалить изображение.');
            }
            //маленькое изображение можно получить из большого
            if ($_POST['small_image_action'] == IMAGE_DELETE || (!$id && $_POST['small_image_action'] == IMAGE_NOACTION)){
                require_once  PATH_INCLUDE . 'GDExtensions.php';
                if (!GDExtensions::fitToRect($section_folder .  '/main.png', 25, 25, $section_folder . '/main_small.png'))
                    throw new ControllerException('Не удалось сохранить изображение.', GDExtensions::lastError());
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

            if ($id)
                $this->db->update('sections', $values, ['id' => $id]);
            else
                $this->db->insert('sections', $values);

            if (isset($old_folder) && strcasecmp($section_folder, $old_folder) !=0) @$this->delTree($old_folder);
            $this->data['section_id'] = $id ? $id : $this->db->lastInsertId();
        }
        catch (Exception $ex){ //все плохо, удалим папку, которую мы собирались сделать главной для этого раздела
            if (isset($section_folder)){
                if (!isset($old_folder) || (isset($old_folder) && strcasecmp($section_folder, $old_folder) !=0))
                    @$this->delTree($section_folder);
            }
            throw $ex;
        }
    }

    function sections(){
        $this->showErrorPage = TRUE;
        $this->validateRights([USER_ADMIN]);

        $action = $_REQUEST['param2'];

        //вывод списка разделов
        if (empty($action)){
            $res = $this->db->fetch('SELECT sections.id AS id, title, data_folder, parent_id, DATE_FORMAT(creation_date, "%e.%m.%Y") AS creation_date, users.id AS user_id, login FROM sections INNER JOIN users ON creator_id=users.id');
            foreach ($res as $s){
                if (!empty($s['parent_id']))
                    $this->data['sections'][$s['parent_id']]['children'][] = $s; //дочерние разделы идут в children родителя
                else $this->data['sections'][$s['id']] = $s;
            }
        }
        //вывод формы для добавления
        else if ($action == 'add'){
            if (!empty($_GET['save'])) {
                $this->saveSection();
                $this->action = 'addsection';
                return;
            }
            $this->data['planets'] = $this->db->fetch('SELECT id, title FROM sections WHERE type=1');
            $this->data['subaction'] = 'add';

        }
        //вывод формы для редактирования
        else if ($action == 'edit'){
            $this->validateArgs($_GET, [['section_id', 'numeric']]);
            $id = $_GET['section_id'];
            if (!empty($_GET['save'])) {
                $this->saveSection($id);
                $this->action = 'addsection';
                return;
            }

            if (!$res = $this->db->fetch('SELECT * FROM sections WHERE id=' . $id))
                throw new ControllerException('Этот раздел еще не существует.');
            $this->data['section'] = $res[0];

            $this->data['section']['description'] = strip_tags(preg_replace('#(<\/h3>|<br\/>)#', "\n", $this->data['section']['description']));

            $this->data['planets'] = $this->db->fetch('SELECT id, title FROM sections WHERE type=1');

            $this->data['subaction'] = 'edit';
        }
        else if ($action == 'delete'){
            $this->showErrorPage = FALSE;
            $this->validateArgs($_GET, [['section_id', 'numeric']]);
            $id = $_GET['section_id'];
            if (!($res = $this->db->fetch('SELECT data_folder FROM sections WHERE id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');

            $this->db->delete('sections', ['id' => $id]);
            @$this->delTree(PATH_SECTION . $res[0]['data_folder']);
        }
    }

    function articles(){
        $this->showErrorPage = TRUE;

        $action = $_REQUEST['param2'];

        //список статей
        if (empty($action)){
            $this->validateRights([USER_ADMIN]);

            $count = $this->db->fetch('SELECT COUNT(*) AS c FROM articles', 1)[0]['c'];
            $page = 1;
            if (isset($_GET['page']) && is_numeric($_GET['page'])) $page = $_GET['page'];

            $this->data = array_merge($this->data, $this->splitPages($count, $page));

            if (!isset($_GET['sort']) || $_GET['sort'] == 0 || $_GET['sort'] > 3) $sort_col = 'articles.pub_date DESC';
            else if ($_GET['sort'] == 1) $sort_col = 'articles.views DESC';
            else if ($_GET['sort'] == 2) $sort_col = 'articles.title';
            else $sort_col = 'articles.verifier_id';

            if (isset($_GET['sort'])) $this->data['sort'] = $_GET['sort'];

            if (!empty($_GET['split'])){
                $sort_col = 'sections.title, ' . $sort_col;
                $this->data['split'] = 1;
            }
            else $this->data['split'] = 0;

            $res = $this->db->fetch('SELECT articles.id AS article_id, if(verifier_id IS NULL, CONCAT("[Не проверено] ",articles.title), articles.title) AS title, DATE_FORMAT(pub_date, "%e.%m.%Y") AS pub_date, verifier_id, views, users.id AS user_id, login, data_folder, sections.title AS section_title FROM articles INNER JOIN users ON articles.author_id = users.id INNER JOIN sections ON articles.section_id = sections.id ORDER BY ' . $sort_col . ' LIMIT '  . (($this->data['page']-1)*$this->data['page_size']) . ', ' . $this->data['page_size']);            
            foreach ($res as &$r){
                $r['href'] = $this->app->config['path']['section'] . $r['data_folder'] . '/' . $r['article_id'] . '/';
                $r['description'] =  @file_get_contents(PATH_SECTION .  $r['data_folder'] . '/' . $r['article_id']. '/description.txt');
            }
            $this->data['articles'] = $res;

            $this->data['page_href'] = '?' . (isset($_GET['sort']) ? 'sort=' . $_GET['sort'] . '&' : '') . 'split=' .  (int)$this->data['split'];
            $this->data['splitter_href'] = '?' . (isset($_GET['sort']) ? 'sort=' . $_GET['sort'] . '&' : '') . 'split=' .  (int)!$this->data['split'] . '&page=' . (int)$this->data['page'];
        }
        //вывод статьи для добавления
        else if ($action == 'add'){
            if (!empty($_GET['save'])) {
                $this->saveArticle();
                $this->action = 'addarticle';
                return;
            }
            $res = $this->db->fetch('SELECT id, title FROM sections' . ($this->data['user']['is_admin'] ? '' : ' WHERE allow_user_articles=1'));
            $this->data['sections'] = $res;
        }
        //вывод статьи для редактирования
        else if ($action == 'edit'){
            $this->validateRights([USER_ADMIN]);
            $this->validateArgs($_GET, [['article_id', 'numeric']]);
            $id = $_GET['article_id'];
            if (!empty($_GET['save'])) {
                $this->saveArticle($id);
                $this->action = 'addarticle';
                return;
            }

            if (!($res = $this->db->fetch('SELECT articles.id AS id, section_id, articles.title AS title, data_folder, verifier_id FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            $this->data['article'] = $res[0];
            //описание
            $this->data['article']['description'] = @file_get_contents(PATH_SECTION . "{$this->data['article']['data_folder']}/{$this->data['article']['id']}/description.txt");
            $this->data['article']['description'] = preg_replace('#<p>(.*)<\/p>#U', "$1", $this->data['article']['description']);

            $folder = PATH_SECTION . "{$this->data['article']['data_folder']}/{$this->data['article']['id']}/";
            $folder_site = $this->app->config['path']['section'] . "{$this->data['article']['data_folder']}/{$this->data['article']['id']}/";

            //содержание
            $this->data['article']['contents'] = @file_get_contents($folder . 'text.txt');

            //список разделов
            $this->data['sections'] = $this->db->fetch('SELECT id, title FROM sections');

            $this->data['article']['images'] = scandir($folder);

            //выбор изображений из папки статьи
            $this->data['article']['images'] = array_map(function($val) use ($folder_site) {
                return $folder_site . $val;
            }, array_filter($this->data['article']['images'], function($val) use ($folder) {
                return $val != '.' && $val != '..' && @getimagesize($folder . $val);
            }));
        }
        //удаление изображений из папки статьи
        else if ($action == 'deleteimg'){
            $this->showErrorPage = FALSE;
            $this->validateRights([USER_ADMIN]);
            $this->validateArgs($_GET, [['article_id', 'numeric'], ['image_path']]);
            $id = $_GET['article_id'];
            $path = pathinfo($_GET['image_path'], PATHINFO_BASENAME);

            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            @unlink(PATH_SECTION . $res[0]['data_folder'] . '/' . $id . '/' . $path);
        }
        //удаление статьи
        else if ($action == 'delete'){
            $this->showErrorPage = FALSE;
            $this->validateArgs($_GET, [['article_id', 'numeric']]);
            $id = $_GET['article_id'];

            if (!($res = $this->db->fetch('SELECT data_folder FROM articles INNER JOIN sections ON articles.section_id=sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');

            $this->db->delete('articles', ['id' => $id]);
            @$this->delTree(PATH_SECTION . $res[0]['data_folder'] . '/' . $id);
        }
        else return;
        $this->data['subaction'] = $action;
    }

    function process($action){
        parent::process('');
        if (empty($action)) $this->action = $action = 'messages';
        else  $this->action = $action;
        $this->$action();
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
