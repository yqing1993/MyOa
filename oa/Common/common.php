<?php
/*订单系统获取当前用户信息*/
function get_user_info($field=''){
    global $user_info;
    if(empty($user_info)){
        if(isset($_SESSION['userid']) && $_SESSION['userid']!=''){
            $user_info = m("user")->where("userid = '".$_SESSION['userid']."'")->field($field)->find();
            if(empty($user_info)){
                alert("登录超时，请重新登录");
            }
        }else{
            alert("登录超时，请重新登录");
        }
    }
    return $user_info;
}

function alert($msg='',$error=0,$status=''){
    if(isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST') && !isset($_POST['alert_type'])){
        echo json_encode(array('error'=>$error,'msg'=>$msg,'status'=>$status));
    }else{
        header("Content-Type: text/html; charset=utf-8");
        if($_POST['alert_type'] == 1){
            echo '<script type="text/javascript">alert("'.$msg.'");window.close();</script>';
        }else{
            echo $msg;
        }
    }
    exit();
}

function KeyData($key=array(),$get='post'){
    $data = Array();
    if(!empty($key)){
        foreach($key as $k=>$v){
            $empty = strstr($v,'!')?1:0;
            $v = str_replace('!','',$v);
            $data[$v] = I($get.".".$v,'');
            if($empty==1 && $data[$v]==''){
                alert($k." 不能为空");
            }
        }
    }
    return $data;
}

function OtherInfo($key=array()){
    $user_info = get_user_info();

    $data = array();
    if(!empty($key)){
        foreach($key as $k=>$v){
            if($v == 'userid'){
                $data['userid'] = $user_info['userid'];
             }elseif($v == 'username'){
                $data['username'] = $user_info['username'];
             }elseif($v == 'addtime'){
                $data['addtime'] = time();
             }
        }
    }
    return $data;
}

function DataPage($arr=array()){
    $database = $arr['database'];
    $para = isset($arr['para'])?$arr['para']:"";
    $field = isset($arr['field'])?$arr['field']:"";
    $order = (isset($arr['order']) && $arr['order']!='')?$arr['order']:'id desc';
    $PageWay = (isset($arr['PageWay']) && $arr['PageWay']!='')?$arr['PageWay']:'get';
    $limit = (isset($arr['limit']) && $arr['limit']!='')?$arr['limit']:30;


    $p = I($PageWay.".p",1);
    $data = m($database)->where(" 1=1 ".$para)->field($field)->order($order)->page($p.','.$limit)->select();
    //$sql = m($database)->getLastSql();

    //return  $sql;
    import("ORG.Util.Page");
    $count = m($database)->where(" 1=1 ".$para)->count();
    $Page = new \Page($count,$limit);
    $show = $Page->show();
    return array('list'=>$data,'page'=>$show);
}

