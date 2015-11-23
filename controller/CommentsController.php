<?php

require_once 'ControllerBase.php';
require_once PATH_INCLUDE . 'TagsParser.php';

const OUT_NORMAL = 0;
const OUT_TEXT = 1;
const OUT_PREVIEW = 2;
const OUT_HELP = 3;

class CommentsController extends ControllerBase
{

    public $outputMode = OUT_NORMAL;
    public $parser;

    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->showErrorPage = FALSE;
        $this->parser = new TagsParser;
    }

    function setActions(){
        $this->actions = ['fetch', 'add', 'edit', 'delete', 'preview', 'html', 'text', 'rate', 'help'];
    }

    //проверка прав доступа к комментарию
    function validateRights(array $users = NULL, $idComment = 0, $throw = TRUE){
        if ($users === NULL) $users = [];
        if ($idComment){
            if (!($res = $this->db->fetch("SELECT users.id AS user_id FROM comments INNER JOIN users ON (users.id=comments.user_id) WHERE comments.id=$idComment"))){
                if ($throw) throw new ControllerException('Произошла ошибка проверки прав доступа.');
                else return FALSE;
            }
            $users[] = $res[0]['user_id'];
        }
        return parent::validateRights($users, $throw);
    }

    //получение списка комментариев
    function fetch(){
        $this->validateRights([USER_ANY]);
        $this->validateArgs($_POST, [['article_id', 'numeric'], ['page', 'numeric'], ['page_size', 'numeric']]);

        $count = $this->db->fetch('SELECT COUNT(*) AS c FROM comments WHERE article_id=' . $_POST['article_id'], 1)[0]['c'];

        $count_page=(int)(($count-1)/$_POST['page_size'])+1;
        if ($_POST['page']>$count_page) return;
        if ($_POST['page']==0) $_POST['page']=1;

        $res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS date_add, SUM(rates.value) as rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.article_id = " . $_POST['article_id'] . " GROUP BY comments.id ORDER BY add_date DESC LIMIT " . (($_POST['page']-1)*$_POST['page_size']) . ",{$_POST['page_size']}");
        $this->data['comments'] = $res;
    }

    //добавление
    function add(){
        $this->validateArgs($_POST, [['article_id', 'numeric'], ['text']]);

        $article = $_POST['article_id'];
        $text = $_POST['text'];

        $this->validateRights([USER_REGISTERED]);
        $this->db->insert('comments', [
                'article_id'    =>      $article,
                'user_id'       =>      $this->data['user']['id'],
                'comm_text'     =>      strip_tags($text),
            ]);
        $_POST['comment_id'] = $this->db->lastInsertId();
        $this->html();
    }

    //редактирование
    function edit(){
        $this->validateArgs($_POST, [['comment_id', 'numeric'], ['text']]);

        $id = $_POST['comment_id'];
        $text = $_POST['text'];

        $this->validateRights(NULL, $id);
        $this->db->update('comments', ['comm_text' => strip_tags($text)], ['id' => $id]);
        $this->html();
    }

    //удаление
    function delete(){
        $this->validateArgs($_POST, [['comment_id', 'numeric']]);
        $id = $_POST['comment_id'];
        $this->validateRights([USER_ADMIN], $id);
        $this->db->delete('comments', ['id' => $id]);
    }
 
    //предпросмотр
    function preview(){
        $this->validateArgs($_POST, [['text']]);

        $text = $_POST['text'];
        $this->validateRights([USER_REGISTERED]);
        $this->data['comments'][0] = $this->data['user'];
        $this->parser->text = strip_tags($text);
        $this->data['comments'][0]['comm_text'] = $this->parser->parse();
        $this->data['comments'][0]['date_add'] = date('d.m.Y H:i');
        $this->outputMode = OUT_PREVIEW;
    }

    //html комментария
    function html(){
        $this->validateArgs($_POST, [['comment_id', 'numeric']]);
        $this->validateRights([USER_ANY]);

        if (!($res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS date_add, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.id = " . $_POST['comment_id'])))
            throw new ControllerException('Комментарий не существует.');

        $this->data['comments'][0] = $res[0];
    }

    //текст комментария с bb-кодами
    function text(){
        $this->validateArgs($_POST, [['comment_id', 'numeric']]);
        $id = $_POST['comment_id'];

        if ($id == 0){
            $this->validateRights([USER_REGISTERED]);
            $this->data['comments'][0] = $this->data['user'];
            $this->outputMode = OUT_TEXT;
            return;
        }

        $this->validateRights(NULL, $id);
        if (!($res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS date_add, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.id = $id")))
            throw new ControllerException('Комментарий не существует.');

        $this->data['comments'][0] = $res[0];
        $this->outputMode = OUT_TEXT;
    }

    //оценка
    function rate(){
        $this->validateArgs($_POST, [['comment_id', 'numeric'], ['value', 'numeric']]);

        $id = $_POST['comment_id'];
        $val = $_POST['value'];

        if ($val != 0){
            $this->validateRights([USER_REGISTERED]);
            try {
                $this->db->insert('rates', [
                    'comment_id'    =>      $id,
                    'user_id'       =>      $this->data['user']['id'],
                    'value'         =>      $val > 0 ? 1 : -1,
                ]);
            }
            catch (DatabaseException $ex){
                if ($ex->getCode() == 1062)
                    throw new ControllerException('Вы уже оценили этот комментарий.');
                else throw $ex;
            }
        }
        $this->html();
    }

    //вывод помощи по BB-кодам
    function help(){
        $this->outputMode = OUT_HELP;
    }

    function process($action) {
        if (!empty($action))
            $this->$action();
    }

    function render(){
        if ($this->outputMode == OUT_HELP)
            $this->renderView('bbhelp');
        else
            $this->renderView('comment');
    }
}
