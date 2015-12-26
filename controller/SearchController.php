<?php

require_once 'ControllerBase.php';

class SearchController extends ControllerBase
{
    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->showErrorPage = FALSE;
        $this->useTransactions = FALSE;
        $this->actions = ['search'];
    }

    function search(){
        $this->validateArgs($_REQUEST, [['text', 'string']]);
        $text = $_REQUEST['text'];
        $files = glob(PATH_SECTION . '*/*/{description,text}.txt', GLOB_BRACE);
        $sql = 'SELECT id, title FROM articles WHERE id IN(';
        foreach ($files as $file){
            $c = preg_match_all('#\b' . $text . '\b#iu',  file_get_contents($file));
            $dir = pathinfo($file, PATHINFO_DIRNAME);
            $id = substr(strrchr($dir, '/'),1);
            if ($c != 0){
                $this->data['results'][$id] = ['count' => $c, 'href' =>  str_replace(PATH_ROOT, '', $dir)];
                $sql .=  $id . ',';
            }
        }

        if (empty($this->data['results'])) return;

        $sql = rtrim($sql, ',') . ')';
        $res = $this->db->fetch($sql);
        foreach ($res as $r)
            $this->data['results'][$r['id']]['title'] = $r['title'];
        
        usort($this->data['results'], function($v1, $v2){
            return $v1['count'] < $v2['count'];
        });
    }

    function process($action){
        if (empty($action)) $action='search';
        $this->$action();
    }

    function render(){
        echo json_encode(isset($this->data['results']) ? $this->data['results'] : []);
    }
}
