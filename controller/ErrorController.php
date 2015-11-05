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
        if ((isset($_REQUEST['param1']) && $_REQUEST['param1'] == '404') || ($this->exception && $this->exception->getCode() == 404)){
            $this->data['error'] = '<h1>404 <span color="#D4CECE"><span>Not Found</span></h1>Страница, которую вы запросили, не существует.';
            http_response_code(404);
        }
        else if ($this->exception) {
            $this->data['error'] = '<p>' . $this->exception . '</p>';
            if ($d = trim($this->exception->getDetails())) $this->data['error'] .= '<p class="details">' . $d . '</p>';
            http_response_code(500);
        }
    }

    function render(){
        if ($this->showErrorPage){
            $this->data['menu'] = $this->db ? parent::render() : '';
            $this->renderView('error');
        }
        else{
            echo $this->data['error'];
        }
    }
}
