<?php

require_once 'MenuController.php';

class ErrorController extends MenuController
{
    private $exception;

    function __construct($db, $exception = NULL, array $data = NULL){
        parent::__construct($db, $data);
        $this->exception = $exception;
        $this->showErrorPage = TRUE;
    }

    function process(){
       
        if ($this->db) parent::process();
        switch (get_class($this->exception)){
            case 'ControllerException':
                $this->data['error'] = '<p>' . $this->exception . '</p>';
                if ($d = trim($this->exception->getDetails())) $this->data['error'] .= '<p class="details">' . $d . '</p>';
                http_response_code(500);
                break;
            case 'DatabaseException':
                $this->data['error'] = '<p>' . $this->exception . '</p>';
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
                $this->data['error'] = '<p>' . $this->exception . '</p>';
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
