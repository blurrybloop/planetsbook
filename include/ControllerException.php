<?php

class ControllerException extends Exception
{
    protected $details;

    function __construct($message, $details = '',  $code = 0, Exception $previous = NULL){    
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    function __toString() {
        return $this->message;
    }

    function getDetails(){
        return $this->details;
    }
}