/*模块权限 和 符合权限的人员*/
function role($role,$type='1',$field=""){
    $role = is_array($role)?implode("|",$role):$role;
    if($role!=''){
        $user_info = get_user_info();
        $MacStatus = (isset($_SESSION['MacStatus']) && $_SESSION['MacStatus']!='' && $_SESSION['MacStatus']=='1') ? '' : ',"0"';
        if($type == 1){
            /*判断是否有权限，返回布尔类型*/
            $user_role_arr = json_decode($user_info['role'],true);
            if(!empty($user_role_arr)){
                //$user_role_arr = array_column($user_role_arr,0);
                $count = m("role")->where("id in ('".implode("','",$user_role_arr)."') and status = '0' and module REGEXP '\"all\"|\"".$role."\"".$MacStatus."'")->count();
                if($count > 0){
                    return true;
                }
            }
            return false;
        }elseif($type == 2){
            /*把符合权限的人员交上去*/
            $role_user_arr = array();
            $role_arr = m("role")->where("status = '0' and module REGEXP '\"all\"|\"".$role."\"'")->field("id")->select();
            //$sql = m('role')->getLastSql();
            if(!empty($role_arr)){
                $role_arr = array_column($role_arr,"id");
                //return $role_arr;
                $role_user_arr = m("user")->where("status = '0' and role REGEXP '\"".implode('"|"',$role_arr)."\"'")->field("userid,username".($field!=''?",".$field:""))->order("rank desc")->select();
                //$sql = m('user')->getLastSql();
                //return $sql;
            }

            return $role_user_arr;

        }elseif($type == 6){
            /*把符合权限的人员交上去 除去超级管理员*/
            $role_user_arr = array();
            $role_arr = m("role")->where("status = '0' and module REGEXP '\"".$role."\"'")->field("id")->select();
            //$sql = m('role')->getLastSql();
            if(!empty($role_arr)){
                $role_arr = array_column($role_arr,"id");
                //return $role_arr;
                $role_user_arr = m("user")->where("status = '0' and role REGEXP '\"".implode('"|"',$role_arr)."\"'")->field("userid,username".($field!=''?",".$field:""))->order("rank desc")->select();
                //$sql = m('user')->getLastSql();
                //return $sql;
            }

            return $role_user_arr;
        }elseif($type == 3){
            /*判断是否有权限，返回普通类型*/
            $user_role_arr = json_decode($user_info['role'],true);
            if(!empty($user_role_arr)){
                $count = m("role")->where("id in ('".implode("','",$user_role_arr)."') and status = '0' and module REGEXP '\"all\"|\"".$role."\"".$MacStatus."'")->count();
                if($count > 0){
                    return true;
                }
            }
            alert("抱歉，您没有该权限");
        }elseif($type == 4){
            /*判断有功能权限，返回布尔类型*/
            $user_role_arr = json_decode($user_info['role'],true);
            if(!empty($user_role_arr)){
                $count = m("role")->where("id in ('".implode("','",$user_role_arr)."') and status = '0' and function REGEXP '\"all\"|\"".$role."\"".$MacStatus."'")->count();
                if($count > 0){
                    return true;
                }
            }
            return false;
        }
    }else{
        alert("不知道您要判断什么角色");
    }
}

/*计算更具KEY计算config的值*/
function GetConfig($key,$arrname,$title=''){
    if($title ==''){
        $arr = is_array($arrname)?$arrname:C($arrname);
        $new_arr = array();
        if(is_array($arr) && !empty($arr)){
            foreach($arr as $k=>$v){
                if(is_array($v)){
                    $new_arr = GetConfig($key,$v);
                    if(!empty($new_arr)){
                        return $new_arr;
                    }
                }else{
                    if($k == $key){
                        $new_arr = array('key'=>$k,'value'=>$v);
                        break;
                    }
                }
            }
        }
        return $new_arr;
    }else{
        $arr = array();
        if($arrname == 'provice' || $arrname == 'city'){
            $citys = C("citys");
            if($arrname == 'provice'){
                $arr = array('key'=>$key,'value'=>$citys[$key][0]);

                /*方便下面计算城市*/
                global $c_provice;
                $c_provice = $key;
            }elseif($arrname == 'city'){
                global $c_provice;
                $arr = array('key'=>$key,'value'=>$citys[$c_provice][1][$key]);
            }
        }else{
            $arr = GetConfig($key,$arrname);
        }
        
        if(!empty($arr)){
            return json_encode($arr,JSON_UNESCAPED_UNICODE);
        }else{
            alert($title." 计算属性值的时候，出错");
        }
        
    }
}

/*组织架构*/
function o_organize($user=1){
    $bumen = m("department")->order("rank desc,id asc")->select();
    
    function get($id,$bumen,$user){
        $zi = array();
        
        foreach($bumen as $k=>$v){
            if($v['parentID'] == $id){
                if($user == 1){
                    $zi[] = array('id'=>$v['id'],'DepartmentName'=>$v['DepartmentName'],'user'=>user($v['id']),'zi'=>get($v['id'],$bumen,$user));
                }else{
                    $zi[] = array('id'=>$v['id'],'DepartmentName'=>$v['DepartmentName'],'zi'=>get($v['id'],$bumen,$user));
                }
                
            }   
        }
        
        return $zi;
    }
    
    function user($id){
        $user_arr = m("user")->where("DepartmentID = '".$id."' and status = '0'")->field("userid,username,role,DepartmentID,DepartmentName,Duty,DutyName")->order("rank desc")->select();
        return $user_arr;
    }
    
    $bm_jg = array();
    if(!empty($bumen)){
        foreach($bumen as $k=>$v){
            if($v['id']=='1'){
                $zi_bm = get($v['id'],$bumen,$user);
                $bm_jg[] = array('id'=>$v['id'],'DepartmentName'=>$v['DepartmentName'],'user'=>user($v['id']),'zi'=>$zi_bm);
            }
        }
    }
    return $bm_jg;
}

