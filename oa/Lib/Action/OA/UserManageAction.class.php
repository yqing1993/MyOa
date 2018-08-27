<?php
class UserManageAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('UserManage',3);
	}

	public function index(){
		/*全部角色*/
		$AllRole = m("role")->where("status = 0")->field("id,RoleName")->order("id asc")->select();
		$this->assign("AllRole",$AllRole);

		/*获取全部职位*/
		$this->assign('Duty',C('Duty'));

		/*获取全部部门*/
		$AllDepartment = o_organize(0);
		$this->assign('AllDepartment',$AllDepartment);

		$this->display();
	}

	public function Page(){
		$data = DataPage(array('database'=>'user','order'=>'rank desc'));
		if(!empty($data['list'])){
			foreach($data['list'] as $k=>$v){

				/*计算角色名称*/
				$role_arr = json_decode($v['role'],true);
				if(!empty($role_arr)){
					foreach($role_arr as $k1=>$v1){
						$info = array();
						$info = m("role")->where("id = '".$v1."'")->field("RoleName")->find();

						if(!empty($info) && $info['RoleName']!=''){
							$data['list'][$k]['RoleName'][] = $info['RoleName'];
						}
					}

				}

			}
		}

		echo json_encode($data);
	}

	public function Data(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');

			/*新增和修改用户*/
			if($operate == 'UpdateAdd'){

				$key = array(
					'用户名'=>'!userid',
					'真实姓名'=>'!username',
					'密码'=>'password',
					'MAC'=>'mac',
					'免MAC登陆'=>'MacSwitch',
					'所属部门'=>'!DepartmentID',
					'职位'=>'!Duty',
					'排序'=>'rank'
					);

				$add = KeyData($key,'post');

				/*获取角色*/
				$role = isset($_POST['role'])?$_POST['role']:"";

				if($role != ''){
					$role_arr = json_decode($role,true);
					if(!empty($role_arr)){

						$other = OtherInfo(array('addtime'));
						$add = array_merge($add,$other);

						/*角色*/
						$add['role'] = json_encode($role_arr,JSON_UNESCAPED_UNICODE);

						/*所属部门*/
						$DepartmentInfo = m("department")->where("id = '".$add['DepartmentID']."'")->find();
						if(!empty($DepartmentInfo)){
							$add['DepartmentName'] = $DepartmentInfo['DepartmentName'];

							/*所属职位*/
							$DutyInfo = GetConfig($add['Duty'],'Duty');
							$add['DutyName'] = $DutyInfo['value'];


							if($id == 'new'){
								/*密码不能为空*/
								if($add['password']==''){
									alert("新建用户，密码不能为空");
								}

								/*新建用户，检测用户名有没有重复*/
								$count = m("user")->where("userid = '".$add['userid']."'")->count();
								if($count == '0'){

									if(m("user")->add($add)){

										$time = time();
										m('department_time')->add(array(
											'userid'=>$add['userid'],
											'username'=>$add['username'],
											'DepartmentID'=>$add['DepartmentID'],
											'DepartmentName'=>$DepartmentInfo['DepartmentName'],
											'StartTime'=>$time,
											'StartTimes'=>date('Y-m-d H:i:s',$time)
										));

										records("UserAdd","添加新的用户，用户名：".$add['userid']." ，姓名：".$add['username']);

										alert("添加成功",1);

									}else{
										alert("添加失败，数据库添加失败，请联系技术人员");
									}
								}else{
									alert("该用户名已经存在，不得重复");
								}
							}else{

								/*如果密码为空，那么不修改密码*/
								if($add['password']==''){
									unset($add['password']);
								}

								/*修改用户信息，检测用户在不在*/
								$count = m("user")->where("id = '".$id."'")->find();
								if(!empty($count)){

									/*检查用户名有没有重复*/
									$counts = m("user")->where("id != '".$id."' and userid = '".$add['userid']."'")->count();
									if($counts == '0'){

										$TopUserInfo = m("user")->where("userid = '".$add['userid']."'")->find();

										if(m("user")->where("id = '".$id."'")->save($add)){

											if($TopUserInfo['DepartmentID'] != $add['DepartmentID']){
												$UserInfo = m("user")->where("userid = '".$add['userid']."'")->find();
												$time = time();
												m('department_time')->add(array(
													'userid'=>$UserInfo['userid'],
													'username'=>$UserInfo['username'],
													'DepartmentID'=>$add['DepartmentID'],
													'DepartmentName'=>$DepartmentInfo['DepartmentName'],
													'StartTime'=>$time,
													'StartTimes'=>date('Y-m-d H:i:s',$time)
												));
											}

											records("UserUpdate","修改了用户信息，数据库ID = ".$id);

											alert("修改成功",1);

										}else{
											alert("修改失败，产品数据库修改失败");
										}
									}else{
										alert("该用户名已经存在，不得重复");
									}
								}else{
									alert("修改失败，该条信息没有找到，请刷新页面重试");
									exit();
								}
							}
						}else{
							alert("没有找到该部门，请刷新页面重试");
						}
					}else{
						alert("请选择所属角色");
					}
				}else{
					alert("请选择所属角色");
				}
			}elseif($operate=='status'){
				$database = I("post.database",'');
				if($database!=''){
					$status = I("post.status");
					if($status!=''){

						/*检测该条数据还在不在*/
						$info = m($database)->where("id = '".$id."'")->find();

						if(!empty($info)){
							$data['status'] = $status;

							if(m($database)->where("id = '".$id."'")->save($data)){

								$status_str = $status=='0'?'启用':'停用';

								records("UserStatus",$status_str." 了 ".$info['username']." 账号 userid = ".$info['userid']);

								alert("状态修改成功",1,$status);
							}else{
								alert("状态修改失败");
							}
						}else{
							alert("数据库里面没有找到该条数据，请刷新页面重试");
						}
					}else{
						alert("修改的状态不能为空");
					}
				}else{
					alert("不知道需要修改哪个状态");
				}
			}elseif($operate=='Get'){
				/*获取单个用户信息，方便编辑model框*/
				$user_info = m("user")->where("id = '".$id."'")->find();
				if(!empty($user_info)){

					$user_info['error'] = '1';

					/*删除密码*/
					unset($user_info['password']);

					echo json_encode($user_info);
				}else{
					alert("该条产品信息没有找到，请刷新页面重试");
				}
			}elseif($operate=='del'){
				if(m("user")->where("id = '".$id."'")->delete()){

					records("UserDel"," 删除了 ID = ".$id." 用户信息");
					alert("删除成功",1);

				}else{
					alert("删除失败，请刷新页面重试");
				}
			}else{
				alert("不知道你要干啥");
			}

		}else{
			alert("出错，提交的ID为空");
		}
	}
}