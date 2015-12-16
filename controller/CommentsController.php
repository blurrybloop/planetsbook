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
        $this->validateArgs($_GET, [['article_id', 'numeric'], ['page', 'numeric'], ['page_size', 'numeric']]);

        $count = $this->db->fetch('SELECT COUNT(*) AS c FROM comments WHERE article_id=' . $_GET['article_id'], 1)[0]['c'];

        $count_page=(int)(($count-1)/$_GET['page_size'])+1;
        if ($_GET['page']>$count_page) return;
        if ($_GET['page']==0) $_GET['page']=1;

        $res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS date_add, SUM(rates.value) as rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.article_id = " . $_GET['article_id'] . " GROUP BY comments.id ORDER BY add_date DESC LIMIT " . (($_GET['page']-1)*$_GET['page_size']) . ",{$_GET['page_size']}");
        $this->data['comments'] = $res;
    }

    //добавление
    function add(){
        $this->validateArgs($_GET, [['article_id', 'numeric'], ['text']]);

        $article = $_GET['article_id'];
        $text = $_GET['text'];

        $this->validateRights([USER_REGISTERED]);
        $this->db->insert('comments', [
                'article_id'    =>      $article,
                'user_id'       =>      $this->data['user']['id'],
                'comm_text'     =>      strip_tags($text),
            ]);
        $_GET['comment_id'] = $this->db->lastInsertId();
        $this->html();
    }

    //редактирование
    function edit(){
        $this->validateArgs($_GET, [['comment_id', 'numeric'], ['text']]);

        $id = $_GET['comment_id'];
        $text = $_GET['text'];

        $this->validateRights(NULL, $id);
        $this->db->update('comments', ['comm_text' => strip_tags($text)], ['id' => $id]);
        $this->html();
    }

    //удаление
    function delete(){
        $this->validateArgs($_GET, [['comment_id', 'numeric']]);
        $id = $_GET['comment_id'];
        $this->validateRights([USER_ADMIN], $id);
        $this->db->delete('comments', ['id' => $id]);
    }
 
    //предпросмотр
    function preview(){
        $this->validateArgs($_GET, [['text']]);

        $text = $_GET['text'];
        $this->validateRights([USER_REGISTERED]);
        $this->data['comments'][0] = $this->data['user'];
        $this->parser->text = strip_tags($text);
        $this->data['comments'][0]['comm_text'] = $this->parser->parse();
        $this->data['comments'][0]['date_add'] = date('d.m.Y H:i');
        $this->outputMode = OUT_PREVIEW;
    }

    //html комментария
    function html(){
        $this->validateArgs($_GET, [['comment_id', 'numeric']]);
        $this->validateRights([USER_ANY]);

        if (!($res = $this->db->fetch("SELECT login, is_admin, DATE_FORMAT(reg_date, '%e.%m.%Y %H:%i') AS reg_date, avatar, users.id AS user_id, rating, comments_cnt, comments.id, comm_text, DATE_FORMAT(add_date, '%e.%m.%Y %H:%i') AS date_add, SUM(rates.value) AS rate FROM comments INNER JOIN users ON (users.id = comments.user_id) LEFT JOIN rates ON rates.comment_id = comments.id WHERE comments.id = " . $_GET['comment_id'])))
            throw new ControllerException('Комментарий не существует.');

        $this->data['comments'][0] = $res[0];
    }

    //текст комментария с bb-кодами
    function text(){
        $this->validateArgs($_GET, [['comment_id', 'numeric']]);
        $id = $_GET['comment_id'];

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
        $this->validateArgs($_GET, [['comment_id', 'numeric'], ['value', 'numeric']]);

        $id = $_GET['comment_id'];
        $val = $_GET['value'];

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
        if ($this->outputMode == OUT_HELP) {
            ob_start();
            $this->renderView('bbhelp');
            echo json_encode(['help' => ob_get_clean()]);
        }
        else{
            if (isset($this->data['comments'])){
                foreach ($this->data['comments'] as &$c){
                    if (!isset($c['rate'])) $c['rate']=0;
                    if (!isset($c['comm_text'])) $c['comm_text'] = "";

                    $c['status'] = $c['is_admin'] ? 'Администратор' : 'Пользователь';
                    $c['avatar'] = $this->app->config['path']['avatar'] . $c['avatar'] . '.png';

                    if ($this->outputMode == OUT_NORMAL){
                        $this->parser->text = $c['comm_text'];
                        $c['comm_text'] = $this->parser->parse();
                    }

                    $c['allow_rate'] = isset($this->data['user']);
                    $c['allow_edit'] = $this->validateRights(NULL, $c['id'], FALSE);
                    $c['allow_delete'] = $this->validateRights([USER_ADMIN], $c['id'], FALSE);
                }
                unset($c);
                echo json_encode(['mode' => $this->outputMode,  'comments' => $this->data['comments']]);
            }
            else echo json_encode([]);
        }
    }
}
