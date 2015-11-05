<?php

require_once 'ControllerException.php';

class Application
{
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

            if ($menu != 'pulse'){
                if (!is_file(PATH_CONTROLLER . $menu . 'Controller.php'))
                    throw new ControllerException('', '', 404);

                include(PATH_CONTROLLER.$menu.'Controller.php');
                $class=strtolower($menu).'Controller';

                if (!class_exists($class))
                    throw new ControllerException('', '', 404);
            }

            $db=new mysql(['pass'=>'71295','db'=>'planetsbook', 'charset'=>'utf8']);
            if (!$db->connect()){
                $err = $db->last_error();
                $db = NULL;
                throw new ControllerException('Не удалось открыть подключение к серверу MySQL.', $err);
            }
            $this->cleanUp($db);

            if ($menu == 'pulse') include 'pulse.php';

            if ($menu != 'pulse'){
                $c=new $class($db);
                $c->show();
            }
        }

        catch (ControllerException $ex) {
            require_once (PATH_CONTROLLER . 'ErrorController.php');
            $ec = new ErrorController(isset($db) ? $db : NULL, $ex);
            $ec->showErrorPage = isset($c) ? $c->showErrorPage : TRUE;
            $ec->show();
            return;
        }

	}
}
?>