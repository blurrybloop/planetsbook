<?php 
require_once 'ControllerBase.php';
require_once 'MenuController.php';

class MainController extends ControllerBase
{
	function show(){
        $res = $this->db->fetch('SELECT id, title, data_folder, description, show_main, type, parent_id FROM sections');
        if ($res === FALSE) echo 'error';
        $show_res = [];
        foreach ($res as &$val)
        {
            $val['image'] = '/sections/' . $val['data_folder'] . '/main.png';
            $val['href'] = '/sections/' . $val['data_folder'] . '/';
            if ($val['show_main'])
            {
                if ($val['type'] == 0 || $val['type'] == 1)
                    $show_res[$val['id']] = [ 'title' => $val['title'], 'description' => $val['description'], 'image' => $val['image'], 'href' => $val['href'], 'moons' => [] ];
                else if ($val['type'] == 2)
                    $show_res[$val['parent_id']]['moons'][] = [ 'title' => $val['title'], 'description' => $val['description'], 'image' => $val['image'], 'href' => $val['href']];
            }
        }

        unset($val);
        $m = new MenuController;
        $m->data = $res;
        ob_start();
        $m->render('menu');
        $this->data['menu'] = ob_get_clean();

        $this->data['show'] = $show_res;
		$this->render('main');
	}
}
?>