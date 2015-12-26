<?php
require_once('SectionsController.php');

class ArticleController extends MenuController
{

    public function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->actions = [];
    }

    function process($action){
        if (!is_numeric($_REQUEST['param2']))
            throw new ControllerException('Неправильные параметры запроса.');

        $this->showErrorPage = TRUE;
        parent::process($action);
        foreach ($this->data['menu'] as $val){
            if ($val['data_folder'] == $_REQUEST['param1']){
                $this->data['section'] = $val;
                break;
            }
        }
        if (!isset($this->data['section']))
            throw new HttpException(404);

        //if (is_file(PATH_SECTION . $this->data['section']['data_folder'] . '/main.png')) 
        //    $this->data['section']['image'] = $this->app->config['path']['section'] . $this->data['section']['data_folder'] . '/main.png';

        if (!($res = $this->db->fetch("SELECT articles.id AS article_id, title, DATE_FORMAT(pub_date, '%e.%m.%Y') AS pub_date, views, verifier_id, users.id AS user_id, login FROM articles INNER JOIN users ON articles.author_id = users.id WHERE articles.id={$_REQUEST['param2']}")) ||
            !file_exists(PATH_SECTION . $this->data['section']['data_folder'] . '/' . $res[0]['article_id']))
            throw new HttpException(404);

        try{ $this->db->query('UPDATE articles SET views=views+1 WHERE id=' . $res[0]['article_id']); }
        catch (DatabaseException $ex) {}

        $this->data['article'] = $res[0];

        require_once PATH_INCLUDE . 'TagsParser.php';
        $parser = new TagsParser(file_get_contents(PATH_SECTION . "{$this->data['section']['data_folder']}/{$this->data['article']['article_id']}/text.txt"));
        $this->data['article']['contents'] = $parser->parse();

        $res = $this->db->fetch("SELECT articles.id AS article_id, articles.title AS title, data_folder FROM articles INNER JOIN sections ON articles.section_id=sections.id ORDER BY views LIMIT 5");
        foreach ($res as &$r){
            $r['href'] = $this->app->config['path']['section'] . $r['data_folder'] . '/' . $r['article_id'] . '/';
        }
        unset($r);
        $this->data['see_also'] = $res;
    }

    function render(){
        $this->data['menu'] = parent::render();
        $this->renderView('article');
    }
}
