<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/18
 * Time: 13:28
 */

class LookJianfAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('InsertJianf',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());
        $categories = m('k_category')->where("status='0'")->order('rank desc')->field('id,CategoryName')->select();
        $projects = m('k_project')->where("status='0' and CategoryID = '2'")->order('rank desc')->field('id,ProjectName,CategoryID')->select();
        $this->assign('projects', $projects);
        $types = m('k_type')->where("status='0'")->order('rank desc')->field('id,TypeName,ProjectID')->select();
        $this->assign('types', $types);

        //组装思维数组
//        for($j=0;$j<count($projects);$j++){
//            foreach ($types as $k => $v){
//                if($v['ProjectID'] == $projects[$j]['id']){
//                    $projects[$j]['type'][] = $v;
//                }
//            }
//        }
//
//        for($i=0;$i<count($categories);$i++){
//            foreach ($projects as $key => $value){
//                if($value['CategoryID'] == $categories[$i]['id']){
//                    $categories[$i]['project'][] = $value;
//                }
//            }
//        }
        //dump($categories);die;
        $this->assign('categories', $categories);
        $botox = m('k_project')->where("ProjectName = '瘦脸' ")->field('id')->find();
        $botox = $botox['id'];
        //获取瘦脸下面的子类
        $bc = m('k_type')->where('ProjectID ='.$botox)->field('id,TypeName')->select();
        //dump($bc);die;

        $jianf = m('k_project')->where("ProjectName = '减肥' ")->field('id')->find();
        $jianf = $jianf['id'];
        $jc = m('k_type')->where('ProjectID ='.$jianf)->field('id,TypeName')->select();
        //dump($bc);die;
        $yangs = m('k_project')->where("ProjectName = '养生' ")->field('id')->find();
        $yangs = $yangs['id'];

        $yc = m('k_type')->where('ProjectID ='.$yangs)->field('id,TypeName')->select();
        //dump($bc);die;
        $this->assign('bc',$bc);
        $this->assign('jc',$jc);
        $this->assign('yc',$yc);
        //dump($botox);die;
