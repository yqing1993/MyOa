<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/7/5
 * Time: 15:43
 */

//关键词搜索级别定义
class KeSearchAction extends loginAction{
    function __construct(){
        parent::__construct();
        role('KeSearch',3);
    }

    public function index(){
        $this->assign('ActionName', $this->getActionName());

        $this->display();
    }

    public function KeSearchPage(){
        $data = DataPage(array('database'=>'k_search','order'=>'rank desc'));

        echo json_encode($data);
    }

    public function KeSearchData(){
        $id = I('post.id');

        if($id != ''){
            $operate = I('post.operate','');
            $database = 'K_search';
            $RecordInfo = array(
                'Head'=>'Search',
                'Name'=>'搜索级别',
                'NameKey'=>'SearchCategory'
            );

            if($operate == 'UpdateAdd'){
                $key = array(
                    '搜索级别名称'=>'!SearchCategory',
                    '搜索级别范围'=>'!SearchRange',
                    '排序'=>'rank'
                );

                $add = KeyData($key,'post');

                $other = OtherInfo(array('userid','username','addtime'));
                $add = array_merge($add,$other);
                //echo json_encode($add);
                if($id == 'new'){

                    /*检测重复*/
                    $repeat = m($database)->where("SearchCategory = '".$add['SearchCategory']."'")->count();
                    //$sql = m($database)->getLastSql();

                    if($repeat == '0'){
                        //首先判断这个是不是第一个级别规定级别必须从1开始
                        //当前的级别-1
                        $last = ((int)$add['SearchCategory']) - 1;
                        if($last > 0){
                            $res = m($database)->where("SearchCategory = '".$last."'")->count();
                            if(!$res){
                                alert('级别必须符合规范不得跳级!');
                            }else{
                                //判断区间是否重复
                                $last_res = m($database)->where("SearchRange = '".$add['SearchRange']."'")->count();
                                if($last_res == '0'){
                                    $last_data = m($database)->where("SearchCategory = '".$last."'")->field('SearchRange')->select();
                                    $last_range = $last_data[0]['SearchRange'];
                                    $last_arr = explode('~',$last_range);
                                    //$sql = m($database)->getLastSql();
                                    $new_arr = explode('~',$add['SearchRange']);
                                    //$data[0] = $last_arr;
                                    //$data[1] = $new_arr;
                                    //echo json_encode($data);
                                    if($new_arr[0] <= $last_arr[1]){
                                        alert('搜索范围开始值必须大于上一个搜索范围结束值！');
                                    }else if($new_arr[0] - $last_arr[1] > 1){
                                        alert('搜索范围必须连贯！');
                                    }else{
                                        $new_id = m($database)->add($add);
                                        if($new_id){
                                            records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']].", 搜索范围为:".$add['SearchRange']." ，ID = ".$new_id);
                                            alert("添加成功",1);

                                        }else{
                                            alert("添加失败，请刷新页面重试");
                                        }
                                    }
                                }else{
                                    alert('搜索范围不得重复！');
                                }
                            }
                        }else{
                            $new_id = m($database)->add($add);
                            if($new_id){
                                records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']].", 搜索范围为:".$add['SearchRange']." ，ID = ".$new_id);
                                alert("添加成功",1);

                            }else{
                                alert("添加失败，请刷新页面重试");
                            }
                        }
                    }else{
                        //echo json_encode($sql);
                        alert("添加失败，该".$RecordInfo['Name']."已存在，".$RecordInfo['Name']."不得重复");
                    }
                }else{

                    $repeat = m($database)->where("id = '".$id."'")->count();
                    if($repeat > 0){
                        /*检测有没有重复*/
                        $repeat = m($database)->where("id != '".$id."' and SearchCategory = '".$add['SearchCategory']."'")->count();
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
                    //获取当前搜索范围的下一个级别 有则不允许删除
                    $now_arr = m($database)->where("id = '".$id."'")->field('SearchCategory,SearchRange')->select();
                    $next = (int)$now_arr[0]['SearchCategory'] + 1;
                    $res = m($database)->where('SearchCategory ='.$next)->find();
                    if($res){
                        alert("删除失败，不可跨级别删除！");
                    }else{
                        if(m($database)->where("id = '".$id."'")->delete()){

                            records($RecordInfo['Head']."Del"," 删除了 ".$RecordInfo['Name']." ".$info[$RecordInfo['NameKey']]." , 搜索范围：".$now_arr[0]['SearchRange']." ，ID = ".$id);
                            alert("删除成功",1);

                        }else{
                            alert("删除失败，请刷新页面重试");
                        }
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