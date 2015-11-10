<?php

require_once 'ControllerBase.php';
require_once '/include/TagsParser.php';

class CommentsController extends ControllerBase
{
    public $outputMode = 0;
    public $parser;
    private $h = FALSE;


    function __construct($db, array $data = NULL){
        parent::__construct($db, $data);
        $this->parser = new TagsParser;
    }

    function getUser($id){
        $res = $this->db->fetch("SELECT rating FROM users WHERE id=$id LIMIT 1 UNION SELECT COUNT(*) FROM comments INNER JOIN users ON comments.user_id=users.id WHERE users.id=$id");
        if (!$res || count($res) < 2) throw new ControllerException('Произошла ошибка.<br/>Повторите действие позже.');
        $this->data['user']['rating'] = $res[0];
        $this->data['user']['comm_cnt'] = $res[1];
    }

    function validateRights(array $users = NULL, $idComment = 0, $throw = TRUE){
        if ($users === NULL) $users = [];
        if ($idComment){
            $res = $this->db->fetch("SELECT users.id AS user_id FROM comments INNER JOIN users ON (users.id=comments.user_id) WHERE comments.id=$idComment");
            if (!$res) {
                if ($throw) throw new ControllerException('Произошла ошибка проверки прав доступа.');
                else return FALSE;
            }
            array_push($users, $res[0]['user_id']);
        }
        return parent::validateRights($users, $throw); 
    }

    function help(){
        $this->h = TRUE;
    }

    function rate($id, $val){
        $this->validateRights([USER_REGISTERED]);
        try {
            $this->db->insert('rates', [
                'comment_id'    =>      $id,
                'user_id'       =>      $this->data['user']['id'],
                'value'         =>      $val,
            ]);
        }
        catch (DatabaseException $ex){
            if ($ex->getCode() == 1062) 
                throw new ControllerException('Вы уже оценили этот комментарий.');
            else throw $ex;
        }
        $this->html($id);
    }

    function add($article, $text){
        $this->validateRights([USER_REGISTERED]);
        $this->db->insert('comments', [
                'article_id'    =>      $article,
                'user_id'       =>      $this->data['user']['id'],
                'comm_text'     =>      strip_tags($text),
            ]);
        $this->html($this->db->lastInsertId());
    }

    function edit($id, $text){
        $this->validateRights(NULL, $id);
        $text = strip_tags($text);
        $this->db->update('comments', [
                'comm_text'     =>      strip_tags($text),
            ], 
            [
                'id'            => $id
            ]);
        $this->html($id);
    }

    function text($id = 0){
        if ($id == 0){
            $this->validateRights([USER_REGISTERED]);
            $this->data['comments'][0] = $this->data['user'];
            $this->outputMode = 1;
            return;
        }
        $this->validateRights(NULL, $id);
        $res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.id = $id");
        if (!$res) 
            throw new ControllerException('Комментарий не существует.');
            $this->data['comments'][0] = $res[0];
            $this->outputMode = 1;
    }

    function html($id){
        $this->validateRights([USER_ANY]);
        $res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.id = $id");
        if (!$res) throw new ControllerException('Комментарий не существует.');
        $this->data['comments'][0] = $res[0];
    }

    function preview($text){
        $this->validateRights([USER_REGISTERED]);
        $this->data['comments'][0] = $this->data['user'];
        $this->parser->text = strip_tags($text);
        $this->data['comments'][0]['comm_text'] = $this->parser->parse();
        $this->data['comments'][0]['add_date'] = date('d.m.Y H:i');
        $this->outputMode = 2;
    }

    function delete($id){
        $this->validateRights([USER_ADMIN], $id);
        $this->db->delete('comments', ['id' => $id]);
    }

    function fetch($article, $page, $pagesize){
        $this->validateRights([USER_ANY]);
        if (!is_numeric($article) || !is_numeric($page) || !is_numeric($pagesize)) throw new ControllerException('Неправильные параметры запроса.');
        $count = $this->db->fetch("SELECT COUNT(*) AS c FROM comments WHERE article_id=$article", 1)[0]['c'];

        $count_page=(int)(($count-1)/$pagesize)+1;
        if ($page>$count_page) return;
        if ($page==0) $page=1; 

        $res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, SUM(rates.value) as rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.article_id = $article GROUP BY comments.id ORDER BY add_date DESC LIMIT " . (($page-1)*$pagesize) . ",{$pagesize}");
        $this->data['comments'] = $res;
    }

    function process() {
        if (!isset($_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        if (!is_array($args = $_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        $action = strtolower($_REQUEST['param1']);

        if ($action == 'like'){
            $action = 'rate';
            $args[] = 1;
        }
        else if ($action == 'dislike') {
            $action = 'rate';
            $args[] = -1;
        }

        call_user_func_array(get_class($this) . '::' . $action, $args);
    }

    function render(){
        if ($this->h)
            $this->renderView('bbhelp');
        else
            $this->renderView('comment');
    }
}
