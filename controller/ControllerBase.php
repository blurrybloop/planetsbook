<?php

abstract class ControllerBase
{
	public $db;
	public $data=array();

    function __construct($db, array $data = NULL) {
        $this->db = $db;
        $this->data =$data;
    }

	function renderView($view) {
		include(PATH_VIEW . $view . '.php');
	}

    function setData(array $data){
        $this->data = $data;
    }

    abstract function process();
    abstract function render();

    function show() {
        $this->process();
        $this->render();
    }
}