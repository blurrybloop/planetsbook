<?php

abstract class ControllerBase
{
	public $db;
	public $data=array();
	
	function render($view) {
		include(PATH_VIEW . $view . '.php');
	}

    abstract function show();
}