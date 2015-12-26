<?php

require_once 'ControllerBase.php';

class MenuController extends ControllerBase
{

	function process($action){
        if (!$this->db) return FALSE;

        try { $res = $this->db->fetch('SELECT sections.id AS id, title, data_folder, parent_id, allow_user_articles, show_main, sections.description AS description, type, DATE_FORMAT(creation_date, "%e.%m.%Y") AS creation_date, CONCAT(big_image, ".", big.extension) AS big_file, CONCAT(small_image, ".", small.extension) AS small_file FROM sections LEFT JOIN storage big ON big_image=big.id LEFT JOIN storage small ON small_image=small.id'); }
        catch (DatabaseException $ex){ return TRUE; }
        foreach ($res as &$val) {
            $val['href'] = $this->app->config['path']['section'] . $val['data_folder'] . '/';
            $val['big_file'] = $this->app->config['path']['storage'] . $val['big_file'];
            $val['small_file'] = $this->app->config['path']['storage'] . $val['small_file'];
        }
        unset($val);
        $this->data['menu'] = $res;
        return TRUE;
	}

    function render($suppressOutput = TRUE){
        if ($suppressOutput) ob_start();
        if (isset($this->data['menu']))
            $this->renderView('menu');
        if ($suppressOutput) return ob_get_clean();
        else return '';
    }
}