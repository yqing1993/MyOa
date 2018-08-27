<?php
class WelcomeAction extends loginAction {
	function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->display();
	}
}