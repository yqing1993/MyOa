<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/6
 * Time: 15:36
 */

class LookAccAction extends loginAction{
    function __construct(){
        parent::__construct();
    }
    public function index(){
        $username = $_SESSION['username'];
        $time = time();
        $begin=mktime(0,0,0,date('m'),1,date('Y'));
        $end=mktime(23,59,59,date('m'),date('t'),date('Y'));

        $a = m('love')->where("acc_user = '$username' and $begin < send_time <$end")->count();
        $this->assign('a',$a);
        $ActionName = $this->getActionName();
        $this->assign('ActionName',$ActionName);
        $this->display();
    }

    public function LookAccPage(){
        $username = $_SESSION['username'];

        $StartTime = I('post.StartTime','');
        if($StartTime == ''){
            $this->week();
        }

        $day = Day();

        $FirstDays = strtotime($day[0]);
        $LastDays = strtotime(end($day).' 23:59:59');
        $data = DataPage(array('database'=>'love','order'=>'send_time desc','para'=>" and sp_res = 1 and acc_user = '$username' and $FirstDays < send_time <$LastDays"));
        $data['date'] = array($day[0],end($day));
        echo json_encode($data);
    }
    public function LookAccData(){

    }

    /*计算week*/
    function week(){
        $time = time();
        $TopWeek = strtotime("+0 day", $time);
        $TopWeek = strtotime(date('Y-m-d', ($TopWeek - ((date('w',$TopWeek) == 0 ? 7 : date('w',$TopWeek)) - 1) * 24 * 3600)));//上周一

        $NextWeek = strtotime("+7 day", $time);
        $NextWeek = strtotime(date('Y-m-d', ($NextWeek + (7 - (date('w', $NextWeek) == 0 ? 7 : date('w', $NextWeek))) * 24 * 3600)));//下下周日

        $_POST['StartTime'] = $TopWeek;
        $_POST['EndTime'] = $NextWeek;
    }
}