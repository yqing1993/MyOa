<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/6
 * Time: 15:30
 */
class SendLoveAction extends loginAction{
    function __construct(){
        parent::__construct();
    }
    public function index(){
        $username = $_SESSION['username'];
        $time = time();
        $begin=mktime(0,0,0,date('m'),1,date('Y'));
        $end=mktime(23,59,59,date('m'),date('t'),date('Y'));

        $s = m('love')->where("send_user = '$username' and $begin < send_time <$end and sp_res = 1")->count();
        $a = m('love')->where("acc_user = '$username' and $begin < send_time <$end")->count();
        //$s=7;
        if($s < 3){
            $s2 = 3-$s;
            $s1 = '还需赠送<span style="color: #f00;font-size: 20px">&nbsp;'.$s2.'&nbsp;</span>颗爱心。';
        }else if($s==3){
            $s1 = '当前完成本月送心基本要求。';
        }else if(3<$s && $s< 10){
            $s1 = '当前完成本月送心基本要求，不要超过<span style="color: #f00;font-size: 20px">10</span>个哦。';
        }else if($s == 10){
            $s1 = '本月爱心送完了。';
        }else{
            $s1 = '<span style="color: #f00;font-size: 20px">本月爱心累计超过10个，要扣分的！</span>';
        }
        $this->assign('a',$a);
        $this->assign('s',$s);
        $this->assign('s1',$s1);

        //计算分数 未送满3颗多于10颗都是扣0.25分/颗 得爱心0.25分/颗
        if($s<=3 ){
            $score = $a*0.25 - (3-$s)*0.25;
        }else if($s>10){
            $score = $a*0.25 - ($s-10)*0.25;
        }else{
            $score = $a*0.25;
        }
        $this->assign('score',$score);
        $role = $_SESSION['role'];
        $DepartmentID = $_SESSION['DepartmentID'];
        //剔除超级管理员 role = 1
        $username = $_SESSION['username'];
        $duty = $_SESSION['Duty'];
        //dump($_SESSION);
        $ActionName = $this->getActionName();
        $this->assign('ActionName',$ActionName);
        $para = "username != '$username' and role NOT REGEXP '\"[1]\"'";
        //同一个部门的下级不能送给上级
        if($duty == 'yg'){
            //除去员工所在部门的组长或者主管
            $res = M('user')->where("DepartmentID = $DepartmentID and Duty ='zz'")->field('username')->find();
            if($res){
                //有组长 继续剔除主管
                //获取当前部门的上级部门
                $res = $res['username'];
                $para .= " and username != '$res'";
                $p = m('department')->where("id = $DepartmentID")->field('parentID')->find();
                $p = $p['parentID'];
                //dump($p);
                $res1 = M('user')->where("DepartmentID = $p and Duty ='zg'")->field('username')->find();
                //dump(M('user')->getLastSql());
                //dump($res1);
                $res1 = $res1['username'];
                $para .= " and username != '$res1'";
            }else{
                //没有组长
                $res2 = M('role')->where("DepartmentID = $DepartmentID and Duty ='zg'")->field('username')->find();
                $res2 = $res2['username'];
                $para .= " and username != '$res2'";
            }
        }elseif($duty == 'zz'){
            $res3 = M('user')->where("DepartmentID = $DepartmentID and Duty ='zg'")->field('username')->find();
            $res3 = $res3['username'];
            $para .= " and username != '$res3'";
        }
        $users = m('user')->where($para)->field('username')->select();
        //dump(M('user')->getLastSql());die;
        //dump($users);die;
        //dump(date('Y-m-d',time()));
        //$start = strtotime(date('Y-m-d',time()).'00:00:00');
        //$end = strtotime(date('Y-m-d',time()).'23:59:59');
        //dump($start);
        //dump(date('Y-m-d H:i:s',$start));
        //dump($end);
        //dump(date('Y-m-d H:i:s',$end));die;
        //获取所有的部门
        $dapts = m('department')->where('ParentID = 1')->select();
        $this->assign('dapts',$dapts);
        $this->assign('users',$users);
        $this->display();
    }

    public function SendLovePage(){
        $username = $_SESSION['username'];

        $data = DataPage(array('database'=>'love','para'=>" and send_user = '$username'",'order'=>'send_time desc'));
        echo json_encode($data);
    }

    public function SendLoveAjax(){
        $dapt = $_POST['dapt'];
        $s_dapt = $_POST['s_dapt'];
        if($dapt){
            //获取下面的子部门
            $s_dapts = m('department')->where("ParentID = $dapt")->select();
            //echo json_encode(m('department')->getLastSql());die;
            if(!empty($s_dapts)){
                $data['status'] = 200;
                $data['data'] = $s_dapts;
            }else{
                $data['status'] = 220;
            }
            echo json_encode($data);
        }
        if($s_dapt){
            //获取下面的员工
            $user = m('user')->where("DepartmentID = $s_dapt and role NOT REGEXP '\"[1]\"'")->select();
            //echo json_encode(m('user')->getLastSql());die;
            if(!empty($user)){
                $data['status'] = 200;
                $data['data'] = $user;
            }else{
                $data['status'] = 260;
            }
            echo json_encode($data);
        }

    }



