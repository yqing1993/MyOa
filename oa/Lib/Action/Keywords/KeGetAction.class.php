<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/5
 * Time: 15:45
 */

//关键词审批
class KeGetAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('KeGet',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());
        $this->display();
    }

    public function KeGetPage(){
        //判断当前登录的人是谁
        $username = $_SESSION['username'];
        $data = DataPage(array('database'=>'k_distribution','order'=>'Dis_time desc','para'=>" and is_operate = '0' and username =  '$username'"));
        $data = get_info($data,'2');
        echo json_encode($data);

    }
    public function KeGetData(){
        $id = I('post.id');
        $username = $_SESSION['username'];
        $userid = $_SESSION['userid'];
        $time = time();

        if($id != ''){
            $operate = I('post.operate','');
            if($operate != ''){
                $database = 'k_distribution';
                $RecordInfo = array(
                    'Head'=>'KeGet',
                    'Name'=>'获取',
                    'NameKey'=>'name'
                );
                //判断当前的数据还在不在
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){
                    //关键词表查找是否存在
                    $name = $info['name'];
                    $result = m('keywords')->where("name = '$name'")->find();
                    //$sql = m('keywords')->getLastSql();
                    //echo json_encode($result);die;
                    if(!empty($result)){
                        $data['is_operate'] = '1';
                        if($operate=='status_ok'){
                            //$data = I('post.');
                            //echo json_encode($data);
                            $Acc_cishu = (int)$result['Acc_cishu'];
                            $Acc_cishu = $Acc_cishu + 1;
                            $ep['Acc_cishu'] = $Acc_cishu;
                            $ex = m('keywords')->where("name = '$name'")->save($ep);
                            //$sql = m('keywords')->getLastSql();
                            //echo json_encode($sql);die;
                            if($ex){
                                $data['Dis_status'] = '1';
                                $res = m($database)->where("id = '".$id."'")->save($data);
                                if($res){
                                    records($RecordInfo['Head'],$username.'在'.date('Y-m-d H:i:s',$time).'接受了由 , '.$info['d_username'].' ,分配的关键词： '.$info['name']);
                                    alert('接受成功！',1);
                                }else{
                                    alert('接受失败，刷新页面重试！ ');
                                }
                            }else{
                                alert('错误，请刷新后重试！');
                            }
                        }else if($operate=='status_no'){
                            $data['Dis_status'] = '0';
                            $res = m($database)->where("id = '".$id."'")->save($data);
                            if($res){
                                records($RecordInfo['Head'],$username.'在'.date('Y-m-d H:i:s',$time).'拒绝了由 , '.$info['d_username'].' ,分配的关键词： '.$info['name']);
                                alert('不接受操作成功！',1);
                            }else{
                                alert('不接受操作失败，刷新页面重试！ ');
                            }
                        }
                    }else{
                        if(m($database)->where("id = '".$id."'")->delete()){
                            alert('该关键词不存在，获取记录即将删除。。。',1);
                        }else{
                            alert('位置错误，请刷新重试！');
                        }
                    }
                }else{
                    alert("接受失败，没有找到该条数据，请刷新页面重试");
                }
            }else{
                alert('出错，你这是什么操作？');
            }

        }else{
            alert("出错，提交的ID为空");
        }
    }

}