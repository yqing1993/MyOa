<?php
class IndexAction extends loginAction {
	function __construct(){
		parent::__construct();
	}

	public function index(){
    	$user_info = get_user_info();
    	$id = $_SESSION['id'];
    	//dump($user_info);die;
    	$w = m('message')->where("FIND_IN_SET($id,noread)")->count();
    	//dump(m('message')->getLastSql());die;
    	$y = m('message')->where("FIND_IN_SET($id,readed)")->count();
        //dump(m('message')->getLastSql());die;
    	$this->assign('w',$w);
    	$this->assign('y',$y);
    	$this->assign("user_info",$user_info);
		//C('TMPL_FILE_DEPR','_');
		$this->display();
	}

}