//        if($_SESSION['Duty'] != 'yg'){
//            $username = $_SESSION['username'];
//            $DarId = m('user')->where("username = '$username'")->field('DepartmentID')->find();
//            $DarId = $DarId['DepartmentID'];
//            $users = Department_arr('1','user');
//            //dump($users);
//            //判断所有属于关键词木块的人
//            $k_users = role('KeBs',2);
//            $k_users = array_column($k_users,'userid');
//            //属于当前部门的人 和 属于关键词模块的人交集
//            $users = array_intersect($users,$k_users);
//            //dump($k_users);
//            //dump($users);
//            $users = implode('","',$users);
//            $usernames = m('user')->where('userid in ("'.$users.'")')->field('username')->select();
////        $usernames = array_column($usernames,'username');
////        $sql = m('user')->getLastSql();
////        dump($sql);
//            // dump($usernames);
//            // dump($users);die;
////        $sql = m('user')->getLastSql();
////        dump($sql);
//            //dump($DarId);die;
//            $this->assign('users',$usernames);
//        }
        $search = m('k_search')->where("status='0'")->order('rank desc')->field('id,SearchRange,SearchCategory')->select();
        //dump($search);die;
        $this->assign('search', $search);
        $this->display();

    }

    public function LookJianfPage(){
        $CategoryID = I('post.CategoryID');
        $ProjectID = I('post.ProjectID');
        $TypeID = I('post.TypeID');
        $rank_search = I('post.rank_search');
        $name = I('post.name');
        $userid = $_SESSION['userid'];
        $username = $_SESSION['username'];
        $yonghu = I('post.username');
        $Duty = $_SESSION['Duty'];

        $botox = m('k_project')->where("ProjectName = '瘦脸' ")->field('id')->find();
        $botox = $botox['id'];
        $jianf = m('k_project')->where("ProjectName = '减肥' ")->field('id')->find();
        $jianf = $jianf['id'];
        $yangs = m('k_project')->where("ProjectName = '养生' ")->field('id')->find();
        $yangs = $yangs['id'];
        $para = " and sp_res = '1' and ProjectID = '$jianf'";

        //判断当前用户是什么角色

        //能录入什么就显示什么词

        //判断
//
//        if(role('KeSp',1)){
//            //具有审批功能的人-》行业词
//            if($_SESSION['role'] == '1'){
//                //超级管理员默认显示所有
//            }else{
//                //行业词审批者能看到所有的行业词
//                if(role('InsertBrand',1)){
//                    //具有审批的品牌词专员还是显示品牌词
//                    $para .= " and CategoryID = 2";
//                }else{
//                    $para .= " and CategoryID = 1";
//                }
//            }
//        }else{
//            if(role('InsertBrand',1)){
//                //品牌词录入者 看到所有的品牌词
//                $para .= " and CategoryID = 2";
//            }else{
//                //普通人
//                if(role('InsertBotox',1)){
//                    //有瘦脸
//
//                    if(role('InsertJianf',1)){
//                        //有减肥
//
//                        if(role('InsertYangs',1)){
//                            //有养生
//                            $para .= " and (ProjectID = $botox or ProjectID = $jianf or ProjectID = $yangs)";
//                        }else{
//                            //没有养生
//                            $para .= " and (ProjectID = $botox or ProjectID = $jianf)";
//                        }
//                    }else{
//                        //没有减肥
//                        if(role('InsertYangs',1)){
//                            //有养生
//                            $para .= " and (ProjectID = $botox or ProjectID = $yangs)";
//                        }else{
//                            //没有养生
//                            $para .= " and ProjectID = $botox";
//                        }
//                    }
//                }else{
//                    //没有瘦脸
//                    if(role('InsertJianf',1)) {
//                        //有减肥
//                        if(role('InsertYangs',1)){
//                            //有养生
//                            $para .= " and (ProjectID = $jianf or ProjectID = $yangs)";
//                        }else{
//                            //没有养生
//                            $para .= " and  ProjectID = $jianf";
//                        }
//                    }else{
//                        //没有减肥
//                        if(role('InsertYangs',1)){
//                            //有养生
//                            $para .= " and ProjectID = $yangs";
//                        }else{
//                            //没有养生
//                            $para .= " and username = '$username'";
//                        }
//
//                    }
//                }
//            }
//        }


//        if($Duty == 'yg'){
//            //普通户用 只需查看自己录入
//            if(role('InsertBotox',1)){
//                //录入瘦脸 则允许查看瘦脸所有的词
//                $para .= " and ProjectID = $botox";
//            }
//
//            if(role('InsertJianf',1)){
//                $para .= " or ProjectID = $jianf";
//            }
//
//            if(role('InsertYangs',1)){
//                $para .= " or ProjectID = $yangs";
//            }
//            $para .= " or username = '$username'";
//        }else{
//            //组长或者主管
//            //获取他所在部门的id
//            $DarId = m('user')->where("username = $username")->field('DepartmentID')->find();
//            $DarId = $DarId['DepartmentID'];
//            $users = Department_arr('1','user');
//            $users = implode('","',$users);
//            $para .= ' or userid in ("'.$users.'")';
//        }
        //$post = I('post.');
        //echo json_encode($post);die;
        //判断
//        if($CategoryID != ''){
//            $para = " and CategoryID = $CategoryID";
//        }
//
//        if($ProjectID != ''){
//            $para = "and ProjectID = $ProjectID";
//        }
//
        if($TypeID != ''){
            $para = "and TypeID = $TypeID";
        }
        if($yonghu != ''){
            $para .= " and username = '$yonghu'";
        }

        if($rank_search != ''){
            $para .= " and rank_search = $rank_search";
        }
        if($name != ''){
            $para = " and name = '$name'";
        }
        $data = DataPage(array('database'=>'keywords','order'=>'concat(rank,addtime) desc','para'=>$para));
        //$sql = m('keywords')->getLastSql();
        //echo json_encode($sql);die;
        $data = get_info($data);
        $search = m('k_search')->select();
        $s = array();
        foreach ($search as $k=>$v){
            $s[$v['SearchCategory']] = $v['SearchRange'];
        }
        foreach ($data['list'] as $key => $value){
            $data['list'][$key]['s_name'] = $s[$value['rank_search']];
        }
//        if(role('KeSp',1)){
//            //具有审批功能 给一个删除
//            $data['list']['del'] = 'del';
//        }
        echo json_encode($data);
    }


    public function LookJianfData(){
        $id = I('post.id');

        if ($id != '') {
            $operate = I('post.operate', '');
            $rank = I('post.rank');
            $rank_search = I('post.rank_search');
            $username = $_SESSION['username'];
            $name = I('post.name');
            $database = 'keywords';
            $RecordInfo = array(
                'Head' => 'Keyword',
                'Name' => '关键词',
                'NameKey' => 'name'
            );
            $data['rank'] = $rank;
            $data['rank_search'] = $rank_search;
            if ($operate == 'UpdateAdd') {
                $repeat = m($database)->where("id = '" . $id . "'")->count();
                if ($repeat > 0) {
                    /*检测有没有重复*/
                    $res = m($database)->where("id = '" . $id . "'")->save($data);
                    if ($res) {

                        records($RecordInfo['Head'] . "Update",$username. "更新了" . $RecordInfo['Name'] . " ：" . $name . " ，ID = " . $id);

                        alert("更新成功", 1);

                    } else {
                        alert("修改失败，请刷新重试");
                    }
                } else {
                    alert("修改失败，没有找到该记录，请刷新页面重试");
                }

            } elseif ($operate == 'Get') {
                $info = m($database)->where("id = '" . $id . "'")->find();
                //$sql = m($database)->getLastSql();
                //echo json_encode($sql);die;
                if (!empty($info)) {

                    $info['error'] = '1';

                    echo json_encode($info);
                } else {
                    alert("该条信息没有找到，请刷新页面重试");
                }
            } elseif ($operate == 'del') {

                //当前的是谁操作
                if(role('KeSp',1)){

                    $info = m($database)->where("id = '" . $id . "'")->find();

                    if (!empty($info)) {

                        if (m($database)->where("id = '" . $id . "'")->delete()) {

                            records($RecordInfo['Head'] . "Del",$username. "删除了" . $RecordInfo['Name'] . " ：" . $name . " ，ID = " . $id);
                            alert("删除成功！",1);

                        } else {
                            alert("删除失败，请刷新页面重试");
                        }

                    } else {
                        alert("没有找到该记录，请刷新页面重试");
                    }
                }else{
                    alert('抱歉，你没有这个权限去删除');
                }

            } else {
                alert("不知道你要干啥");
            }

        } else {
            alert("出错，提交的ID为空");
        }
    }
}