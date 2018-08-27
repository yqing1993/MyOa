<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/6/29
 * Time: 11:57
 */

class IndexAction extends loginAction {
    function __construct(){
        parent::__construct();
    }

    public function index(){
        //echo 'hello';
        $user_info = get_user_info();
        $this->assign("user_info",$user_info);
        $username = $_SESSION['username'];
        //C('TMPL_FILE_DEPR','_');
        //获取当前的用户
        $role = $_SESSION['role'];
        //echo json_encode($role);die;
        if($role == '1'){
            //超级管理员审批品牌专员的
            $num = m('keywords')->where("sp_status = '0' and sp_role = '1'" )->count();
        }else{
            $num = m('keywords')->where("sp_status = '0' and sp_role = '2'" )->count();
        }
        //根据当前的用户获取接受的词数量
        $number = m('k_distribution')->where("is_operate = '0' and username = '$username'")->count();
        $numbers = m('keywords')->where("(rank_search = '' or rank_search = '0') and sp_res = '1' and CategoryID = 1")->count();
        $this->assign('num',$num);
        $this->assign('number',$number);
        $this->assign('numbers',$numbers);
        $this->display();
    }

}