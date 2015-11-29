<?php
require_once 'ControllerBase.php';
require_once 'MenuController.php';

class SectionsController extends MenuController
{
    function setActions(){
        $this->actions = [];
    }

	function process($action){
        //перенаправление на ArticleController
        if (!empty($_REQUEST['param2'])) {
            $this->app->callController('article');
            return FALSE;
        }

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

        if (is_file(PATH_SECTION . $this->data['section']['data_folder'] . '/main.png')) 
            $this->data['section']['image'] = $this->app->config['path']['section'] . $this->data['section']['data_folder'] . '/main.png';

        $count = $this->db->fetch('SELECT COUNT(*) AS c FROM articles WHERE section_id=' . $this->data['section']['id'], 1)[0]['c'];
        $page = 1;
        if (isset($_GET['page']) && is_numeric($_GET['page'])) $page = $_GET['page'];

        $this->data = array_merge($this->data, $this->splitPages($count, $page));

        if (!isset($_GET['sort']) || $_GET['sort'] == 0 || $_GET['sort'] > 2) $sort_col = 'pub_date DESC';
        else if ($_GET['sort'] == 1) $sort_col = 'views DESC';
        else $sort_col = 'title';

        if (isset($_GET['sort']))
            $this->data['sort'] = $_GET['sort'];

        $this->data['page_href'] = '?sort=' . (isset($_GET['sort']) ?  $_GET['sort'] : '0');

        $sql = 'SELECT articles.id AS article_id, if(verifier_id IS NULL, CONCAT("[Не проверено] ",title), title) AS title, DATE_FORMAT(pub_date, "%e.%m.%Y") AS pub_date, views, users.id AS user_id, login FROM articles INNER JOIN users ON articles.author_id = users.id WHERE section_id=' . $this->data['section']['id'];
        if (empty($this->data['user']['is_admin']))
            $sql .= ' AND verifier_id IS NOT NULL ';
        $sql .=  ' ORDER BY ' . $sort_col . ' LIMIT '  . (($this->data['page']-1)*$this->data['page_size']) . ', ' . $this->data['page_size'];
        $res = $this->db->fetch($sql);

        foreach ($res as &$a){
            $a['href'] = "{$this->app->config['path']['section']}{$this->data['section']['data_folder']}/{$a['article_id']}/";
            $a['description'] = @file_get_contents(PATH_SECTION . "{$this->data['section']['data_folder']}/{$a['article_id']}/description.txt");
        }
        unset($a);

        $this->data['articles'] = $res;

        
        $this->data['see_also'] = [];
        foreach ($this->data['menu'] as $val){
            if ($val['id'] != $this->data['section']['id'] && ($val['type'] == $this->data['section']['type'] || ($this->data['section']['type']==2 && $val['id']==$this->data['section']['parent_id']))){
                $this->data['see_also'][] = $val;
                if (count($this->data['see_also']) == 5) break;
            }
        }

        return TRUE;
	}

    function render(){
        $this->data['menu'] = parent::render();
        $this->renderView('section');
    }
}
?>