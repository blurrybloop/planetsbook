<?php 
require_once 'ControllerBase.php';
require_once 'MenuController.php';

class MainController extends MenuController
{ 

	function process($action){
        //получение меню
        parent::process($action);

        //формирование данных для слайдера

        $show_res = [];
        foreach ($this->data['menu'] as &$val)
        {
            if ($val['show_main'])
            {
                if ($val['type'] == 0 || $val['type'] == 1){
                    $show_res[$val['id']]['title'] = $val['title'];
                    $show_res[$val['id']]['description'] = $val['description'];
                    $show_res[$val['id']]['image'] = $this->app->config['path']['section'] . $val['data_folder'] . '/main.png';
                    $show_res[$val['id']]['href'] = $val['href'];
                    if (!isset($show_res[$val['id']]['moons'])) $show_res[$val['id']]['moons'] = [];
                }
                else if ($val['type'] == 2)
                    $show_res[$val['parent_id']]['moons'][] = [ 
                        'title'         =>  $val['title'], 
                        'description'   =>  $val['description'], 
                        'image'         =>  $this->app->config['path']['section'] . $val['data_folder'] . '/main.png', 
                        'href'          =>  $val['href']
                    ];
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