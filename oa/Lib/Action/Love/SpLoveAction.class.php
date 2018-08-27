<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/6
 * Time: 15:38
 */
class SpLoveAction extends loginAction{
    function _construct(){
        parent::__construct();
        role('SpLove',3);
    }
    public function index(){
        $send_user = I('post.send_user');
        $para = "role NOT REGEXP '\"[1]\"'";
        if($send_user != ''){
            $para .= " and send_user = '$send_user'";
        }

//        $begin=mktime(0,0,0,date('m'),1,date('Y'));
//        $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
//        dump($begin);
//        dump(date('Y-m-d H:i:s',$begin));
//        dump($end);
//        dump(date('Y-m-d H:i:s',$end));die;
        //获取出超级管理员以外的人

        $users = m('user')->where($para)->field('username')->select();
        $this->assign('user',$users);
        $ActionName = $this->getActionName();
        $this->assign('ActionName',$ActionName);
        $this->display();
    }
    public function SpLovePage(){
        $send_user = I('post.send_user');
        $time = time();
        $begin=mktime(0,0,0,date('m'),1,date('Y'));
        $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $username = $_SESSION['username'];
        $para = "and $begin <send_time<$end";
        if($send_user != ''){
            $para .= " and send_user = '$send_user'";
        }
        $data = DataPage(array('database'=>'love','order'=>'send_time desc','para'=>$para));
        echo json_encode($data);
    }

    public function SpLoveData(){
        $id = I('post.id');
        $username = $_SESSION['username'];
        $userid = $_SESSION['userid'];
        $time = time();

        if($id != ''){
            $operate = I('post.operate','');
            if($operate != ''){
                $database = 'love';
                $RecordInfo = array(
                    'Head'=>'SpLove',
                    'Name'=>'审批',
                    'NameKey'=>'name'
                );
                //判断当前的数据还在不在
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){
                    //echo json_encode($info);die;
                    $data['sp_status'] = '1';
                    if($operate == 'UpdateAdd'){
                        $data['sp_res'] = '0';
                        $data['fail_reason'] = I('post.fail_reason');
                        $res = m($database)->where("id = '".$id."'")->save($data);
                        //$sql = m($database)->getLastSql();
                        //echo json_encode($sql);die;
                        if($res){
                            records($RecordInfo['Head'],$username."审批了爱心请求，ID = ".$id);
                            alert("录入成功",1);
                        }else{
                            alert('录入失败，请重试！');
                        }
                    }elseif($operate=='Get'){
                        $info = m($database)->where("id = '".$id."'")->find();
                        if(!empty($info)){

                            $info['error'] = '1';

                            echo json_encode($info);
                        }else{
                            alert("该条信息没有找到，请刷新页面重试");
                        }
                    }elseif($operate=='status_ok'){
                        //$data = I('post.');
                        //echo json_encode($data);
                        $data['sp_res'] = '1';
                        $res = m($database)->where("id = '".$id."'")->save($data);
                        if($res){
                            records($RecordInfo['Head'],$username.'在'.date('Y-m-d H:i:s',$time).'审核了由 , '.$info['username'].' ,提交的关键词： '.$info['name'].' , 通过');
                            alert('审批成功！',1);
                        }else{
                            alert('审批失败，刷新页面重试！ ');
                        }
                    }else if($operate=='status_no'){
                        $data['sp_res'] = '0';
                        $res = m($database)->where("id = '".$id."'")->save($data);
                        if($res){
                            records($RecordInfo['Head'],$username.'在'.date('Y-m-d H:i:s',$time).'审核了由 , '.$info['username'].' ,提交的关键词： '.$info['name'].' , 未通过');
                            alert('审批成功！',1);
                        }else{
                            alert('审批失败，刷新页面重试！ ');
                        }
                    }else{
                        alert("不知道你要干啥");
                    }

                }else{
                    alert("审批失败，没有找到该条数据，请刷新页面重试");
                }

            }else{
                alert('出错，你这是什么操作？');
            }

        }else{
            alert("出错，提交的ID为空");
        }
    }

}
