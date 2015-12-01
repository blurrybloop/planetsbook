<?php 
require_once 'ControllerBase.php';

class PulseController extends ControllerBase
{    
    function setActions(){
        $this->actions = [];
    }

    //чистка временной папки
    function cleanUp(){
        if ($res = $this->db->fetch('SELECT id FROM temp_pages WHERE TIMESTAMPDIFF(SECOND, last_access, NOW()) > ' . ($this->app->config['pulse']['frequency'] + $this->app->config['pulse']['max_diff']))){
            foreach ($res as $val){
                $this->db->delete('temp_pages', ['id' => $val['id']]);
                $tmp_files = glob(PATH_TEMP . $val['id'] . '_*.*', GLOB_NOSORT);
                foreach ($tmp_files as $file)
                    @unlink($file);
            }
        }
    }
    
	function process($action){
        try {
            $this->cleanUp();
            if (isset($_POST['page_id']) && is_numeric($_POST['page_id']) && isset($this->data['user']['id'])) {
                $this->db->query('UPDATE temp_pages SET last_access=NOW() WHERE id='  . $_POST['page_id'] . ' AND user_id=' . $this->data['user']['id']);
            }
        }
        catch (Exception $ex) {}
	}

    function render(){}
}
?>