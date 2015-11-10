<?php

require_once 'ControllerBase.php';

class MenuController extends ControllerBase
{
	function process(){
        try { $res = $this->db->fetch('SELECT * FROM sections'); }
        catch (DatabaseException $ex){ return; }
            foreach ($res as &$val) {
                $val['image'] = '/sections/' . $val['data_folder'] . '/main.png';
                $val['href'] = '/sections/' . $val['data_folder'] . '/';
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