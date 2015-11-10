<?php

require_once 'ControllerException.php';
require_once 'HttpException.php';

class Application
{
    public $config;

    public function __construct($config){
        $this->config = $config;
    }

    function cleanUp($db){
        if ($res = $db->fetch('SELECT id FROM temp_pages WHERE TIMESTAMPDIFF(SECOND, last_access, NOW()) > 30')){
            foreach ($res as $val){
                $db->delete('temp_pages', ['id' => $val['id']]);
                $tmp_files = glob($_SERVER['DOCUMENT_ROOT'] . '/res/' . $val['id'] . '_*.*', GLOB_NOSORT);
                foreach ($tmp_files as $file)
                    @unlink($file);
            }
        }
    }

	function Run()
	{
        session_start();
        try
        {
            $menu='main';
            if (isset($_REQUEST['menu'])) $menu = $_REQUEST['menu'];
            if (!empty($_REQUEST['param2']) && $menu == 'sections') $menu='article';

            if ($menu == 'error') throw new HttpException(isset($_REQUEST['param1']) ? $_REQUEST['param1'] : 500);

            if ($menu != 'pulse'){
                if (!is_file(PATH_CONTROLLER . $menu . 'Controller.php'))
                    throw new HttpException(404);

                include(PATH_CONTROLLER.$menu.'Controller.php');
                $class=strtolower($menu).'Controller';

                if (!class_exists($class))
                    throw new HttpException(404);
            }

            $db=new Database($this->config['db']);
            $db->connect();
            $this->cleanUp($db);

            if ($menu == 'pulse') include 'pulse.php';

            if ($menu != 'pulse'){
                $c=new $class($db);
                $c->show();
            }
        }

        catch (Exception $ex) {
            require_once (PATH_CONTROLLER . 'ErrorController.php');
            $ec = new ErrorController(isset($db) ? $db : NULL, $ex);
            $ec->showErrorPage = isset($c) ? $c->showErrorPage : TRUE;
            $ec->show();
            return;
        }

	}
}
?>