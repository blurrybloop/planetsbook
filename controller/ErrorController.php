<?php

require_once 'MenuController.php';

class ErrorController extends MenuController
{
    private $exception;

    function __construct($app, $db, array $data = NULL){
        parent::__construct($app, $db, $data);
        $this->exception = isset($data['exception']) && 
                           is_subclass_of($data['exception'], 'Exception') ? 
                                $data['exception'] : 
                                new Exception('Неизвестная ошибка');

        $this->showErrorPage = isset($data['showErrorPage']) ? $data['showErrorPage'] : FALSE;
    }

    function setActions(){
        $this->actions = [];
    }

    function process($action){
       
        if ($this->db) parent::process($action);
        $this->data['error'] = 'Нам очень жаль, но что то пошло не так!<br/>Единственное, что мы можем сказать:<br/>';
        switch (get_class($this->exception)){
            case 'ControllerException':
                $this->data['error'] .= $this->exception;
                if ($d = trim($this->exception->getDetails())) $this->data['error'] .= '<p class="details">' . $d . '</p>';
                http_response_code(500);
                break;
            case 'DatabaseException':
                $this->data['error'] .= $this->exception;
                http_response_code(500);
                break;

            case 'HttpException':
                switch ($this->exception->getCode()){
                    case 403:
                        $this->data['error'] = '<h1>403 <span color="#D4CECE"><span>Forbidden</span></h1>Доступ запрещен.';
                        break;
                    case 404:
                        $this->data['error'] = '<h1>404 <span color="#D4CECE"><span>Not Found</span></h1>Страница, которую вы запросили, не существует.';
                        break;
                    case 500:
                        $this->data['error'] = '<h1>500 <span color="#D4CECE"><span>Internal Server Error</span></h1>Сервер не может выполнить ваш запрос из-за критической ошибки.';
                        break;
                    default:
                        $this->data['error'] = '<h1>' . $this->exception->getCode() . ' <span color="#D4CECE"></h1>Неизвестная Http-ошибка.';
                        break;
                }
                http_response_code($this->exception->getCode());
                break;

            default:
                $this->data['error'] .= 'Произошла неизвестная ошибка';
                http_response_code(500);
                break;
        }
    }

    function render(){
        if ($this->showErrorPage){
            $this->data['menu'] = $this->db ? parent::render() : '';
            $this->renderView('error');
        }
        else{
            echo '<div class="error">' . $this->data['error'] . '</div>';
        }
    }
}
