<?php

require_once 'ControllerBase.php';
require_once '/include/TagsParser.php';

class CommentsController extends ControllerBase
{
    public $outputMode = 0;
    public $parser;

    function __construct($db, array $data = NULL){
        parent::__construct($db, $data);
        $this->parser = new TagsParser;
    }

    function like($id){
        if (!$this->db->query("INSERT INTO rates VALUES($id, 1, 1)"))
            throw new ControllerException('Вы уже оценили этот комментарий.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->html($id);
    }

    function dislike($id){
        if (!$this->db->query("INSERT INTO rates VALUES($id, 1, -1)"))
            throw new ControllerException('Вы уже оценили этот комментарий.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->html($id);
    }

    function add($article, $text){
        $text = str_replace(["\r", "\n"], '', strip_tags($text));
        if (!$this->db->query("INSERT INTO comments(article_id, user_id, comm_text, add_date) VALUES ($article, 1, \"$text\", now())"))
            throw new ControllerException('Произошла ошибка при добавлении комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->html($this->db->last_insert_id());
    }

    function edit($id, $text){
        $res = $this->db->fetch("SELECT users.id AS user_id FROM comments INNER JOIN users ON (users.id=comments.user_id) WHERE comments.id=$id");
        if (!$res) throw new ControllerException('Произошла ошибка при редактировании комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        if ($res[0]['user_id'] != 1) throw new ControllerException('У вас нет прав для редактирования этого комментария');
        $text = str_replace(["\r", "\n"], '', strip_tags($text));
        if (!$this->db->query("UPDATE comments SET comm_text=\"$text\" WHERE id=$id"))
            throw new ControllerException('Произошла ошибка при редактировании комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->html($id);
    }

    function text($id = 0){
        if ($id == 0){
            $user = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar FROM users WHERE id=1");
            if (!$user) throw new ControllerException('Произошла ошибка при обработке комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
            $this->data[0] = $user[0];
            $this->outputMode = 1;
            return;
        }
        $res = $this->db->fetch("SELECT users.id AS user_id FROM comments INNER JOIN users ON (users.id=comments.user_id) WHERE comments.id=$id");
        if (!$res) throw new ControllerException('Произошла ошибка при редактировании комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        if ($res[0]['user_id'] != 1) throw new ControllerException('У вас нет прав для редактирования этого комментария');

        $res = $this->db->fetch("SELECT comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id FROM comments INNER JOIN users ON (users.id=comments.user_id) WHERE comments.id=$id");
        if (!$res) 
            throw new ControllerException('Произошла ошибка при обработке комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
            $this->data[0] = $res[0];
            $this->outputMode = 1;
    }

    function html($id){
        $res = $this->db->fetch("SELECT comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id=comments.user_id) LEFT JOIN rates ON rates.comment_id=comments.id WHERE comments.id=$id");
        if (!$res) throw new ControllerException('Произошла ошибка при обработке комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->data[0] = $res[0];
    }

    function preview($text){
        $user = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar FROM users WHERE id=1");
        if (!$user) throw new ControllerException('Произошла ошибка при обработке комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
        $this->data[0] = $user[0];
        $this->parser->text = str_replace(['\n', '\r'], '', strip_tags($text));
        $this->data[0]['comm_text'] = $this->parser->parse();
        $this->data[0]['add_date'] = date('d.m.Y H:i');
        $this->outputMode = 2;
    }

    function delete($id){
        file_put_contents('huy.txt', "DELETE FROM comments WHERE id=$id");
        if (!$this->db->query("DELETE FROM comments WHERE id=$id")) throw new ControllerException('Произошла ошибка при удалении комментария.<br/>Повторите действие позже.', 'Ошибка MySQL #' . $this->db->last_error());
    }

    function fetch($article, $page, $pagesize){
        if (!is_numeric($article) || !is_numeric($page) || !is_numeric($pagesize)) throw new ControllerException('Неправильные параметры запроса.');
        $count = $this->db->fetch("SELECT COUNT(*) AS c FROM comments WHERE article_id=$article", 1)[0]['c'];
        if ($count === FALSE) throw new ControllerException('Ошибка при получении списка комментариев', 'Ошибка MySQL #' . $this->db->last_error());

        $count_page=(int)(($count-1)/$pagesize)+1;
        if ($page>$count_page) return;
        if ($page==0) $page=1; 

        $res = $this->db->fetch("SELECT comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS add_date, login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id=comments.user_id) LEFT JOIN rates ON rates.comment_id=comments.id WHERE article_id=$article GROUP BY comments.id ORDER BY add_date DESC LIMIT " . (($page-1)*$pagesize) . ",{$pagesize}");
        if ($res === FALSE) throw new ControllerException('Ошибка при получении списка комментариев', 'Ошибка MySQL #' . $this->db->last_error());

        $this->data = $res;
    }

    function process() {
        if (!isset($_REQUEST['args'])) throw new ControllerException('Неправильные параметры запроса');
        if (!is_array($args = $_REQUEST['args'])) $args=[1,1,10]; //throw new ControllerException('Неправильные параметры запроса');
        $action = strtolower($_REQUEST['param1']);

        call_user_func_array(get_class($this) . '::' . $action, $args);
    }

    function render(){
        $this->renderView('comment');
    }
}
