<?php

require_once 'ControllerBase.php';

class StorageController extends ControllerBase
{
    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->showErrorPage = FALSE;
        $this->actions = ['fetch', 'upload', 'delete', 'descript'];
        $this->validateRights([USER_REGISTERED]);
    }

    function fetch(){
        if (!($c=$this->db->fetch('SELECT COUNT(*) AS c FROM storage')))
		throw new ControllerException('Не удалось получить список файлов.');
	$c = $c[0]['c'];

        $sql = 'SELECT * FROM storage WHERE ';
        if (isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id']) && $_REQUEST['user_id'] > 0){
            $this->validateRights([$_REQUEST['user_id'], USER_ADMIN]);
            $sql .= ' user_id=' . $_REQUEST['user_id'] . ' AND';
        }
        if (isset($_REQUEST['search']) && is_string($_REQUEST['search'])){
            $sql .= ' (description LIKE "%' . $_REQUEST['search'] . '%" OR id LIKE "%' . $_REQUEST['search'] . '%") AND';
        }
        if (!isset($_REQUEST['sort']) || !is_numeric($_REQUEST['sort'])){
            $_REQUEST['sort'] = 0;
        }

	if (substr($sql, -3) == 'AND') $sql = substr($sql, 0, -3);
	if (substr($sql, -6) == 'WHERE ') $sql = substr($sql, 0, -6);


        if ($_REQUEST['sort'] == 0) $sql .= ' ORDER BY id ';
        if ($_REQUEST['sort'] == 1) $sql .= ' ORDER BY add_date DESC ';

        if (!isset($_REQUEST['page']) || !is_numeric($_REQUEST['page']) || $_REQUEST['page'] <=0) $_REQUEST['page'] = 1;
	$count_page=(int)(($c-1)/15)+1;
	if ($_REQUEST['page'] > $count_page) $_REQUEST['page'] = $count_page; 
        
	$sql .= ' LIMIT ' . (15*($_REQUEST['page']-1)) . ',15';
        $res = $this->db->fetch($sql);
        foreach ($res as &$r)
            $r['href'] = $this->app->config['path']['storage'] . $r['id'] . '.' . $r['extension'];
        unset($r);

        $this->data['fetched'] =$res;
	$this->data['page']['start'] = count($res) == 0 ? 0 : 15*($_REQUEST['page']-1)+1;
	$this->data['page']['end'] = count($res) == 0 ? 0 : $this->data['page']['start'] + count($res)-1;
        $this->data['page']['current'] = $_REQUEST['page'];
    }

    function upload(){

        $this->validateArgs($_FILES, [['files', 'array']]);

        $img_errors = [
                        0 => 'Неизвестная ошибка',
                        1 => 'Превышен максимальный размер файла, указанный в php.ini',
                        2 => 'Превышен максимальный размер файла, указанный в форме HTML',
                        3 => 'Была отправлена только часть файла',
                        4 => 'Файл для отправки не был выбран.'];

        $errors = [];
        $ext;
        $this->data['uploaded'] = [];

	file_put_contents('dump.txt', count($_FILES['files']['name']));

        foreach ($_FILES['files']['name'] as $i => $name){

            if ($_FILES['files']['error'][$i] != 0){
                if (!array_key_exists($_FILES['files']['error'][$i], $img_errors)) $k = 0;
                else $k = $_FILES['files']['error'][$i];

                $errors[] =  $name . ' - ' . $img_errors[$k];
                continue;
            }

            if (!is_uploaded_file($_FILES['files']['tmp_name'][$i])){
                $errors[] = $name . ' - Выбранный файл не является загруженным.';
                continue;
            }

            if (!($s = getimagesize($_FILES['files']['tmp_name'][$i]))){
                $errors[] = $name . ' - Выбранный файл не является допустимым форматом изображения.';
                continue;
            }

            list($type, $subtype) = explode('/', strtolower($s['mime']));

            if ($type != 'image') {
                $errors[] = $name . ' - Файл не является допустимым форматом изображения.';
                continue;
            }

            if ($subtype == 'gif') $ext = 'gif';
            else if ($subtype == 'jpeg') $ext = 'jpg';
            else if ($subtype == 'png') $ext = 'png';
            else {
                $errors[] = $name . ' - Файл не является допустимым форматом изображения.';
                continue;
            }

            $this->db->transactionStart();

            $this->db->insert('storage', [
                'extension' => $ext,
                'user_id' => $this->data['user']['id'],
                ]);

            if (!@move_uploaded_file($_FILES['files']['tmp_name'][$i], PATH_STORAGE . $this->db->lastInsertId() . '.' . $ext)){
                $this->db->transactionRollback();
                $errors[] = $name . ' - Не удалось сохранить загруженный файл.';
            }

            $this->data['uploaded'][] = ['href' => $this->app->config['path']['storage'] . $this->db->lastInsertId() . '.' . $ext, 'id' => $this->db->lastInsertId()];

            $this->db->transactionCommit();
        }

        if (!empty($errors))
            throw new ControllerException('Не удалось загрузить одно или несколько изображений', json_encode($errors));
    }

    function descript(){
	$this->validateArgs($_REQUEST, [['file_id', 'numeric'], ['text', 'string']]);
	$desc = strip_tags(trim($_REQUEST['text']));
	if (!preg_match('#^.{0,50}$#u', $desc))
		throw new ControllerException('Неправильный формат описания.');

	$id = $_REQUEST['file_id'];
 	if (!($file = $this->db->fetch('SELECT id, user_id FROM storage WHERE id=' . $id)))
                throw new ControllerException('Файл не существует.');

        $this->validateRights([USER_ADMIN, $file[0]['user_id']]);
	$this->db->update('storage', ['description' => $desc], ['id' => $id]);
    }

    function delete(){
        $this->validateArgs($_REQUEST, [['file_id', 'array']]);
        $ids = (array)$_REQUEST['file_id'];
        $errors = [];

        foreach ($ids as $id){

            if (!($file = $this->db->fetch('SELECT id, extension, user_id FROM storage WHERE id=' . $id)))
                throw new ControllerException('Файл не существует.');

            $file = $file[0];
            $this->validateRights([USER_ADMIN, $file['user_id']]);

            $this->db->transactionStart();

            $this->db->delete('storage', ['id' => $file['id']]);
            if (file_exists(PATH_STORAGE . $file['id'] . '.' . $file['extension'])){
                if (!@unlink(PATH_STORAGE . $file['id'] . '.' . $file['extension'])){
                    $this->db->transactionRollback();
                    $errors[] = $id . ' - Не удалось удалить файл.';
                }
            }
            if (!empty($errors))
                throw new ControllerException('Не удалось удалить одно или несколько изображений', json_encode($errors));

            $this->db->transactionCommit();

         }
    }

    function process($action){
        if (!empty($action)) {
            $this->data['action'] = $action;
            $this->$action();
        }
    }

    function render(){
        if ($this->data['action'] == 'fetch'){
            echo json_encode(['fetched' => $this->data['fetched'], 'page' => $this->data['page']]);
        }
        else if ($this->data['action'] == 'upload'){
            echo json_encode($this->data['uploaded']);
        }
        else echo json_encode([]);
    }
}
