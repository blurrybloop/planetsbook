<?php

class HttpException extends Exception
{
    function __construct($code, Exception $previous = NULL){    
        parent::__construct('', $code, $previous);
    }

    public function __toString(){
        return 'HTTP/1.1 ' . $this->code;
    }
}
