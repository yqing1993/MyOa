<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/6
 * Time: 15:37
 */

class RankLoveAction extends loginAction{
    function __contruct(){
        parent::__construct();
        role('SpLove',3);
    }
    function index(){

//        $users = m('user')->where("role NOT REGEXP '\"[1]\"'")->field('username')->select();
//        $users = array_column($users,'username');
//        //本周的时间区间
//        $time = time();
//        $w = date('w',$time);
//        $start = time()-($w-1)*24*60*60;
//        $start = date('Y-m-d',$start).' 00:00:00';
//        $start = ($_POST['StartTime']?$_POST['StartTime']:strtotime($start));
//        $end = time()+(7-$w)*24*60*60;
//        $end = date('Y-m-d',$end).' 23:59:59';
//        $end = ($_POST['EndTime']?$_POST['EndTime']:strtotime($end));
//        $new = array();
//        for($i = 0;$i < count($users);$i++){
//            $s = m('love')->where("$start < send_time < $end and send_user = '$users[$i]'")->count();
//            $a = m('love')->where("$start < send_time < $end and acc_user = '$users[$i]'")->count();
//            $new[$i]['user'] = $users[$i];
//            $new[$i]['a_cishu'] = $a;
//            $new[$i]['s_cishu'] = $s;
//            $new[$i]['a_score'] = $a*0.25;
//            if($s <= 3 ){
//                $new[$i]['s_score'] = ($s-3)*0.25;
//            }else if($s>4 && $s <=10){
//                $new[$i]['s_score'] = 0;
//            }else if($s>10){
//                $new[$i]['s_score'] = (10-$s)*0.25;
//            }
//            $new[$i]['score'] = $new[$i]['a_score'] +  $new[$i]['s_score'];
//        }
//
//        //使用冒泡排序
//        for($j=0;$j<count($new)-1;$j++){
//            for($k=0;$k<count($new)-1-$j;$k++){
//                if($new[$k]['a_cishu'] <$new[$k+1]['a_cishu']){
//                    $middle = $new[$k];
//                    $new[$k] = $new[$k+1];
//                    $new[$k+1] = $middle;
//                }else if($new[$k]['a_cishu'] == $new[$k+1]['a_cishu']){
//                    if($new[$k]['s_cishu'] <$new[$k+1]['s_cishu']){
//                        $middle = $new[$k];
//                        $new[$k] = $new[$k+1];
//                        $new[$k+1] = $middle;
//                    }
//                }
//            }
//        }

        //dump($new);die;
        $ActionName = $this->getActionName();
        $this->assign('ActionName',$ActionName);
        $this->display();
    }

    function RankLovePage(){
        //获取所有人 除去超级管理员
        //echo json_encode($_POST);die;
        $users = m('user')->where("role NOT REGEXP '\"[1]\"'")->field('username')->select();
        $users = array_column($users,'username');
        //本周的时间区间
        $time = time();
        $w = date('w',$time);
        $start = time()-($w-1)*24*60*60;
        $start = date('Y-m-d',$start).' 00:00:00';
        $start = ($_POST['StartTime']?$_POST['StartTime']:strtotime($start));
        $end = time()+(7-$w)*24*60*60;
        $end = date('Y-m-d',$end).' 23:59:59';
        $end = ($_POST['EndTime']?$_POST['EndTime']:strtotime($end));
        $new = array();
        for($i = 0;$i < count($users);$i++){
            $s = m('love')->where("$start <send_time and send_time < $end and send_user = '$users[$i]' and sp_res=1")->count();
            $a = m('love')->where("$start < send_time and send_time < $end and acc_user = '$users[$i]' and sp_res=1")->count();
            $new[$i]['user'] = $users[$i];
            $new[$i]['a_cishu'] = $a;
            $new[$i]['s_cishu'] = $s;
            $new[$i]['a_score'] = $a*0.25;
            if($s <= 3 ){
                $new[$i]['s_score'] = ($s-3)*0.25;
            }else if(4 <= $s && $s <= 10){
                $new[$i]['s_score'] = 0;
            }else if($s>10){
                $new[$i]['s_score'] = (10-$s)*0.25;
            }
            $new[$i]['score'] = $new[$i]['a_score'] +  $new[$i]['s_score'];
        }
        //echo json_encode(m('love')->getLastSql());die;

        //使用冒泡排序
        for($j=0;$j<count($new)-1;$j++){
            for($k=0;$k<count($new)-1-$j;$k++){
                if($new[$k]['a_cishu'] <$new[$k+1]['a_cishu']){
                    $middle = $new[$k];
                    $new[$k] = $new[$k+1];
                    $new[$k+1] = $middle;
                }else if($new[$k]['a_cishu'] == $new[$k+1]['a_cishu']){
                    if($new[$k]['s_cishu'] <$new[$k+1]['s_cishu']){
                        $middle = $new[$k];
                        $new[$k] = $new[$k+1];
                        $new[$k+1] = $middle;
                    }
                }
            }
        }


        import("ORG.Util.Page");
        $count = count($new);
        $Page = new \Page($count,50);
        $show = $Page->show();
        $data = array('list'=>$new,'page'=>$show);
        $day = Day();
        $data['date'] = array(date('Y-m-d',$start),date('Y-m-d',$end));
        echo json_encode($data);

    }

}