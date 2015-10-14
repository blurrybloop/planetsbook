<?php 
require_once 'ControllerBase.php';

class MainController extends ControllerBase
{
	function show(){
        $res = $this->db->fetch('SELECT id AS moons, title, data_folder, description, show_main, type FROM sections WHERE parent_id IS NULL');
        if ($res === FALSE) echo 'error';
        foreach ($res as &$val)
        {
            $r = $this->db->fetch("SELECT title, data_folder, description, parent_id, show_main, type FROM sections WHERE parent_id={$val['moons']}");
            if ($r === FALSE) echo 'error';
            if ($r)
                foreach ($r as &$val2){
                    $val2['image'] = '/sections/' . $val2['data_folder'] . '/main.png';
                    $val2['href'] = '/sections/' . $val2['data_folder'];
                }
            unset($val2);
            $val['moons'] = $r;
            $val['image'] = '/sections/' . $val['data_folder'] . '/main.png';
            $val['href'] = '/sections/' . $val['data_folder'];
        }
        unset($val);
        $this->data = $res;
		$this->render('main');
	}
}
?>