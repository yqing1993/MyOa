<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/5
 * Time: 15:56
 */

//瘦脸词录入
class InsertBotoxAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('InsertBotox',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());
        $res = m('k_project')->where("ProjectName = '瘦脸'")->field('id')->select();
        $id = $res[0]['id'];
        $where['ProjectID'] = $id;
        $where['status'] = '0';
        $Types = m('k_type')->where($where)->order('concat(rank, addtime) desc')->field('id,TypeName')->select();
        //$Types = $_SESSION;
//        $userid = $_SESSION['userid'];
//        $data = DataPage(array('database'=>'keywords','order'=>'rank desc','para'=>"userid = $userid"));
//        $data = get_info($data);
//        $data = $data['list'];
//        $this->assign('data', $data);
        $this->assign('Types', $Types);
        $this->display();
    }

    public function InsertBotoxPage(){
        $res = m('k_project')->where("ProjectName = '瘦脸'")->field('id')->select();
        $id = $res[0]['id'];
        $userid = $_SESSION['userid'];
        $data = DataPage(array('database'=>'keywords','order'=>'concat(rank, addtime) desc','para'=>" and userid = '$userid' and ProjectID = '$id'"));
        $data = get_info($data);
        echo json_encode($data);
    }

    public function InsertBotoxData(){
        $id = I('post.id');

        if($id != ''){
            $operate = I('post.operate','');
            $database = 'keywords';
            $RecordInfo = array(
                'Head'=>'Botox',
                'Name'=>'瘦脸关键词',
                'NameKey'=>'name'
            );

            if($operate == 'UpdateAdd'){
                $key = array(
                    '关键词名称'=>'!name',
                    '所属类型'=>'!TypeID',
                    '排序'=>'rank'
                );

                $add = KeyData($key,'post');
                //echo json_encode($add);die;

                $other = OtherInfo(array('userid','username','addtime'));
                //echo json_encode($other);die;

                $add = array_merge($add,$other);
                //echo json_encode($add);die;

                if($id == 'new'){
                    //echo json_encode($add);die;
                    $repeat = m($database)->where("name = '".$add['name']."'")->count();
                    //$sql = m($database)->getLastSql();
                    if($repeat == '0'){
                        $database = 'keywords';
                        $ProjectID = m('k_type')->where('id ='.$add['TypeID'])->field('ProjectID')->find();
                        //echo json_encode($ProjectID);die;
                        $ProjectID = $ProjectID['ProjectID'];
                        $add['ProjectID'] = $ProjectID;
                        $CategoryID = m('k_project')->where('id ='.$ProjectID)->field('CategoryID')->find();
                        $CategoryID = $CategoryID['CategoryID'];
                        $add['CategoryID'] = $CategoryID;
                        //echo json_encode($add);die;
                        //判断当前操作的人员角色 超级管理员直接入库 品牌专员则需要提交给超级管理员审核
                        if(role('KeSp',1)){
//                            if($_SESSION['role'] == '1'){
                                //超级管理员直接入库
                                //审批状态/审批结果
                                $add['sp_status'] = 1;
                                $add['sp_res'] = 1;
                                //echo json_encode($add);die;
                                $new_id = m($database)->add($add);
                                //$sql = m($database)->getLastSql();
                                //echo json_encode($sql);die;
                                if($new_id){

                                    records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
                                    alert("添加成功",1);

                                }else{
                                    alert("添加失败，请刷新页面重试");
                                }
//                            }else{
//                                //品牌专员
//                                    $add['sp_role'] = 1;
//                                    $new_id = m($database)->add($add);
//                                    if($new_id){
//
//                                        records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
//                                        alert("添加成功",1);
//
//                                    }else{
//                                        alert("添加失败，请刷新页面重试");
//                                    }
//                            }
                        }else{
                            //普通专员
                                $add['sp_role'] = 2;
                                $new_id = m($database)->add($add);
                                if($new_id){

                                    records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
                                    alert("添加成功",1);

                                }else{
                                    alert("添加失败，请刷新页面重试");
                                }
                        }
                    }else{
                        //echo json_encode($sql);die;
                        alert("添加失败，该".$RecordInfo['Name']."已存在，".$RecordInfo['Name']."不得重复");
                    }
                }else{
                    $repeat = m($database)->where("id = '".$id."'")->count();
                    if($repeat > 0){
                        /*检测有没有重复*/
                        //判断根据当前的
                        $repeat = m($database)->where("id != '".$id."' and name = '".$add['name']."'")->count();
                        if($repeat == '0'){

                            m($database)->where("id = '".$id."'")->save($add);

                            records($RecordInfo['Head']."Update","更新了".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$id);

                            alert("更新成功",1);

                        }else{
                            alert("修改失败，该".$RecordInfo['Name']."已存在，".$RecordInfo['Name']."不得重复");
                        }
                    }else{
                        alert("修改失败，没有找到该记录，请刷新页面重试");
                    }
                }
            }elseif($operate=='status'){
                $status = I("post.status");
                if($status!=''){

                    /*检测该条数据还在不在*/
                    $info = m($database)->where("id = '".$id."'")->find();

                    if(!empty($info)){

                        $data = OtherInfo(array('userid','username','addtime'));
                        $data['status'] = $status;

                        if(m($database)->where("id = '".$id."'")->save($data)){

                            $status_str = $status=='0'?'启用':'停用';

                            records($RecordInfo['Head']."Status",$status_str." 了 ".$info[$RecordInfo['NameKey']]."  ".$RecordInfo['Name']."，ID = ".$info['id']);

                            alert("状态修改成功",1,$status);
                        }else{
                            alert("状态修改失败，请刷新页面重试");
                        }
                    }else{
                        alert("状态修改失败，没有找到该条数据，请刷新页面重试");
                    }
                }else{
                    alert("状态修改失败，修改的状态不能为空");
                }
            }elseif($operate=='Get'){
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){

                    $info['error'] = '1';

                    echo json_encode($info);
                }else{
                    alert("该条信息没有找到，请刷新页面重试");
                }
            }elseif($operate=='del'){
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){
                    //说明该记录存在
                    $res = getChildren($id,1);
                    if(m($database)->where("id = '".$id."'")->delete()){

                        records($RecordInfo['Head']."Del"," 删除了 ".$RecordInfo['Name']." ".$info[$RecordInfo['NameKey']]."，ID = ".$id);
                        alert("删除成功",1);

                    }else{
                        alert("删除失败，请刷新页面重试");
                    }

                }else{
                    alert("没有找到该记录，请刷新页面重试");
                }
            }else{
                alert("不知道你要干啥");
            }

        }else{
            alert("出错，提交的ID为空");
        }
    }


}