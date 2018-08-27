<?php
class IndexAction extends loginAction {
	function __construct(){
		parent::__construct();
	}

	public function index(){
    	$user_info = get_user_info();
    	$this->assign("user_info",$user_info);
		$this->display();
	}

}