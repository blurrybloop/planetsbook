<?php
require_once 'ControllerBase.php';

class MenuController extends ControllerBase
{
	function show(){
        $res = $this->db->fetch('SELECT * FROM sections');
        if ($res === FALSE) echo 'error';
        foreach ($res as &$val)
        {
            $val['image'] = '/sections/' . $val['data_folder'] . '/main.png';
            $val['href'] = '/sections/' . $val['data_folder'] . '/';
        }
        unset($val);
        $this->data = $res;
        $this->render('menu');
	}
}