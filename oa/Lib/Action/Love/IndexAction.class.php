<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/6
 * Time: 9:04
 */

class IndexAction extends loginAction{
    function __construct(){
        parent::__construct();
    }

    public function index(){
        $num = m('love')->where("sp_status = '0'")->count();
        //dump($num);die;
        $user_info = get_user_info();
        $this->assign("user_info",$user_info);$this->assign('num',$num);
        //C('TMPL_FILE_DEPR','_');
        $this->display();
    }
}