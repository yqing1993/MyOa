<?php
class IndexAction extends loginAction {
	function __construct(){
		parent::__construct();
	}

	public function index(){
    	$this->display();
	}

}