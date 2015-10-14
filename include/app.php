<?php
class Application
{
	function Run()
	{
		$menu='main';
		if (isset($_REQUEST['menu'])) $menu=$_REQUEST['menu'];
		if (!is_file(PATH_CONTROLLER . $menu . 'Controller.php'))
		{
			echo 'error file controller';
			return;
		}
		include(PATH_CONTROLLER.$menu.'Controller.php');
		$class=strtolower($menu).'Controller';
		if (!class_exists($class))
		{
			echo 'error class controller';
			return;
		}
		$db=new mysql(['pass'=>'71295','db'=>'planetsbook', 'charset'=>'utf8']);
		if (!$db->connect())
		{
			echo 'error DB';
			return;
		}
		$c=new $class();
		$c->db=$db;
		$c->Show();
	}
}
?>