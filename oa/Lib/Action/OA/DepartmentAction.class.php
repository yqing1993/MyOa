<?php
class DepartmentAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('Department',3);
	}

	public function index(){
		$this->assign('Duty',C('Duty'));

		$this->display();
	}

	public function organize(){
		$organize = o_organize(1);

		echo json_encode(array('error'=>'1','data'=>$organize));
	}

	public function MessagePage(){
		$data = DataPage(array('database'=>'message','order'=>'rank desc'));

		echo json_encode($data);
	}

	/*短信模板DATA处理页面*/
	public function Data(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');

			/*新增和修改推荐码*/
			if($operate == 'UpdateAdd'){
				$key = array(
					'部门名称'=>'!DepartmentName',
					'父部门'=>'!parentID',
					'排序'=>'rank'
					);

				$add = KeyData($key,'post');

				$other = OtherInfo(array('userid','username','addtime'));
				$add = array_merge($add,$other);

				if($id == 'new'){

					/*检测部门名称有没有重复*/
					$count = m("department")->where("DepartmentName = '".$add['DepartmentName']."'")->count();
					if($count == '0'){
						$new_id = m("department")->add($add);
						if($new_id){
							records("DepartmentAdd","添加新的部门，部门名称：".$add['DepartmentName']." ，ID = ".$new_id);
							alert("添加成功",1);
						}else{
							alert("添加到数据库失败，请刷新页面重试，或者联系技术");
						}
					}else{
						alert("添加失败，该部门名称已经使用，不得重复");
					}
				}else{
					/*检测该条信息还在不在*/
					$info = m("department")->where("id != '".$id."'")->find();
					if(!empty($info)){
						/*检测名称有没有重复*/
						$count = m("department")->where("id != '".$id."' and DepartmentName = '".$add['DepartmentName']."'")->count();
						if($count == '0'){
							m("department")->where("id = '".$id."'")->save($add);

							/*把通讯录表里面的部门名称修改一下*/
							if($info['DepartmentName'] != $add['DepartmentName']){
								m("user")->where("DepartmentID = '".$id."'")->save(array('DepartmentName'=>$add['DepartmentName']));
							}

							records("MessageUpdate","更新了部门，部门名称：".$add['DepartmentName']." ，ID = ".$id);

							alert("更新成功",1);
						}else{
							alert("更新失败，该部门名称已经使用，不得重复");
						}
					}else{
						alert("更新失败，没有找到该条记录，请刷新页面重试");
					}
				}
			}elseif($operate=='Get'){
				$info = m("department")->where("id = '".$id."'")->find();
				if(!empty($info)){

					$info['error'] = '1';

					echo json_encode($info);
				}else{
					alert("该条信息没有找到，请刷新页面重试");
				}
			}elseif($operate=='del'){
				if($id != '1'){
					$info = m("department")->where("id = '".$id."'")->find();
					if(!empty($info)){

						/*首先判断这个部门下面，还有没有员工*/
						$arr = Department_arr($id,'user');
						if(empty($arr)){
							$arr = Department_arr($id,'department');
							if(count($arr) == 1){
								if(m("department")->where("id = '".$id."'")->delete()){
									records("DepartmentDel"," 删除了 ".$info['DepartmentName']." 部门，ID = ".$id);
									alert("删除成功",1);
								}else{
									alert("删除失败，请刷新页面重试");
								}
							}else{
								alert("该部门下面还有子部门，请先删除子部门");
							}
						}else{
							alert("该部门下面还有员工，请先移动或删除员工");
						}
					}else{
						alert("没有找到该部门，请刷新页面重试");
					}
				}else{
					alert("根部门不能删除");
				}
			}elseif($operate == 'GetUser'){
				$UserInfo = m("user")->where("userid = '".$id."' and status='0'")->field("userid,username,DepartmentID,Duty")->find();

				if(!empty($UserInfo)){
					$AllDepartment = o_organize(0);
					echo json_encode(array('error'=>'1','AllDepartment'=>$AllDepartment,'UserInfo'=>$UserInfo));
				}else{
					alert("没有找到该员工信息，请刷新页面重试");
				}
			}elseif($operate == 'UpdateUser'){

				$UserInfo = m("user")->where("userid = '".$id."' and status='0'")->find();

				if(!empty($UserInfo)){
					$key = array(
						'所属部门'=>'!DepartmentID',
						'职位'=>'!Duty'
						);

					$add = KeyData($key,'post');

					$DepartmentInfo = m("department")->where("id = '".$add['DepartmentID']."'")->find();

					if(!empty($DepartmentInfo)){
						$add['DepartmentName'] = $DepartmentInfo['DepartmentName'];

						$DutyInfo = GetConfig($add['Duty'],'Duty');
						$add['DutyName'] = $DutyInfo['value'];

						if(m("user")->where("userid = '".$id."' and status='0'")->save($add)){

							if($UserInfo['DepartmentID'] != $add['DepartmentID']){
								records("UserDepartment","把 ".$UserInfo['username']." 从 ".$UserInfo['DepartmentName']." 移到 ".$add['DepartmentName']);
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

							if($UserInfo['Duty'] != $add['Duty']){
								records("UserDuty","把 ".$UserInfo['username']." 由 ".$UserInfo['DutyName']." 变动为 ".$add['DutyName']);
							}

							
							alert("修改成功",1);

						}else{
							alert("没有变动",2);
						}
					}else{
						alert("没有找到该部门，请刷新页面重试");
					}
				}else{
					alert("没有找到该员工信息，请刷新页面重试");
				}
			}else{
				alert("不知道你要干啥");
			}

		}else{
			alert("出错，提交的ID为空");
		}
	}

}