    public function SendLoveData(){
        $send_user = $_SESSION['username'];
        $DepartmentID = $_SESSION['DepartmentID'];
        $duty = $_SESSION['Duty'];
        $send_time = time();
        $sp_status = '0';
        $sp_res = '0';
        $fail_reason = '';
        $id = I('post.id');
        if($id != ''){
            $operate = I('post.operate','');
            $database = 'love';
            $RecordInfo = array(
                'Head'=>'SendLove',
                'Name'=>'送心',
            );

            if($operate == 'UpdateAdd'){
                if($id == 'new'){
                    $acc_user = I('post.acc_user');
                    //不可送给上级

                    if($duty == 'yg'){
                        //除去员工所在部门的组长或者主管
                        $res = M('user')->where("DepartmentID = $DepartmentID and Duty ='zz'")->field('username')->find();
                        //echo json_encode($_SESSION);die;
                        if($res){
                            //有组长 继续剔除主管
                            //获取当前部门的上级部门
                            $res = $res['username'];
                            $p = m('department')->where("id = $DepartmentID")->field('parentID')->find();
                            $p = $p['parentID'];
                            //dump($p);
                            $res1 = M('user')->where("DepartmentID = $p and Duty ='zg'")->field('username')->find();
                            //dump(M('user')->getLastSql());
                            //dump($res1);
                            $res1 = $res1['username'];
                        }else{
                            //没有组长
                            $res2 = M('role')->where("DepartmentID = $DepartmentID and Duty ='zg'")->field('username')->find();
                            $res2 = $res2['username'];
                        }
                    }elseif($duty == 'zz'){
                        //echo json_encode($_SESSION);die;
                        $p = m('department')->where("id = $DepartmentID")->field('parentID')->find();
                        $p = $p['parentID'];
                        $res3 = M('user')->where("DepartmentID = $p and Duty ='zg'")->field('username')->find();
                        $res3 = $res3['username'];

                    }
//                    $data[] = $res;
//                    $data[] = $res1;
//                    $data[] = $res2;
//                    $data[] = $res3;
                    //echo json_encode($data);die;
                    if($acc_user == $res || $acc_user == $res1 || $acc_user == $res2 || $acc_user == $res3){
                        alert('换个人吧，你不能送给他（她）！');
                    }else{
                        $reason = htmlspecialchars(I('post.reason'));
                        //一天同一个人只能送一次
                        $start = strtotime(date('Y-m-d',time()).'00:00:00');
                        $end = strtotime(date('Y-m-d',time()).'23:59:59');
                        $res = m($database)->where("send_user = '$send_user' and acc_user = '$acc_user' and $start < send_time and send_time < $end")->find();
                        //echo json_encode(m($database)->getLastSql());die;
                        if(!$res){
                            $add['send_user'] = $send_user;
                            $add['acc_user'] = $acc_user;
                            $add['reason'] = $reason;
                            $add['send_time'] = $send_time;
                            if(role('SpLove',1)){
                                $add['sp_status'] = '1';
                                $add['sp_res'] = 1;
                                $add['fail_reason'] = '无需审批';
                            }else{
                                $add['sp_status'] = $sp_status;
                                $add['sp_res'] = $sp_res;
                                $add['fail_reason'] = $fail_reason;
                            }

                            $new_id = m($database)->add($add);
                            if($new_id){
                                //echo json_encode(m($database)->getLastSql());die;
                                records($RecordInfo['Head'].'Add',$send_user.'送了一颗爱心给'.$acc_user);
                                alert('送心成功！','1');
                            }else{
                                alert("送心失败，请刷新页面重试");
                            }
                        }else{
                            alert('今天已经给这个人送过了，明天再送吧！');
                        }
                    }

                }
            }elseif($operate == 'del'){
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)) {
                    //说明该记录存在
                    if (m($database)->where("id = '" . $id . "'")->delete()) {

                        records($RecordInfo['Head'] . "Del", $send_user . " 删除了未审批的爱心记录 ，ID = " . $id);
                        alert("删除成功", 1);

                    } else {
                        alert("删除失败，请刷新页面重试");
                    }
                }else{
                    alert("没有找到该记录，请刷新页面重试");
                }
            }else{
                alert('错误，不知道你要干啥！');
            }
        }else{
            alert('送心出现错误，请重新刷新！');
        }
    }
}