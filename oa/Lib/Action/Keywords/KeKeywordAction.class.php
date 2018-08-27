<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/18
 * Time: 13:28
 */

class KeKeywordAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('KeKeyword',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());
        $categories = m('k_category')->where("status='0'")->order('rank desc')->field('id,CategoryName')->select();
        $projects = m('k_project')->where("status='0'")->order('rank desc')->field('id,ProjectName,CategoryID')->select();
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
//        $rank_search = '1';
//        $range = m('k_search')->where("SearchCategory = $rank_search")->field('SearchRange')->find();
//        $range = $range['SearchRange'];
//        $arr_range = explode('~',$range);
//        dump($arr_range);
//        dump($range);die;

        if($_SESSION['Duty'] != 'yg'){
            $username = $_SESSION['username'];
            $DarId = m('user')->where("username = '$username'")->field('DepartmentID')->find();
            $DarId = $DarId['DepartmentID'];
            $users = Department_arr('1','user');
            //dump($users);
            //判断所有属于关键词木块的人
            $k_users = role('KeBs',2);
            $k_users = array_column($k_users,'userid');
            //属于当前部门的人 和 属于关键词模块的人交集
            $users = array_intersect($users,$k_users);
            //dump($k_users);
            //dump($users);
            $users = implode('","',$users);
            $usernames = m('user')->where('userid in ("'.$users.'")')->field('username')->select();
//        $usernames = array_column($usernames,'username');
//        $sql = m('user')->getLastSql();
//        dump($sql);
            // dump($usernames);
            // dump($users);die;
//        $sql = m('user')->getLastSql();
//        dump($sql);
            //dump($DarId);die;
            $this->assign('users',$usernames);
        }
        $this->assign('categories', $categories);
        $search = m('k_search')->where("status='0'")->order('rank desc')->field('id,SearchRange,SearchCategory')->select();
        $this->assign('search', $search);
        $this->display();

    }

    public function KeKeywordPage(){
        $CategoryID = I('post.CategoryID');
        $ProjectID = I('post.ProjectID');
        $TypeID = I('post.TypeID');
        $rank_search = I('post.rank_search');
        $name = I('post.name');
        $yonghu = I('post.username');
        $para = " and status = '0'";
        //$post = I('post.');
        //echo json_encode($post);die;
        //判断

        if($yonghu != ''){
            $para .= " and username = '$yonghu'";
        }

        if($CategoryID != ''){
            $para = " and CategoryID = $CategoryID";
        }

        if($ProjectID != ''){
            $para = "and ProjectID = $ProjectID";
        }

        if($TypeID != ''){
            $para = "and TypeID = $TypeID";
        }

        if($rank_search != ''){
            $para .= " and rank_search = $rank_search";
        }
        if($name != ''){
            $para = " and name = '$name'";
        }
        $data = DataPage(array('database'=>'keywords','order'=>'concat(addtime,search_number,id,rank) desc','para'=>$para));
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
        echo json_encode($data);
    }


    public function KeKeywordData(){
        $id = I('post.id');

        if ($id != '') {
            $operate = I('post.operate', '');
            $rank = I('post.rank');
            $rank_search = I('post.rank_search');
            $search_number = I('post.search_number');
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
            $data['search_number'] = $search_number;
            if ($operate == 'UpdateAdd') {
                $repeat = m($database)->where("id = '" . $id . "'")->count();
                if ($repeat > 0) {
                    //检测当前录取的搜索量在不在当前的区间
//                    $range = m('k_search')->where("SearchCategory = $rank_search")->field('SearchRange')->find();
//                    $range = $range['SearchRange'];
//                    $arr_range = explode('~',$range);
//                    if($search_number >= $arr_range[0] && $search_number <= $arr_range[1] ){
                        $res = m($database)->where("id = '" . $id . "'")->save($data);
                        if ($res) {

                            records($RecordInfo['Head'] . "Update",$username. "更新了" . $RecordInfo['Name'] . " ：" . $name . " ，ID = " . $id);

                            alert("更新成功", 1);

                        } else {
                            alert("修改失败，请刷新重试");
                        }
//                    }else{
//                        alert("搜索量必须要在对应的搜索区间内！");
//                    }

                } else {
                    alert("修改失败，没有找到该记录，请刷新页面重试");
                }

            } elseif ($operate == 'Get') {
                $info = m($database)->where("id = '" . $id . "'")->find();
                if (!empty($info)) {

                    $info['error'] = '1';

                    echo json_encode($info);
                } else {
                    alert("该条信息没有找到，请刷新页面重试");
                }
            } elseif ($operate == 'del') {
                $info = m($database)->where("id = '" . $id . "'")->find();
                //$sql = m($database)->getLastSql();
                //echo json_encode($sql);die;
                if (!empty($info)) {
                    $name = $info['name'];
                    if (m($database)->where("id = '" . $id . "'")->delete()) {

                        records($RecordInfo['Head'] . "Del",$username. "删除了" . $RecordInfo['Name'] . " ：" . $name . " ，ID = " . $id);
                        alert("删除成功！",1);

                    } else {
                        alert("删除失败，请刷新页面重试");
                    }

                } else {
                    alert("没有找到该记录，请刷新页面重试");
                }
            } else {
                alert("不知道你要干啥");
            }

        } else {
            alert("出错，提交的ID为空");
        }
    }
}