<?php 
require_once 'ControllerBase.php';
require_once 'MenuController.php';

class MainController extends MenuController
{
   
	function process(){
        parent::process();
        $show_res = [];
        foreach ($this->data['menu'] as &$val)
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
        $this->data['show'] = $show_res; 
	}

    function render(){
        $this->data['menu'] = parent::render();
		$this->renderView('main');
    }
}
?>