/*获得指定部门下面的所有部门或者员工*/
function Department_arr($DepartmentID='',$status='department'){
    $arr = array();
    if($DepartmentID!=''){
        $department = m("department")->order("rank desc,id asc")->select();

        if(!function_exists("get")){
            function get($department,$DepartmentID){
                $arr = array();
                $zi_arr = array();
                foreach($department as $k=>$v){
                    if($v['parentID'] == $DepartmentID){
                        $arr[] = $v['id'];
                        $zi_arr = get($department,$v['id']);
                        $arr = array_merge($arr,$zi_arr);
                    }
                }
                return $arr;
            }
        }

        $arr[] = $DepartmentID;
        $arr = array_merge($arr,get($department,$DepartmentID));
    }

    if($status=='department'){
        return $arr;
    }elseif($status == 'user'){
        $user_arr = array();
        if(!empty($arr)){
            $user_arr = m("user")->where("DepartmentID in ('".implode("','",$arr)."') and status = '0'")->field("userid")->order("rank desc")->select();
            $user_arr = array_column($user_arr,'userid');
        }
        return $user_arr;
    }
}

/*职位权限*/
function DutyRole($status,$key='',$type='para',$DepartmentField='DepartmentID',$UseridField='userid'){
    $user_info = get_user_info();

    $Department_arr = array();
    if($status=='Look'){
        if($user_info['Duty'] == 'zg' || $user_info['Duty'] == 'zz'){
            if($key==''){
                return true;
            }else{
                $Department_arr = Department_arr($user_info['DepartmentID'],$key);
            }
        }else{
            if($key==''){
                return false;
            }
        }
    }elseif($status == 'Update'){
        if($user_info['Duty'] == 'zg'){
            if($key==''){
                return true;
            }else{
                $Department_arr = Department_arr($user_info['DepartmentID'],$key);
            }
        }else{
            if($key==''){
                return false;
            }
        }
    }

    if($type=='arr'){
        return $Department_arr;
    }elseif($type=='para') {
        $para = false;
        if(!empty($Department_arr)){
            $field = $key == 'user'?$UseridField:$DepartmentField;
            $para = " and ".$field." in ('".implode("','",$Department_arr)."')";
        }else{
            $para = " and ".$UseridField." = '".$user_info['userid']."'";
        }
        return $para;
    }
}

/*根据userid得到部门指定职位员工信息*/
function DepartmentDucy($userid,$Duct='zg',$field='userid,username'){
    $DepartmentDucy = array();
    $userid = is_array($userid)?implode("','",$userid):$userid;

    $Duct_arr = explode(",",$Duct);
    $Duct = implode("','",$Duct_arr);

    if($userid!=''){
        $AllDepartmentID = m("user")->where("userid in ('".$userid."') and status='0'")->group("DepartmentID")->field("DepartmentID")->select();

        if(!empty($AllDepartmentID)){
            $AllDepartmentID = array_column($AllDepartmentID,'DepartmentID');
            if(!empty($AllDepartmentID)){
                $DepartmentDucy = m("user")->where("DepartmentID in ('".implode("','",$AllDepartmentID)."') and Duty in ('".$Duct."') and status='0'")->field($field)->select();
            }
        }
    }
    return $DepartmentDucy;
}

/*操作日志*/
function records($type='',$content=''){
    if($type!='' && $content!=''){
        $other = OtherInfo(array('userid','username','addtime'));
        $other['type'] = $type;
        $other['content'] = $content;
        m("record")->add($other);
    }
}

/*计算时间*/
function Day($now=1){
    $StartTime = I('post.StartTime','');
    $EndTime = I('post.EndTime','');
    $time = time();
    //$time = strtotime('2017-09-07');
    $AllDay = array();
    if($StartTime != ''){
        $EndTime = $EndTime == '' ? $time : $EndTime;
        $cha_val = $EndTime - $StartTime;
        if($cha_val < 0){
            alert("结束时间不能小于开始时间");
            exit();
        }else if($cha_val > 5356800){
            alert("时间跨度太大，不能超过两个月");
            exit();
        }else{
            $Start = $StartTime;
            while($Start <= $EndTime){
                $AllDay[] = date('Y-m-d',$Start);
                $Start = strtotime("+1 day",$Start);
            }
        }
    }else{
        /*首先获取当月*/
        $month_start = strtotime(date('Y-m',$time).'-1');
        while($month_start <= $time){
            $AllDay[] = date('Y-m-d',$month_start);
            $month_start = strtotime("+1 day",$month_start);
        }

        /*如果每月6号之前，获取上个月整月，加上这个月5天*/
        $six = $now == 0 ? 7 : 6;
        if(count($AllDay) < $six){
            $AllDay = array();
            $top_month_start = strtotime(date('Y-m',strtotime('-1 month',$time)).'-1');

            while($top_month_start  <= $time){
                $AllDay[] = date('Y-m-d',$top_month_start);
                $top_month_start = strtotime("+1 day",$top_month_start);
            }
        }

        /*当天不算*/
        if($now == 0){
            array_pop($AllDay);
        }
    }
    return $AllDay;
}

