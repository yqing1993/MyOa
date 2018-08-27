<?php
class RecordYuHuAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('RecordYuHu',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$AllPlatform = m('j_platform')->where("status='0'")->order('rank desc')->field('id,PlatformName')->select();
		$this->assign('AllPlatform', $AllPlatform);

		$AllHu = m('j_hu')->where("status='0'")->order('rank desc')->field('id,HuName,PlatformID')->select();
		$this->assign('AllHu', $AllHu);

		$this->display();
	}

	public function RecordYuHuPage(){
		$data = DataPage(array('database'=>'j_recordyuhu','order'=>'Day desc,id desc'));

		echo json_encode($data);
	}

	public function RecordYuHuData(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');
			$database = 'j_recordyuhu';
			$RecordInfo = array(
				'Head'=>'RecordYuHu',
				'Name'=>'竞价预算录入'
			);

			if($operate == 'UpdateAdd'){

				$other = OtherInfo(array('userid','username','addtime'));

				if($id == 'new'){
					$RecordDay = I('post.RecordDay','');
					if($RecordDay == ''){
						alert("录入失败，录入日期不能为空");
					}else{
						$Day['Day'] = strtotime($RecordDay." 00:00:00");
					}

					$RecordData = isset($_POST['RecordData'])?$_POST['RecordData']:"";
					$RecordData_arr = json_decode($RecordData,true);

					$add = array();
					if(!empty($RecordData_arr)){
						foreach($RecordData_arr as $k=>$v){
							$HuInfo = m('j_hu')->where("id = '".$v['HuID']."' and status = '0'")->field('HuName,PlatformID')->find();

							$one = array();
							if(!empty($HuInfo)){
								$one['PlatformID'] = $HuInfo['PlatformID'];
								$one['PlatformInfo'] = FindInfo(
									array(
										'database' =>'j_platform',
										'where'    =>" and id = '".$HuInfo['PlatformID']."'",
										'field'    =>'PlatformName',
										'name'     =>'ID = '.$HuInfo['PlatformID'].'该平台'
									),'json');

								$one['HuInfo'] = json_encode($HuInfo, JSON_UNESCAPED_UNICODE);

								$add[] = array_merge($one,$v,$other,$Day);
							}else{
								alert("录入失败，未找到该户信息，户ID=".$v['HuID']);
							}
						}
					}

					$repeat = array();
					foreach($add as $k=>$v){
						$count = m($database)->where("HuID = '".$v['HuID']."' and Day = '".$v['Day']."'")->field('addtime')->find();

						if(!empty($count)){
							$repeat[] = array('msg'=>date("Y-m-d H:i:s",$count['addtime']).' 已经记录', 'HuID'=>$v['HuID']);
						}
					}

					if(empty($repeat)){
						if(m($database)->addAll($add)){
							records($RecordInfo['Head']."Add","录入了 ".$RecordInfo['Name']);
							alert("录入成功",1);
						}
					}else{
						echo json_encode(array('error'=>'2','repeat'=>$repeat));
					}

				}else{

					$repeat = m($database)->where("id = '".$id."'")->count();
					if($repeat > 0){
						$add = array();
						$add['Money'] = I('post.Money',0);
						$add['ps'] = I('post.ps','');

						$add = array_merge($add, $other);

						m($database)->where("id = '".$id."'")->save($add);

						records($RecordInfo['Head']."Update","更新了 ".$RecordInfo['Name']."，ID = ".$id);

						alert("更新成功",1);
					}else{
						alert("修改失败，没有找到该记录，请刷新页面重试");
					}
				}
			}elseif($operate=='del'){
				$info = m($database)->where("id = '".$id."'")->find();
				if(!empty($info)){
					if(m($database)->where("id = '".$id."'")->delete()){

						records($RecordInfo['Head']."Del"," 删除了 ".$RecordInfo['Name']."，ID = ".$id);
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