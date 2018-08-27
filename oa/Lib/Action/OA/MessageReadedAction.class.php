<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/14
 * Time: 13:46
 */

class MessageReadedAction extends loginAction{
    function __construct(){
        parent::__construct();
    }
    public function index(){
        $this->assign('ActionName',$this->getActionName());
        $this->display();
    }
    public function MessageReadedPage(){
        //根据当前的id去查找
        $id = $_SESSION['id'];
        $data = DataPage(array('database'=>'message','order'=>'addtime desc','para'=>" and FIND_IN_SET($id,readed)"));
        echo json_encode($data);
    }

    public function MessageReadedData()
    {
        $id = I('post.id');

        if ($id != '') {
            $operate = I('post.operate', '');
            $database = 'message';
            if ($operate == 'UpdateAdd') {
                $repeat = m($database)->where("id = '" . $id . "'")->count();
                if ($repeat > 0) {
                    //已读
                    $w_info = m($database)->where("id = $id")->field('noread')->find();
                    $w_users = $w_info['noread'];
                    $w_users = json_decode($w_users);

                    for($i=0;$i<count($w_users);$i++){
                        if($w_users[$i] == $_SESSION['id']){
                            unset($w_users[$i]);
                        }

                    }
                    $w_users = array_values($w_users);
                    $w_users = json_encode($w_users);

                    $y_info = m($database)->where("id = $id")->field('readed')->find();
                    //echo json_encode($y_info);die;
                    if($y_info['readed'] != ''){
                        $y_users = $y_info['readed'];
                        $y_users = json_decode($y_users);
                        array_push($y_users,$_SESSION['id']);
                        //echo json_encode($y_users);die;
                    }else{
                        $y_users[] = $_SESSION['id'];
                        //echo json_encode($y_users);die;
                    }
                    $y_users = json_encode($y_users);

                    //插入 开启事务
                    $res1 = m($database)->where("id = $id")->setField('noread',$w_users);
                    $res2 = m($database)->where("id = $id")->setField('readed',$y_users);
                    M()->startTrans();
                    if($res1 && $res2){
                        M()->commit();
                        alert('查看成功！已自动加入已读',1);
                    }else{
                        M()->rollback();
                        alert("发生未知错误，请刷新页面重试");
                    }
                    //新数组 更新
                    echo json_encode($w_users);die;
                }else {
                    alert("查看失败，没有找到该记录，请刷新页面重试");
                }
            }elseif($operate == 'Get'){
                $info = m($database)->where("id = '" . $id . "'")->find();

                if (!empty($info)) {
                    $info['error'] = '1';
                    $info['content'] = htmlspecialchars_decode($info['content']);
                    echo json_encode($info);//$this->display();
                } else {
                    alert("该条信息没有找到，请刷新页面重试");
                }
            }else{
                alert("不知道你要干啥");
            }
        } else {
            alert("出错，提交的ID为空");
        }
    }

}