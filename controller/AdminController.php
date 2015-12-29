<?php

require_once PATH_INCLUDE . 'gd/GDImage.php';
require_once PATH_INCLUDE . 'gd/charts/BarChart.php';
require_once PATH_INCLUDE . 'gd/charts/LineChart.php';
require_once 'MenuController.php';

const IMAGE_NOACTION = 0;
const IMAGE_ADD = 1;
const IMAGE_DELETE = 2;

class AdminController extends MenuController
{
    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->actions = ['messages', 'preview', 'sections', 'articles', 'users', 'storage', 'stats'];
        $this->showErrorPage = TRUE;
        $this->validateRights([USER_REGISTERED]);
    }

    function messages(){
        $this->showErrorPage = TRUE;
        $this->validateRights([USER_ADMIN]);
        $res = $this->db->fetch('SELECT sections.title as section_title, data_folder, articles.id AS article_id, articles.title as article_title, users.id as user_id, login, DATE_FORMAT(pub_date, \'%e.%m.%Y %H:%i\') AS pub_date FROM articles INNER JOIN users ON articles.author_id=users.id INNER JOIN sections ON sections.id = articles.section_id WHERE verifier_id IS NULL');
        foreach ($res as &$r){
            $r['href'] = $this->app->config['path']['section'] . $r['data_folder'] . '/' . $r['article_id'] . '/';
            $r['description'] = @file_get_contents(PATH_SECTION . $r['data_folder'] . '/' . $r['article_id'] . '/description.txt');
        }
        unset($r);
        $this->data['messages'] = $res;
    }

    //предпросмотр статьи
    function preview(){
        $this->showErrorPage = FALSE;
        $this->validateArgs($_POST, [['text', 'string']]);
        $text = strip_tags(trim($_POST['text']));        
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
        try {
            if ($id) $this->validateRights([USER_ADMIN]);

            //торжественно клянусь, что замышляю только шалость!
            $this->validateArgs($_POST, [['section_id', 'numeric'], ['title', 'string'], ['description', 'string'], ['contents', 'string']]);

            //для начала проверим, в какой раздел лезет пользователь
            if (!$this->db->fetch('SELECT id FROM sections WHERE id=' . $_POST['section_id'] . (empty($this->data['user']['is_admin']) ? ' AND allow_user_articles=1' : '')))
                throw new ControllerException('Невозможно создать/изменить статью в этом разделе.<br/>Возможно, он не существует, или у вас нет прав на публикацию в нем.');

            //проверим, не слишком ли много непроверенных публикаций наспамил пользователь 
            if (empty($this->data['user']['is_admin'])) {
                if ($this->db->fetch('SELECT COUNT(*) AS c FROM articles WHERE author_id=' . $this->data['user']['id'] . ' AND verifier_id IS NULL', 1)[0]['c'] >= $this->app->config['article']['max_unverified_articles'])
                    throw new ControllerException('Вы не можете предолжить статью, так как мы еще не проверили ваши предыдущие публикации.<br/>Повторите попытку позже.');
            }

            //проверка формата
            if (!preg_match('#^.{1,100}$#u', $_POST['title'])) throw new ControllerException('Неправильный формат заголовка.');
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
                $article_path_root = $this->app->config['path']['section'] . $res[0]['data_folder'] . '/' . $this->db->lastInsertId() . '/';
                if (!@mkdir($article_path)){
                    unset($article_path);
                    throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
                }
                //в базу добавили, новую папку получили
            }
            else {
                $article_path = PATH_SECTION . $res[0]['data_folder'] . '/' . $id . '/';
                $article_path_root = $this->app->config['path']['section'] . $res[0]['data_folder'] . '/' . $id . '/';
                if (strcasecmp($old_folder, $article_path) != 0){
                    if (!@$this->copyTree($old_folder, $article_path)){
                        unset($article_path);
                        throw new ControllerException('Не удалось изменить папку с данными.<br/>Повторите действие позже.');
                    }
                }
                $this->db->update('articles', $fields, ['id' => $id]); //базу обновили, папку переместили
            }

            file_put_contents($article_path . 'description.txt' , preg_replace('#^(.+)$#m', '<p>$1</p>', strip_tags(trim($_POST['description']))));
            file_put_contents($article_path . 'text.txt', $text);

            if (isset($old_folder) && strcasecmp($article_path, $old_folder) !=0) @$this->delTree($old_folder);

            //шалость удалась!
            $this->data['article_path'] = $article_path_root;
        }
        catch (Exception $ex){
            if (isset($article_path)){
                if (!isset($old_folder) || (isset($old_folder) && strcasecmp($article_path, $old_folder) !=0))
                    @$this->delTree($article_path);
            }
            throw $ex;
        }

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
                ($_POST['small_image_action'] == IMAGE_ADD && empty($_POST['small_image_path'])))
                throw new ControllerException('Неправильные параметры запроса.<br/>Повторите действие позже.');

            if (!$id && $_POST['small_image_action'] != IMAGE_ADD)
                throw new ControllerException('Вы должны выбрать маленькое изображение.');
            if ($_POST['small_image_action'] == IMAGE_DELETE)
                throw new ControllerException('Вы не можете удалить маленькое изображение.');

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
                if (!($res = $this->db->fetch('SELECT data_folder, big_image from sections WHERE id=' . $id)))
                    throw new ControllerException('Раздел еще не существует.');

                if (empty($res[0]['big_image']) && $_POST['show_main'] == 1 && $_POST['big_image_action'] != IMAGE_ADD)
                    throw new ControllerException("Вы должны выбрать изображение для отображения на главной странице.");
                else if ($_POST['show_main'] == 1 && $_POST['big_image_action'] == IMAGE_DELETE)
                    throw new ControllerException("Вы должны выбрать изображение для отображения на главной странице.");

                $old_folder = PATH_SECTION . $res[0]['data_folder'];

                $section_folder = PATH_SECTION . $_POST['data_folder'];
                if (strcasecmp($old_folder, $section_folder) != 0){
                    if (!@$this->copyTree($old_folder, $section_folder)) {//копируем папку
                        unset($section_folder);
                        throw new ControllerException('Не удалось переместить папку с данными.<br/>Повторите действие позже.');
                    }
                }
            }
            else {
                if ($_POST['show_main'] == 1 && $_POST['big_image_action'] != IMAGE_ADD)
                    throw new ControllerException("Вы должны выбрать изображение для отображения на главной странице.");

                $section_folder = PATH_SECTION . $_POST['data_folder'];
                if (!@mkdir($section_folder)) { //создаем папку
                    unset($section_folder);
                    throw new ControllerException('Не удалось создать папку с данными.<br/>Повторите действие позже.');
                }
            } 

            if ($_POST['big_image_action'] == IMAGE_ADD){
                if (!($res = $this->db->fetch('SELECT id, extension FROM storage WHERE id=' . $this->db->escapeString($_POST['big_image_path']))))
                    throw new ControllerException('Неправильный идентификатор изображения.');

                if (!($s = PATH_STORAGE . $res[0]['id'] . '.' . $res[0]['extension']) || $s[0] > 500 || $s[1] > 500)
                    throw new ControllerException('Превышены максимальные размеры большого изображения (500x500)');
            }

            if ($_POST['small_image_action'] == IMAGE_ADD){
                if (!($res = $this->db->fetch('SELECT id, extension FROM storage WHERE id=' . $this->db->escapeString($_POST['small_image_path']))))
                    throw new ControllerException('Неправильный идентификатор изображения.');

                if (!($s = PATH_STORAGE . $res[0]['id'] . '.' . $res[0]['extension']) || $s[0] > 500 || $s[1] > 500)
                    throw new ControllerException('Превышены максимальные размеры маленького изображения (500x500)');
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

            if($_POST['big_image_action'] != IMAGE_NOACTION) $values['big_image'] = $_POST['big_image_action'] == IMAGE_DELETE ? NULL : $_POST['big_image_path'];
            if($_POST['small_image_action'] == IMAGE_ADD) $values['small_image'] = $_POST['small_image_path'];

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
            $res = $this->db->fetch('SELECT sections.id AS id, title, data_folder, parent_id, DATE_FORMAT(creation_date, "%e.%m.%Y") AS creation_date, CONCAT(big_image, ".", big.extension) AS big_file, CONCAT(small_image, ".", small.extension) AS small_file, users.id AS user_id, login FROM sections INNER JOIN users ON creator_id=users.id LEFT JOIN storage big ON big_image=big.id LEFT JOIN storage small ON small_image=small.id');
            $this->data['sections'] = [];
            foreach ($res as $s){
                if (!empty( $s['big_file'])) $s['big_file'] = $this->app->config['path']['storage'] . $s['big_file'];
                if (!empty( $s['small_file']))  $s['small_file'] = $this->app->config['path']['storage'] . $s['small_file'];
                if (!empty($s['parent_id'])){
                    $this->data['sections'][$s['parent_id']]['children'][] = $s; //дочерние разделы идут в children родителя
                }
                else {
                    if (isset($this->data['sections'][$s['id']]))
                        $this->data['sections'][$s['id']] = array_merge($this->data['sections'][$s['id']], $s);
                    else $this->data['sections'][$s['id']] = $s;
                }

            }
        }
        //вывод формы для добавления
        else if ($action == 'add'){
            if (!empty($_GET['save'])) {
                $this->saveSection();
                $this->data['action'] = 'addsection';
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
                $this->data['action'] = 'addsection';
                return;
            }

            if (!$res = $this->db->fetch('SELECT sections.id AS id, title, data_folder, parent_id, allow_user_articles, show_main, sections.description AS description, type, DATE_FORMAT(creation_date, "%e.%m.%Y") AS creation_date, CONCAT(big_image, ".", big.extension) AS big_file, CONCAT(small_image, ".", small.extension) AS small_file FROM sections LEFT JOIN storage big ON big_image=big.id LEFT JOIN storage small ON small_image=small.id WHERE sections.id=' . $id))
                throw new ControllerException('Этот раздел еще не существует.');

            foreach ($res as &$r){
                if (!empty( $r['big_file'])) $r['big_file'] = $this->app->config['path']['storage'] . $r['big_file'];
                if (!empty( $r['small_file'])) $r['small_file'] = $this->app->config['path']['storage'] . $r['small_file'];
            }
            unset($r);

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
            $this->data['subaction'] = 'delete';
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
                $this->data['action'] = 'addarticle';
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
                $this->data['action'] = 'addarticle';
                return;
            }

            if (!($res = $this->db->fetch('SELECT articles.id AS id, section_id, articles.title AS title, data_folder, verifier_id FROM articles INNER JOIN sections ON articles.section_id = sections.id WHERE articles.id=' . $id)))
                throw new ControllerException('Этот раздел еще не существует.');
            $this->data['article'] = $res[0];
            //описание
            $this->data['article']['description'] = @file_get_contents(PATH_SECTION . "{$this->data['article']['data_folder']}/{$this->data['article']['id']}/description.txt");
            $this->data['article']['description'] = preg_replace('#<p>(.*)<\/p>#U', "$1", $this->data['article']['description']);

            $folder = PATH_SECTION . "{$this->data['article']['data_folder']}/{$this->data['article']['id']}/";
            
            //содержание
            $this->data['article']['contents'] = @file_get_contents($folder . 'text.txt');

            //список разделов
            $this->data['sections'] = $this->db->fetch('SELECT id, title FROM sections');
        }
        //удаление статьи
        else if ($action == 'delete'){
            $this->showErrorPage = FALSE;
            $this->validateRights([USER_ADMIN]);
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

    function storage(){}

    function stats(){
        $this->validateRights([USER_ADMIN]);

        if (isset($_REQUEST['top_articles'])){
            if ($t = $this->db->fetch('SELECT views FROM articles ORDER BY views DESC LIMIT 5')){
                $v = [0];
                foreach ($t as $a) $v[] = $a['views'];
                unset($v[0]);
                $this->data['image'] = new GDImage(NULL, new Size(800,500));
                $g=new BarChart(new Boundary(0,0,$this->data['image']->size()->width,$this->data['image']->size()->height));
                $g->depth(15);
                $g->background(new Color(255,144,130, 127));
                $g->values(['bgcolors' => [new Color(255,102,91),new Color(50,255,0), new Color(63,168,255), new Color(255,220,22), new Color(255,112,238)], 'labels' => TRUE, 'thickness' => 3]);
                $g->data($v);
                $g->title(['text' => 'Топ 5 публикаций', 'color' => new Color(255,255,255), 'font' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Bold.ttf' ,14), 'margin' => ['top' => 0, 'bottom' => 15]]);
                $g->axis(['font_x' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Regular.ttf',8), 'font_y' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Regular.ttf',8), 'bgcolor' => new Color(255,255,255,100), 'fgcolor' => new Color(255,255,255), 'y_lines' => TRUE]);
                $g->max(50);
                $g->min(0);
                $g->draw($this->data['image']->getHandle());
            }
            $this->data['subaction'] = 'image';
        }
        else if (isset($_REQUEST['storage_usage'])){
            $this->validateArgs($_REQUEST, [['start_date', 'string'], ['end_date', 'string'], ['resolution', 'numeric']]);

            $s = $_REQUEST['start_date'];
            $e = $_REQUEST['end_date'];
            $r = $_REQUEST['resolution'];
       
            $v = [];
            if ($t = $this->db->call('show_storage_usage', [$s, $e, $r])){
                $b = $t[0]['balance'];

                for ($i=1; $i<count($t); $i++){
                    if ($r == 1)
                        $v[sprintf('%02d.%02d.%04d', $t[$i]['day'], $t[$i]['month'], $t[$i]['year'])] = round(($b += $t[$i]['balance'])/1024/1024,2);
                    else if ($r == 2)
                        $v[sprintf('%02d.%04d', $t[$i]['month'], $t[$i]['year'])] = round(($b += $t[$i]['balance']) / 1024/1024,2);
                }

               
                $this->data['image'] = new GDImage(NULL, new Size(800,500));
                $g=new LineChart(new Boundary(0,0,$this->data['image']->size()->width,$this->data['image']->size()->height));
                $g->depth(15);
                $g->background(new Color(255,144,130, 127));
                $g->values(['bgcolors' => [new Color(0,255,0)], 'fgcolors' => [new Color(255,255,255)], 'font' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Regular.ttf' ,10), 'labels' => TRUE, 'thickness' => 2]);
                $g->data($v);
                $g->title(['text' => 'Использование хранилища', 'color' => new Color(255,255,255), 'font' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Bold.ttf' ,14), 'margin' => ['top' => 0, 'bottom' => 15]]);
                $g->axis(['font_x' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Regular.ttf',8, 90), 'font_y' => new Font(PATH_ROOT . '/css/fonts/OpenSans-Regular.ttf',8), 'bgcolor' => new Color(255,255,255,100), 'fgcolor' => new Color(255,255,255), 'y_lines' => TRUE]);
                $g->draw($this->data['image']->getHandle());
            }
            $this->data['subaction'] = 'image';
        }

        else{

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_users FROM users'))
                $this->data['stats']['total_users'] = $t[0]['total_users'];

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_admins FROM users WHERE is_admin=1'))
                $this->data['stats']['total_admins'] = $t[0]['total_admins'];

            if ($t = $this->db->fetch('SELECT id, login, DATE_FORMAT(reg_date, \'%e.%m.%Y %H:%i\') AS reg_date FROM users WHERE reg_date=(SELECT MAX(reg_date) FROM users)'))
                $this->data['stats']['last_user'] = $t[0];

            if ($t = $this->db->fetch('SELECT SUM(file_size) AS total_size FROM storage'))
                $this->data['stats']['storage_usage'] = round($t[0]['total_size'] / 1024 / 1024, 1) . ($this->app->config['storage']['max_size'] != -1 ? ' из ' . $this->app->config['storage']['max_size'] : '') . ' МБ';

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_sections FROM sections'))
                $this->data['stats']['total_sections'] = $t[0]['total_sections'];

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_articles FROM articles'))
                $this->data['stats']['total_articles'] = $t[0]['total_articles'];

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_unverified_articles FROM articles WHERE verifier_id IS NULL'))
                $this->data['stats']['total_unverified_articles'] = $t[0]['total_unverified_articles'];

            if ($t = $this->db->fetch('SELECT COUNT(*) AS total_comments FROM comments'))
                $this->data['stats']['total_comments'] = $t[0]['total_comments'];

            if ($t = $this->db->fetch('SELECT articles.id AS id, articles.title AS title, data_folder, users.id AS user_id, login FROM articles INNER JOIN sections ON articles.section_id=sections.id INNER JOIN users ON author_id=users.id ORDER BY views DESC LIMIT 5')) { 
                foreach ($t as &$a)
                    $a['href'] = $this->app->config['path']['section'] . $a['data_folder'] . '/' . $a['id'] . '/';
                unset($a);
                $this->data['stats']['top_articles'] = $t;
            }
        }
    }

    function users(){
        $this->validateRights([USER_ADMIN]);
        header('Location: http://planetsbook.pp.ua/admin');
    }

    function process($action){
        parent::process('');
        if (empty($action)) $this->data['action'] = $action = 'messages';
        else  $this->data['action'] = $action;
        $this->$action();
    }

    function render(){
        if ($this->data['action'] == 'preview'){
            echo $this->data['parsed_text'] . '<div class="clearfix"></div>';
        }
        else if ($this->data['action'] == 'addarticle'){
            $j = [];
            $j['article_path'] = $this->data['article_path'];
            echo json_encode($j);
        }
        else if ($this->data['action'] == 'addsection'){
            $j = ['pub_path' => '/admin/articles/add/?section=' . $this->data['section_id']];
            echo json_encode($j);
        }
        else if (isset($this->data['subaction']) && $this->data['subaction'] == 'delete'){
            echo json_encode([]);
        }
        else if (isset($this->data['subaction']) && $this->data['subaction'] == 'image'){
            if (isset($this->data['image']))
                $this->data['image']->saveTo('png', NULL, 0);
        }
        else{
            $this->data['menu'] = parent::render();
            $this->renderView('admin_' . $this->data['action']);
        }
    }
}
