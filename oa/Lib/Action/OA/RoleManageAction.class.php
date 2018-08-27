<?php
class RoleManageAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('RoleManage',3);
	}

	public function Role(){
		$this->assign("module",C("module"));
		$this->assign("function",C("function"));


		/*获取全部部门，架构组织*/
		$AllDepartment = o_organize(0);
		$this->assign('AllDepartment',$AllDepartment);

		$this->display();
	}

	public function Page(){
		$data = DataPage(array('database'=>'role'));
		echo json_encode($data);
	}

	public function Data(){
		$id = I('post.id','');
		if($id != ''){
			$operate = I('post.operate','');

			/*新增和修改*/
			if($operate == 'UpdateAdd'){

				$key = array('角色名称'=>'!RoleName');
				$add = KeyData($key,'post');
				$other = OtherInfo(array('userid','username','addtime'));

				/*模块权限*/
				$module = isset($_POST['module'])?$_POST['module']:"";
				$module_arr = $module != ''?json_decode($module,true):"";
				$add['module'] = $module_arr !='' ? json_encode($module_arr,JSON_UNESCAPED_UNICODE) : "";

				/*功能权限*/
				$function = isset($_POST['function'])?$_POST['function']:"";
				$function_arr = $function != '' ? json_decode($function,true) : "";
				$add['function'] = $function_arr != '' ? json_encode($function_arr,JSON_UNESCAPED_UNICODE) : "";
				
				if($id == 'new'){
					$add = array_merge($add,$other);

					/*检测角色名称有没有重复*/
					$count = m("role")->where("RoleName = '".$add['RoleName']."'")->count();

					if($count=='0'){
						if(m("role")->add($add)){

							records("AddRole","添加新的角色，角色名称：".$add['RoleName']);

							alert("添加角色成功",1);
						}else{
							alert("添加角色失败，数据库添加失败");
						}
					}else{
						alert("该角色名称已经存在，不得重复");
					}
				}else{
					/*检测该条数据还在不在*/
					$info = m("role")->where("id = '".$id."'")->find();
					if(!empty($info)){
						$add = array_merge($add,$other);
						if(m("role")->where("id = '".$id."'")->save($add)){

							records("UpdateRole","修改了 ID = ".$id." 角色内容");

							alert("修改成功",1);
						}
					}else{
						alert("数据库里面没有找到该条数据，请刷新页面重试");
					}
				}
			}elseif($operate=='status'){
				$status = I("post.status");
				if($status!=''){

					/*检测该条数据还在不在*/
					$info = m("role")->where("id = '".$id."'")->find();

					if(!empty($info)){
						$data = OtherInfo(array('userid','username','addtime'));
						$data['status'] = $status;

						if(m("role")->where("id = '".$id."'")->save($data)){

							$status_str = $status=='0'?'启用':'停用';


							records("RoleStatus",$status_str." 了 ID = ".$id." 角色");

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
			}elseif($operate=='Get'){
				/*获取单个信息，方便编辑model框*/
				$info = m("role")->where("id = '".$id."'")->find();
				if(!empty($info)){

					$info['error'] = '1';

					echo json_encode($info);
				}else{
					alert("该条信息没有找到，请刷新页面重试");
				}
			}elseif($operate=='del'){
				if(m("role")->where("id = '".$id."'")->delete()){

					records("RoleDel"," 删除了 ID = ".$id." 角色");
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