<?php
class updateModel extends Model{
	public function bumen(){
		$access_token = token();
		$data = curl("https://oapi.dingtalk.com/department/list?access_token=".$access_token);
		//print_r ($data);
		
		$m = m("ddbumen");
		$m->where('1')->delete();
		
		$state = '';
		foreach($data['department'] as $k=>$v){
			$id = $v['id'];
			
			$m = m("ddbumen");
			if($m->add($v)){
				$state .= $v['name']."  更新成功<br />";
			}else{
				$state .= $v['name']."  更新失败，程序以停止更新，请刷新页面重试<br />";
			}
		}
		return $state;
	}
	
	public function user(){
		set_time_limit(0);//程序不终止
		
		$state = self::bumen();//先更新部门
		
		$m = m("ddbumen");
		$bumen_arr = $m->select();
		
		if(!empty($bumen_arr)){
			$m = m("dduser");
			$m->where('1')->delete();
			
			$gong = 0;
			$success = 0;
			
			$user_arr = array();
			foreach($bumen_arr as $k=>$v){
				$access_token = token();
				$data = curl("https://oapi.dingtalk.com/user/list?access_token=".$access_token."&department_id=".$v['id']);//请求得到每个部门下面的员工list
				
				foreach($data['userlist'] as $k1=>$v1){//遍历部门下面的员工
					if(!in_array($v1['userid'],$user_arr)){//如果一个员工有存在两个部门内，那就计算一个就好了
						$user_arr[] = $v1['userid'];//把员工ID记录到数组里面，方便上面的判断
						$arr = $v1;//记录员工数据
						$arr['department'] = implode("|",$v1['department']);//把员工部门ID用|符号分割连起来，方便存入数据库
						$arr['department_name'] = '';//建立部门名称变量
						foreach($v1['department'] as $k2=>$v2){//计算员工所在部门的名称
							$m = m("ddbumen");
							$bumen_name = $m->where("id='".$v2."'")->find();
							$arr['department_name'] .= $bumen_name['name']."|";
						}
						$arr['department_name'] = trim($arr['department_name'],"|");
					
						$m = m("dduser");
						if($m->add($arr)){//把员工数据存入数据库
							$success++;
						}
						$gong++;
					}
				}
			}
			
			$error = $gong-$success;
			
			return $state."共处理".$gong."条员工数据，成功".$success."条，失败".$error."条";
		}else{
			return "数据库部门数据为空";
		}
		
	}
}