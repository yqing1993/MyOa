<?php
class ClassCiAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('ClassCi',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$AllWechat = DataApi(array('type'=>'Group','data'=>array('database'=>'wechat','para'=>'and status=\'0\'','order'=>'rank desc','field'=>'WechatID,WechatName')));

		$AllWechat = array_column($AllWechat, 'WechatName', 'WechatID');
		//dump($AllWechat);die;
		$this->assign('AllWechat', $AllWechat);
		/*$where['id'] = 4;
		$result = m('classci')->where($where)->find();*/


		$this->display();
	}

	public function ClassCiPage(){
		$data = DataPage(array('database'=>'classci','order'=>'rank desc,id asc'));
		echo json_encode($data);
	}

	public function ClassCiData(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');
			$database = 'classci';
			$RecordInfo = array(
				'Head'=>'ClassCi',
				'Name'=>'班次',
				'NameKey'=>'ClassCiName'
			);

			if($operate == 'UpdateAdd'){
				$key = array(
					'班次名称'=>'!ClassCiName',
					'排序'=>'rank'
				);

				$add = KeyData($key,'post');
				//echo json_encode($operate);die;
				$other = OtherInfo(array('addtime'));
				$add = array_merge($add,$other);

				$Wechat = isset($_POST['Wechat']) ? $_POST['Wechat'] : "";
				$Wechat_arr = array();
				if($Wechat != ''){
					$Wechat_arr = json_decode($Wechat, true);
					if(!empty($Wechat_arr)){
						$add['Wechat'] = json_encode($Wechat_arr, JSON_UNESCAPED_UNICODE);
					}
				}

				if($id == 'new'){
					/*检测重复*/
					$repeat = m($database)->where("ClassCiName = '".$add['ClassCiName']."'")->count();
					if($repeat == '0'){

						/*检测微信号有没有在其他班次*/
						if(!empty($Wechat_arr)){
							foreach($Wechat_arr as $k=>$v){
								$info = m($database)->where("Wechat regexp '\"".$k."\"\:'")->find();
								if(!empty($info)){
									alert("添加失败，".$v." 该微信已存在 ".$info['ClassCiName']." 班次，一个微信号不能存在两个班次");
								}
							}
						}

						//把添加前的数据填入子表classcicopy
						$rs = ClassCiInsert($id);



						$new_id = m($database)->add($add);
						if($new_id){

							records($RecordInfo['Head']."Add","添加新的".$RecordInfo['Name']."，".$RecordInfo['Name']."名称：".$add[$RecordInfo['NameKey']]." ，ID = ".$new_id);
							alert("添加成功",1);

						}else{
							alert("添加失败，请刷新页面重试");
						}
					}else{
						alert("添加失败，该".$RecordInfo['Name']."名称已存在，".$RecordInfo['Name']."名称不得重复");
					}
				}else{

					$repeat = m($database)->where("id = '".$id."'")->count();
					if($repeat > 0){
						/*检测有没有重复*/
						$repeat = m($database)->where("id != '".$id."' and ClassCiName = '".$add['ClassCiName']."'")->count();
						if($repeat == '0'){

							/*检测微信号有没有在其他班次*/
							if(!empty($Wechat_arr)){
								foreach($Wechat_arr as $k=>$v){
									$info = m($database)->where("id != '".$id."' and Wechat regexp '\"".$k."\"\:'")->find();
									if(!empty($info)){
										alert("修改失败，".$v." 该微信已存在 ".$info['ClassCiName']." 班次，一个微信号不能存在两个班次");
									}
								}
							}

							//把添加前的数据填入子表classcicopy
							$rs1 = ClassCiInsert($id);



							m($database)->where("id = '".$id."'")->save($add);

							records($RecordInfo['Head']."Update","更新了".$RecordInfo['Name']."，".$RecordInfo['Name']."名称：".$add[$RecordInfo['NameKey']]." ，ID = ".$id);

							alert("更新成功",1);

						}else{
							alert("修改失败，该".$RecordInfo['Name']."名称已存在，".$RecordInfo['Name']."名称不得重复");
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