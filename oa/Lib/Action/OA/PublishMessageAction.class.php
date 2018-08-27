<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/14
 * Time: 13:47
 */

class PublishMessageAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('PublishMessage',3);
    }
    public function index(){
        $this->assign('ActionName', $this->getActionName());
        //获取所有的部门
        $dapts = m('department')->field('id,DepartmentName')->select();
        $this->assign('dapts',$dapts);
        $this->display();
    }

    public function Data(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $dapts = $_POST['dapts'];
        //echo json_encode($dapts);die;
        $operate = ($_POST['operate']?$_POST['operate']:'');
        if($operate != ''){
            //根据部门的id获取所有的属于当前部门的人员
            $ids = get_ids($dapts);
            $add['title'] = $title;
            $add['content'] = htmlspecialchars($content);
            $add['noread'] = implode(',',$ids);
            $add['addtime'] = time();
            $add['publisher'] = $_SESSION['username'];
            $add['dapts'] = implode(',',$dapts);
            $res = m('message')->add($add);
            //echo json_encode($add);die;
            //echo json_encode(m('message')->getLastSql());die;
            if($res){
                records('SendMessage',$add['publisher'].'发布了消息：'.$add['title']);
                alert('发布成功',1);
            }else{
                alert("发布失败，请重试！");
            }

        }else{
            alert('不知道你要干什么');
        }
    }
}