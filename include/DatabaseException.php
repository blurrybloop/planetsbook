<?php

class DatabaseException extends Exception
{
    function __construct($message, $code, Exception $previous = NULL){    
        parent::__construct($message, $code, $previous);
    }

    function __toString() {
        return 'Ошибка базы данных #' . $this->code . ': ' . $this->message;
    }
}