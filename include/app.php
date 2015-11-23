<?php
require_once 'ControllerException.php';
require_once 'HttpException.php';
require_once 'Database.php';

class Application
{
    public $config;
    public $db = NULL;
    public $controller = NULL;

    public function __construct($config){
        $this->config = $config;
    }

    public function callController($name, $data = NULL){
        if (!is_file(PATH_CONTROLLER . $name . 'Controller.php'))
            throw new HttpException(404);

        include(PATH_CONTROLLER . $name . 'Controller.php');
        $class= strtolower($name) . 'Controller';

        if (!class_exists($class))
            throw new HttpException(404);

        if (!$this->db){
            $this->db=new Database($this->config['db']);
            $this->db->connect();
        }
        $this->controller = new $class($this, $this->db, $data);
        $this->controller->show();
    }

	function Run()
	{
        session_start();$c = NULL;
        try
        {
            $menu='main';
            if (isset($_REQUEST['menu'])) $menu = $_REQUEST['menu'];
            if ($menu == 'error') 
                throw new HttpException(isset($_REQUEST['param1']) ? (int)$_REQUEST['param1'] : 500);

            $this->callController('pulse');
            $this->callController($menu);
        }
        catch (Exception $ex) {

            $this->callController('error', [
                'exception'     =>  $ex, 
                'showErrorPage' =>  empty($this->controller) ? TRUE : $this->controller->showErrorPage,
                ]);
            return;
        }

	}
}
?>