/*加解密*/
function encrypt($string,$operation,$key='tmskz99@11onjm#xkza'){
    $src  = array("/","+","=");
    $dist = array("_a","_b","_c");
    if($operation=='D'){$string  = str_replace($dist,$src,$string);}
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++)
    {
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++)
    {
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++)
    {
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D')
    {
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
        {
            return substr($result,8);
        }
        else
        {
            return'';
        }
    }
    else
    {
        $rdate  = str_replace('=','',base64_encode($result));
        $rdate  = str_replace($src,$dist,$rdate);
        return $rdate;
    }
}

function DataApi($arr){
    $data = D("DataApi")->Get($arr);
    $data = json_decode($data,true);
    if(!empty($data) && isset($data['error']) && $data['error'] == '0'){
        return $data['data'];
    }
    return array();
}

function SearchPara($arr,$type='post'){
    $para = '';
    if(!empty($arr)){
        foreach($arr as $k=>$v){
            $val = I($type.".".$v[1], '');

            if($val != ''){
                $para .= " and ".$v[0].(is_array($val) ? implode("','", $val) : $val).$v[2];
            }
        }
    }
    return $para;
}

function FindInfo($arr, $type="json"){
    $data = m($arr['database'])->where("1=1 ".$arr['where'])->field($arr['field'])->find();
    if($type == 'json'){
        if(!empty($data)){
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            alert("未找到".$arr['name']."，请刷新页面重试");
        }
        
    }

    return $data;
}
/**
 * 获取当前部门下的所有子类
 * auth   Yq
 * @param $data
 * @param int $pid
 * @param string $html
 * @param int $level
 * @return array
 */
function getChildrenIds ($sort_id='0')
{
    $ids = '';
    $result = M('Department')->where("parentID = ".$sort_id)->select();
    //echo M()->getLastSql();die;
    //dump($result);exit;
    if ($result)
    {
        foreach ($result as $key=>$val)
        {
            $ids .= ','.$val['id'];
            $ids .= getChildrenIds ($val['id']);
        }
    }
    return $ids;
}

/**
 * 根据部门的id去查找所有属于该部门的员工
 * auth   yq
 * @param $DepartmentID
 * @return mixed|string
 */
function getAllUsers($DepartmentID){
    //$DepartmentID = 8;
    $SonIds = getChildrenIds($DepartmentID);
    $SonIds = explode(',',$SonIds);
    $SonIds[0] = $DepartmentID;
    $SonIds = implode(',',$SonIds);
    //dump($_SESSION);
    //dump($SonIds);exit;
    $where['DepartmentID'] = array('in',$SonIds);
    $users = M('classuser')->field('userid')->where($where)->select();
    //echo M('classuser')->getLastSql();exit;

    foreach($users as $k => $v){
        $user[] = $v['userid'];
    }
    $users = implode(',',$user);
    return $users;

}

//插入classcicopy
function ClassCiInsert($id)
{
    $where['id'] = $id;
    $result = m('classci')->where($where)->find();
    $data['cid'] = $result['id'];
    $data['Wechat'] = $result['Wechat'];
    $data['ctime'] = $result['addtime'];
    $data['classname'] = $result['ClassCiName'];
    $rs = m('classcicopy')->add($data);
    return $rs;
}

