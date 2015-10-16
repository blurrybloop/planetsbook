<?php
require_once 'ControllerBase.php';

class MenuController extends ControllerBase
{
	function process(){
            $res = $this->db->fetch('SELECT * FROM sections');
            if ($res === FALSE) echo 'error';
            foreach ($res as &$val) {
                $val['image'] = '/sections/' . $val['data_folder'] . '/main.png';
                $val['href'] = '/sections/' . $val['data_folder'] . '/';
            }
            unset($val);
            $this->data['menu'] = $res;
	}

    function render($suppressOutput = TRUE){
        if ($suppressOutput) ob_start();
        $this->renderView('menu');
        if ($suppressOutput) return ob_get_clean();
        else return '';
    }
}