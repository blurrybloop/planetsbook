<?php

require_once 'ControllerBase.php';

class MenuController extends ControllerBase
{
    function setActions(){
        $this->actions = [];
    }

	function process($action){
        try { $res = $this->db->fetch('SELECT * FROM sections'); }
        catch (DatabaseException $ex){ return; }
        foreach ($res as &$val) {
            $val['href'] = $this->app->config['path']['section'] . $val['data_folder'] . '/';
        }
        unset($val);
        $this->data['menu'] = $res;
	}

    function render($suppressOutput = TRUE){
        if ($suppressOutput) ob_start();
        if (isset($this->data['menu']))
            $this->renderView('menu');
        if ($suppressOutput) return ob_get_clean();
        else return '';
    }
}