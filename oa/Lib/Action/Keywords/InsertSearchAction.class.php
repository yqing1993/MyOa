<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/7
 * Time: 10:27
 */

class InsertSearchAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('InsertSearch',3);
    }

    public function index(){
        $ActionName = $this->getActionName();
        $this->assign('ActionName',$ActionName);
        $search = m('k_search')->where("status='0'")->order('rank desc')->field('id,SearchRange,SearchCategory')->select();
        $this->assign('search', $search);
        $this->display();
    }

    public function InsertSearchPage(){
        $data = DataPage(array('database'=>'keywords','order'=>'concat(rank,id, addtime) desc','para'=>"and CategoryID = 1 and (rank_search = '' or rank_search = '0') and sp_res = '1' "));
        $data = get_info($data);
        echo json_encode($data);
    }

    public function InsertSearchData(){
        $id = I('post.id');
        $name = I('post.name');
        $rank_search = I('post.rank_search');
        $search_number = I('post.search_number');
        $username = $_SESSION['username'];
        if($id != ''){
            $operate = I('post.operate','');
            $database = 'keywords';
            $RecordInfo = array(
                'Head'=>'InsertSearch',
                'Name'=>'搜索级别',
                'NameKey'=>'name'
            );
            if($operate == 'UpdateAdd'){
                //监测该数据还在不在
                $repeat = m($database)->where("id = '".$id."'")->count();
                    if($repeat > 0){
                        $add['rank_search'] = $rank_search;
                        $add['search_number'] = $search_number;
//                        //检测当前录取的搜索量在不在当前的区间
//                        $range = m('k_search')->where("SearchCategory = $rank_search")->field('SearchRange')->find();
//                        $range = $range['SearchRange'];
//                        $arr_range = explode('~',$range);
//                        if($search_number >= $arr_range[0] && $search_number <= $arr_range[1] ){
                            $res = m($database)->where("id = '".$id."'")->save($add);
                            if($res){
                                records($RecordInfo['Head'],"录入了关键词 ".$name." 的搜索级别，搜索级别为：".$add['rank_search']." ，ID = ".$id);
                                alert("录入成功",1);
                            }else{
                                alert('录入失败，请重试！');
                            }
//                        }else{
//                            alert("搜索量必须要在对应的搜索区间内！");
//                        }

                    }else{
                        alert("录入失败，没有找到该记录，请刷新页面重试");
                    }
            }elseif($operate=='Get'){
                $info = m($database)->where("id = '".$id."'")->find();
                if(!empty($info)){

                    $info['error'] = '1';

                    echo json_encode($info);
                }else{
                    alert("该条信息没有找到，请刷新页面重试");
                }
            }elseif ($operate == 'del') {
                $info = m($database)->where("id = '" . $id . "'")->find();
                //$sql = m($database)->getLastSql();
                //echo json_encode($sql);die;
                if (!empty($info)) {
                    $name = $info['name'];
                    if (m($database)->where("id = '" . $id . "'")->delete()) {

                        records($RecordInfo['Head'] . "Del",$username. "删除了关键词 ：" . $name . " ，ID = " . $id);
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
        }else{
            alert("出错，提交的ID为空");
        }
    }
}