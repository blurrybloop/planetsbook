<?php
require_once('SectionsController.php');

class ArticleController extends MenuController
{
    function process(){
        parent::process();
        foreach ($this->data['menu'] as $val){
            if ($val['data_folder'] == $_REQUEST['param1']){
                $this->data['section'] = $val;
                break;
            }
        }

        if (!is_numeric($_REQUEST['param2'])) {
            echo 'error';
            return;
        }

        $res = $this->db->fetch("SELECT articles.id AS article_id, title, data_folder, DATE_FORMAT(pub_date, '%e.%m.%Y') AS pub_date, views, users.id AS user_id, login FROM articles INNER JOIN users ON articles.author_id = users.id WHERE articles.id={$_REQUEST['param2']}");
        $this->data['article'] = $res[0];
    }

    function render(){
        $this->data['menu'] = parent::render();
        $this->renderView('article');
    }
}
