<?php
class ClassUserAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('ClassUser',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$AllUser = m('user')->where("status = '0'")->field("userid,username")->order('rank desc,id asc')->select();
		$this->assign("AllUser", $AllUser);

		$this->display();
	}

	public function ClassUserPage(){
		$data = DataPage(array('database'=>'classuser','order'=>'rank desc'));

		echo json_encode($data);
	}

	public function ClassUserData(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');
			$database = 'classuser';
			$RecordInfo = array(
				'Head'=>'ClassUser',
				'Name'=>'排班人员',
				'NameKey'=>'username'
			);

			if($operate == 'UpdateAdd'){
				$key = array(
					'人员'=>'!userid',
					'排序'=>'rank'
				);

				$add = KeyData($key,'post');

				$other = OtherInfo(array('addtime'));
				$add = array_merge($add,$other);

				$UserInfo = m('user')->where("userid = '".$add['userid']."' and status = 0")->field("username,DepartmentID,DepartmentName")->find();
				if(!empty($UserInfo)){

					$add['username'] = $UserInfo['username'];
					$add['DepartmentID'] = $UserInfo['DepartmentID'];
					$add['DepartmentName'] = $UserInfo['DepartmentName'];


					if($id == 'new'){
						/*检测重复*/
						$repeat = m($database)->where("userid = '".$add['userid']."'")->count();
						if($repeat == '0'){

							$new_id = m($database)->add($add);
							if($new_id){

								records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
								alert("添加成功",1);

							}else{
								alert("添加失败，请刷新页面重试");
							}
						}else{
							alert("添加失败，该".$RecordInfo['Name']."已存在，".$RecordInfo['Name']."不得重复");
						}
					}else{

						$repeat = m($database)->where("id = '".$id."'")->count();
						if($repeat > 0){
							/*检测有没有重复*/
							$repeat = m($database)->where("id != '".$id."' and userid = '".$add['userid']."'")->count();
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
				}else{
					alert("添加失败，没有找到该员工信息，请刷新页面重试");
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
					if(m($database)->where("id = '".$id."'")->delete()){

						records($RecordInfo['Head']."Del"," 删除了 ".$info[$RecordInfo['NameKey']]." ".$RecordInfo['Name']."，ID = ".$id);
						alert("删除成功",1);

					}else{
						alert("删除失败，请刷新页面重试");
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