<?php
require_once('SectionsController.php');

class ArticleController extends MenuController
{

    public $action;

    private function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    } 

    function getArticle($id){
        $this->showErrorPage = TRUE;
        parent::process();
        foreach ($this->data['menu'] as $val){
            if ($val['data_folder'] == $_REQUEST['param1']){
                $this->data['section'] = $val;
                break;
            }
        }

        $res = $this->db->fetch("SELECT articles.id AS article_id, title, DATE_FORMAT(pub_date, '%e.%m.%Y') AS pub_date, views, verifier_id, users.id AS user_id, login FROM articles INNER JOIN users ON articles.author_id = users.id WHERE articles.id={$_REQUEST['param2']}");
        if (!count($res) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $this->data['section']['data_folder'] . '/' . $res[0]['article_id']))
            throw new HttpException(404);

        $this->data['article'] = $res[0];
    }

    function publicate($id){
        parent::validateRights([USER_ADMIN]);
        $this->db->update('articles', ['verifier_id' => $this->data['user']['id']], ['id' => $id]);
    }

    function dismiss($id){
        parent::validateRights([USER_ADMIN]);

        $article_folder = $_SERVER['DOCUMENT_ROOT'] . '/sections/';

        $res = $this->db->fetch('SELECT data_folder from articles INNER JOIN sections ON articles.section_id=sections.id WHERE articles.id=' . $id);
           
        $article_folder .= $res[0]['data_folder'] . '/' . $id;

        $this->db->delete('articles', ['id' => $id]);

        if (!@$this->delTree($article_folder))
            throw new ControllerException('Произошла ошибка при удалении.<br/>Повторите действие позже.');
    }

    function process(){
        if (!is_numeric($_REQUEST['param2']))
            throw new ControllerException('Неправильные параметры запроса.');
        if (!isset($_REQUEST['action']))
            $this->getArticle($_REQUEST['param2']);
        else{

            $this->action = strtolower($_REQUEST['action']);

            if ($this->action == 'pub')
                $this->publicate($_REQUEST['param2']);
            else if ($this->action == 'dismiss')
                $this->dismiss($_REQUEST['param2']);
            else $this->getArticle($_REQUEST['param2']);
        }
    }

    function render(){
        if ($this->action != 'pub' && $this->action != 'dismiss'){
            $this->data['menu'] = parent::render();
            $this->renderView('article');
        }
    }
}
