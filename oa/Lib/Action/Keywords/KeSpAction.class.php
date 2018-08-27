<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/5
 * Time: 15:45
 */

//关键词审批
class KeSpAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('KeSp',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());
        $this->display();
    }

    public function KeSpPage(){
        //判断当前登录的人是谁
        //alert('35546');die;
        //$role = $_SESSION;
        $role = $_SESSION['role'];
        //echo json_encode($role);die;
        if($role == '1'){
            //超级管理员审批品牌专员的
            $data = DataPage(array('database'=>'keywords','order'=>'addtime desc','para'=>" and sp_status = '0' and sp_role = '1'"));
            $data = get_info($data,'1');
        }else{
            $data = DataPage(array('database'=>'keywords','order'=>'addtime desc','para'=>" and sp_status = '0' and sp_role = '2'"));
            $data = get_info($data,'1');
        }
        echo json_encode($data);

    }
    public function KeSpData(){
        $id = I('post.id');
        $username = $_SESSION['username'];
        $userid = $_SESSION['userid'];
        $rank_search = I('post.rank_search');
        $search_number = I('post.search_number');
        $name = I('post.name');
        $time = time();

        if($id != ''){
            $operate = I('post.operate','');
            if($operate != ''){
                $database = 'keywords';
                $RecordInfo = array(
                    'Head'=>'KeSp',
                    'Name'=>'审批',
                    'NameKey'=>'name'
                );
                //判断当前的数据还在不在
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){
                    //echo json_encode($info);die;
                    $data['sp_status'] = '1';
                    $data['sp_userid'] = $userid;
                    $data['sp_username'] = $username;
                    $data['sp_time'] = $time;
                    $data['sp_role'] = '0';
                    if($operate == 'UpdateAdd'){
                        $data['rank_search'] = $rank_search;
                        $data['search_number'] = $search_number;
                        $data['sp_res'] = '1';
                        $res = m($database)->where("id = '".$id."'")->save($data);
                        //$sql = m($database)->getLastSql();
                        //echo json_encode($sql);die;
                        if($res){
                            records($RecordInfo['Head'],$username."审批了关键词 ".$name." ，关键词级别为：".$data['rank_search']."，关键词搜索量为：". $data['search_number'] = $search_number." ，ID = ".$id);
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