//当前周的日期
function weektime()
{
    $Datetime = array();

    $time = time();
    $TopWeek = strtotime("+0 day", $time);
    $TopWeek = strtotime(date('Y-m-d', ($TopWeek - ((date('w',$TopWeek) == 0 ? 7 : date('w',$TopWeek)) - 1) * 24 * 3600)));//上周一
    $Datetime[] = $TopWeek;
    $NextWeek = strtotime("+7 day", $time);
    $NextWeek = strtotime(date('Y-m-d', ($NextWeek + (7 - (date('w', $NextWeek) == 0 ? 7 : date('w', $NextWeek))) * 24 * 3600)));//下下周日

    $Datetime[] = $NextWeek;
    return $Datetime;
}

function Jiema($a)
{
    $w = json_decode($a);
    $b = '';
    foreach($w as $vi)
    {
        $b .= $vi.',';
    }
    $c = rtrim($b,',');
    return $c;
}

//根据当前词的id获取子类
function getChildren($id,$type='1'){

    if($type == '1'){
        //行业词只需要判断下面的有无项目词
        $res = M('k_project')->where('CategoryID ='.$id)->find();
        return $res;
    }elseif($type == '2'){
        //项目词只需要判断下面有无类别词
        $res = M('k_type')->where('ProjectID ='.$id)->find();
        return $res;
    }elseif($type == '3'){
        //行业词下类别词需要判断下面有无关键词
        $res1 = M('k_sp_admin')->where('TypeID ='.$id)->find();
        $res2 = M('k_sp_normal')->where('TypeID ='.$id)->find();
        $res3 = M('keywords')->where('TypeID ='.$id)->find();
        if($res1 ||$res2 ||$res3 ){
            return true;
        }else{
            return false;
        }

    }

}

//关键词录入时候判定
function InsertKeyword($database,$add,$RecordInfo){

    /*检测重复*/
    $repeat = m($database)->where("name = '".$add['name']."'")->count();
    //$sql = m($database)->getLastSql();
    if($repeat == '0'){

        $new_id = m($database)->add($add);
        if($new_id){

            records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
            alert("添加成功",1);

        }else{

            alert("添加失败，请刷新页面重试");
        }
    }else{
        //echo json_encode($sql);die;
        alert("添加失败，该".$RecordInfo['Name']."已存在，".$RecordInfo['Name']."不得重复");
    }
}
//减少代码冗余
function get_info($data,$t='1'){
    if($t == '1'){
        $types = m('K_type')->select();
        $projects = m('K_project')->select();
        $categories = m('K_category')->select();
        $t = array();
        $p = array();
        $c = array();
        foreach ($types as $k => $v){
            $t[$v['id']] .= $v['TypeName'];
        }
        foreach ($projects as $k => $v){
            $p[$v['id']] .= $v['ProjectName'];
        }
        foreach ($categories as $k => $v){
            $c[$v['id']] .= $v['CategoryName'];
        }
        foreach ($data['list'] as $key => $value){
            $data['list'][$key]['c_name'] = $c[$value['CategoryID']];
            $data['list'][$key]['p_name'] = $p[$value['ProjectID']];
            $data['list'][$key]['t_name'] = $t[$value['TypeID']];
        }
        return  $data;
    }else if($t == '2'){
        //行业词
        $projects = m('K_project')->select();
        $categories = m('K_category')->select();
        $p = array();
        $c = array();
        foreach ($projects as $k => $v){
            $p[$v['id']] .= $v['ProjectName'];
        }
        foreach ($categories as $k => $v){
            $c[$v['id']] .= $v['CategoryName'];
        }
        foreach ($data['list'] as $key => $value){
            $data['list'][$key]['c_name'] = $c[$value['CategoryID']];
            $data['list'][$key]['p_name'] = $p[$value['ProjectID']];
            $data['list'][$key]['t_name'] = '';
        }
        return  $data;
    }

}

function check_user($duty){
    if($duty == 'yg'){
        return false;
    }else{
        return true;
    }
}

/*
 * 获取部门下所有的人员
 */
function get_ids($dapts){
    if($dapts!=''){
        $new = array();
        //$dapts = ["8", "9", "10", "14",'1'];
        for($i=0;$i<count($dapts);$i++){
            if($dapts[$i] == '1'){
                //说明是全部人员
                $users = m('user')->field('id,username,DepartmentID')->select();
                $new = array_column($users,'id');
                break;
            }
            $users = m('user')->where('DepartmentID ='.$dapts[$i])->field('id,username,DepartmentID')->select();
            $ids = array_column($users,'id');
            $new = array_merge($new,$ids);
        }
        return $new;
    }else{
        return false;
    }

}






