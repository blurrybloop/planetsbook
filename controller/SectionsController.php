<?php
require_once 'ControllerBase.php';
require_once 'MenuController.php';

class SectionsController extends MenuController
{
    function getSection(){
    
    }

	function process(){
            parent::process();
            foreach ($this->data['menu'] as $val){
                if ($val['data_folder'] == $_REQUEST['param1']){
                    $this->data['section'] = $val;
                    break;
                }
            }
            
            if (isset($_REQUEST))

                if (!isset($_REQUEST['sort']) || $_REQUEST['sort'] == 0 || $_REQUEST['sort'] > 2) $sort_col = 'pub_date DESC';
                else if ($_REQUEST['sort'] == 1) $sort_col = 'views DESC';
            else $sort_col = 'title';

            $sql = 'SELECT articles.id AS article_id, if(verifier_id IS NULL, CONCAT("[Не проверено] ",title), title) AS title, DATE_FORMAT(pub_date, "%e.%m.%Y") AS pub_date, views, users.id AS user_id, login FROM articles INNER JOIN users ON articles.author_id = users.id WHERE section_id=' . $this->data['section']['id'];
            if (empty($this->data['user']['is_admin']))
                $sql .= ' AND verifier_id IS NOT NULL ';
            $sql .=  ' ORDER BY ' . $sort_col;
            $res = $this->db->fetch($sql);
            if ($res === FALSE){
                echo 'error';
                return;
            }

            $this->data['articles'] = $res;
	}

    function render(){
        $this->data['menu'] = parent::render();
        $this->renderView('section');
    }
}
?>