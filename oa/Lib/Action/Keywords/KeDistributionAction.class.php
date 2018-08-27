<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/5
 * Time: 15:49
 */

//关键词分配
class KeDistributionAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('KeDistribution',3);
    }
    public function index(){
        $roles = role('LogKeyword',6);
        //剔除管理员
        $ActionName = $this->getActionName();
        $c_id = m('k_category')->where("CategoryName = '品牌词'")->field('id')->find();
        $c_id = $c_id['id'];
        $AllCheck = m('k_project')->field('id,ProjectName')->where("CategoryID = $c_id")->select();
//        $arr = array_column($AllCheck,'id');
//        $arr1 = implode('","',$arr);
//        dump($arr1);
//        dump($arr);
//        DUMP($AllCheck);die;

        $c_id = m('k_category')->where("CategoryName = '品牌词'")->field('id')->find();
        $c_id = $c_id['id'];
        $para = " and CategoryID = $c_id";
        $ProjectID = I('post.ProjectID');
        $name = I('post.name');

        $time = time();
        $now_day = date('w',$time);
        //获取一周的第一天，注意第一天应该是星期天
        $begin_time = $time - ($now_day-1)*60*60*24;
        $begin_time = date('Y-m-d',$begin_time);
        $begin_time = $begin_time.' 00:00:00';
        $begin_time = strtotime($begin_time);
        //获取一周的最后一天，注意最后一天是星期六
        $end_time = $time + (7-$now_day)*60*60*24;
        $end_time = date('Y-m-d',$end_time);
        $end_time = $end_time.' 23:59:59';
        $end_time = strtotime($end_time);

        //判断
        if($ProjectID != ''){
            $para = "and ProjectID = $ProjectID";
        }

        if($name != ''){
            $para = " and name = '$name'";
        }
        $data = DataPage(array('database'=>'keywords','order'=>'concat(search_number,id,rank,addtime) desc','para'=>$para));
        $data = get_info($data);

        $users = m('k_distribution')->where("Dis_time < $end_time and Dis_time > $begin_time")->field('name')->select();
        $new_arr = array();

        $new_arr = array_column($users,'name');
        $new_arr = array_unique($new_arr);
        $i=0;


            for($i=0;$i<count($new_arr);$i++){
                $user = m('k_distribution')->where("name = '$new_arr[$i]' and Dis_time < $end_time and Dis_time > $begin_time")->field('username')->select();
                $user = array_column($user,'username');
                $user = implode(',',$user);
                foreach ($data['list'] as $k=>$v){
                if($v['name'] == $new_arr[$i]){
                    $data['list'][$k]['person'] = $user;
                }else{
                    $data['list'][$k]['person'] = null;
                }

            }
       }
        //dump($users);

        ////dump($new_arr);dump($user);
        //dump($data);



        //dump($users);die;




        $this->assign('check',$AllCheck);
        $this->assign('ActionName',$ActionName);
        $this->assign('roles',$roles);
        $this->display();
    }
    public function KeDistributionPage(){
        $c_id = m('k_category')->where("CategoryName = '品牌词'")->field('id')->find();
        $c_id = $c_id['id'];
        $para = " and CategoryID = $c_id";
        $ProjectID = I('post.ProjectID');
        $name = I('post.name');

        $time = time();
        $now_day = date('w',$time);
        //获取一周的第一天，注意第一天应该是星期天
        $begin_time = $time - ($now_day-1)*60*60*24;
        $begin_time = date('Y-m-d',$begin_time);
        $begin_time = $begin_time.' 00:00:00';
        $begin_time = strtotime($begin_time);
        //获取一周的最后一天，注意最后一天是星期六
        $end_time = $time + (7-$now_day)*60*60*24;
        $end_time = date('Y-m-d',$end_time);
        $end_time = $end_time.' 23:59:59';
        $end_time = strtotime($end_time);

        //判断
        if($ProjectID != ''){
            $para = "and ProjectID = $ProjectID";
        }

        if($name != ''){
            $para = " and name = '$name'";
        }
        $data = DataPage(array('database'=>'keywords','order'=>'concat(search_number,id,rank,addtime) desc','para'=>$para));
        $data = get_info($data);

        echo json_encode($data);
    }

    public function KeDistributionData(){
        $id = I('post.id');
        $username = $_SESSION['username'];
        $userid = $_SESSION['userid'];
        $time = time();

        if($id != ''){
            $operate = I('post.operate','');
            if($operate != ''){
                $database = 'k_distribution';
                $RecordInfo = array(
                    'Head'=>'KeDistribution',
                    'Name'=>'分配',
                    'NameKey'=>'name'
                );
                //判断当前的数据还在不在
                $info = m('keywords')->where("id = '".$id."'")->find();
                if(!empty($info)){
                    if($operate=='UpdateAdd'){
                        $name = I('post.name');
                        $username = I('post.username');
                        $ProjectID = I('post.ProjectID');
                        $now_day = date('w',$time);
                        //获取一周的第一天，注意第一天应该是星期天
                        $begin_time = $time - ($now_day-1)*60*60*24;
                        $begin_time = date('Y-m-d',$begin_time);
                        $begin_time = $begin_time.' 00:00:00';
                        $begin_time = strtotime($begin_time);
                        //获取一周的最后一天，注意最后一天是星期六
                        $end_time = $time + (7-$now_day)*60*60*24;
                        $end_time = date('Y-m-d',$end_time);
                        $end_time = $end_time.' 23:59:59';
                        $end_time = strtotime($end_time);
                        $check = m('k_distribution')->where("name = '$name' and username = '$username' and (Dis_time between $begin_time and $end_time)")->count();
                        //$sql = m('k_distribution')->getLastSql();
                        //echo json_encode($sql);die;
                        if($check == '0'){
                            //alert('111');die;
                            $data['d_username'] = $_SESSION['username'];
                            $data['name'] = $name;
                            $data['Dis_time'] = $time;
                            $data['username'] = $username;
                            $data['ProjectID'] = $ProjectID;
                            //将keywords表中分配次数+1
                            $cishu = m('keywords')->where("name = '$name'")->field('Dis_cishu')->find();
                            $cishu = (int)$cishu['Dis_cishu'];
                            //$sql = m('keywords')->getLastSql();
                            //echo json_encode($cishu);die;
                            $cishu = $cishu + 1;
                            $ex['Dis_cishu'] = $cishu;
                            $insert = m('keywords')->where("name = '$name'")->save($ex);
                            //$sql = m('keywords')->getLastSql();
                            //echo json_encode($sql);die;
                            if($insert){
                                //alert('66666');
                                $a = m('k_distribution')->add($data);
                                //$sql = m('k_distribution')->getLastSql();
                                //echo json_encode($sql);die;
                                if($a){
                                    records($RecordInfo['head'],$_SESSION['username'].'分配给'.$data['username'].'品牌关键词：'.$data['name']);
                                    alert('分配成功！',1);
                                }else{
                                    alert('分配失败！请重试！');
                                }
                            }else{
                                alert('发生未知错误，请刷新重试！');
                            }
                        }else{
                            alert('该成员本周已经分配过当前关键词，请重新选取！');
                        }
                    }elseif ($operate == 'Get'){
                        $res = m('keywords')->where("id = '".$id."'")->find();
                        if($res){
                            $res['error'] = 1;
                            $info = m('K_project')->where('id = '.$res['ProjectID'])->field('ProjectName')->find();
                            $res['p_name'] = $info['ProjectName'];
                            //$res['list'] = $res;
                            $res = get_info($res,2);

                        }else{
                            alert('出错，请刷新新重试！');
                        }
                        echo json_encode($res);die;
                    }
                }else{
                    alert("分配失败，没有找到该条数据，请刷新页面重试");
                }

            }else{
                alert('出错，你这是什么操作？');
            }

        }else{
            alert("出错，提交的ID为空");
        }
    }

//    public function KeDistributionLook(){
//        $checked = I('post.checked');
//        if($checked){
//            return $this->KeDistributionPage($checked);
//        }else{
//            alert('出现错误，请刷新后重试！');
//        }
//    }
}