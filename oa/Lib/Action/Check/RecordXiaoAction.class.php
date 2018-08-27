<?php
class RecordXiaoAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('RecordXiao',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$AllProject = m('j_project')->where("status='0'")->order('rank desc')->field('id,ProjectName')->select();
		$this->assign('AllProject', $AllProject);

		$AllPlatform = m('j_platform')->where("status='0'")->order('rank desc')->field('id,PlatformName')->select();
		$this->assign('AllPlatform', $AllPlatform);

		$AllHu = m('j_hu')->where("status='0'")->order('rank desc')->field('id,HuName,PlatformID')->select();
		$this->assign('AllHu', $AllHu);

		$AllZhaoWeb = m('j_zhaoweb')->where("status='0'")->order('rank desc')->field('id,ZhaoWebName,ProjectID')->select();
		$this->assign('AllZhaoWeb', $AllZhaoWeb);

		$AllWechat = m('j_wechat')->where("status='0'")->order('rank desc')->field('WechatID,WechatName,ProjectID')->select();
		$this->assign('AllWechat', $AllWechat);

		$AllTuiWeb = m('j_tuiweb')->where("status = '0'")->field('status,rank,userid,username,addtime',true)->order('ProjectID asc,PlatformID asc,HuID asc,ZhaoWebID asc,rank desc,id asc')->select();
		$this->assign("AllTuiWeb", $AllTuiWeb);



		$this->display();
	}

	public function RecordXiaoPage(){
		$data = DataPage(array('database'=>'j_recordxiao','order'=>'StartTime desc,id asc'));

		echo json_encode($data);
	}

	public function RecordXiaoData(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');
			$database = 'j_recordxiao';
			$RecordInfo = array(
				'Head'=>'RecordXiao',
				'Name'=>'竞价消费录入'
			);

			if($operate == 'UpdateAdd'){

				$other = OtherInfo(array('userid','username','addtime'));

				if($id == 'new'){
					$RecordData = isset($_POST['RecordData'])?$_POST['RecordData']:"";
					$RecordData_arr = json_decode($RecordData,true);

					$add = array();
					if(!empty($RecordData_arr)){
						foreach($RecordData_arr as $k=>$v){
							$TuiWebInfo = m('j_tuiweb')->where("id = '".$v['TuiWebID']."' and status = '0'")->field('TuiWebName,ProjectID,PlatformID,HuID,ZhaoWebID,WechatID,TuiWebUrl')->find();

							$one = array();
							if(!empty($TuiWebInfo)){
								$one['ProjectID'] = $TuiWebInfo['ProjectID'];
								$one['ProjectInfo'] = FindInfo(
									array(
										'database' =>'j_project',
										'where'    =>" and id = '".$TuiWebInfo['ProjectID']."'",
										'field'    =>'ProjectName',
										'name'     =>'ID = '.$TuiWebInfo['ProjectID'].'该项目'
									),'json');

								$one['PlatformID'] = $TuiWebInfo['PlatformID'];
								$one['PlatformInfo'] = FindInfo(
									array(
										'database' =>'j_platform',
										'where'    =>" and id = '".$TuiWebInfo['PlatformID']."'",
										'field'    =>'PlatformName',
										'name'     =>'ID = '.$TuiWebInfo['PlatformID'].'该平台'
									),'json');

								$one['HuID'] = $TuiWebInfo['HuID'];
								$one['HuInfo'] = FindInfo(
									array(
										'database' =>'j_hu',
										'where'    =>" and id = '".$TuiWebInfo['HuID']."'",
										'field'    =>'HuName,PlatformID',
										'name'     =>'ID = '.$TuiWebInfo['HuID'].'该户'
									),'json');

								$one['ZhaoWebID'] = $TuiWebInfo['ZhaoWebID'];
								$one['ZhaoWebInfo'] = FindInfo(
									array(
										'database' =>'j_zhaoweb',
										'where'    =>" and id = '".$TuiWebInfo['ZhaoWebID']."'",
										'field'    =>'ZhaoWebName,ProjectID',
										'name'     =>'ID = '.$TuiWebInfo['ZhaoWebID'].'该户着陆页'
									),'json');

								$one['WechatID'] = $TuiWebInfo['WechatID'];
								$one['WechatInfo'] = FindInfo(
									array(
										'database' =>'j_wechat',
										'where'    =>" and WechatID = '".$TuiWebInfo['WechatID']."'",
										'field'    =>'WechatName,ProjectID',
										'name'     =>'WechatID = '.$TuiWebInfo['WechatID'].'该微信号'
									),'json');


								$one['TuiWebInfo'] = json_encode($TuiWebInfo, JSON_UNESCAPED_UNICODE);

								$add[] = array_merge($one,$v,$other);
							}else{
								alert("录入失败，未找到该推广信息，推广ID=".$v['TuiWebID']);
							}
						}
					}

					$repeat = array();
					foreach($add as $k=>$v){
						$count = m('j_recordxiao')->where("TuiWebID = '".$v['TuiWebID']."' and ((StartTime >= '".$v['StartTime']."' and EndTime <= '".$v['EndTime']."') or (StartTime <= '".$v['StartTime']."' and EndTime >= '".$v['EndTime']."') or (EndTime >= '".$v['EndTime']."' and EndTime <= '".$v['EndTime']."'))")->field('addtime')->find();

						if(!empty($count)){
							$repeat[] = array('msg'=>date("Y-m-d H:i:s",$count['addtime']).' 已经记录', 'TuiWebID'=>$v['TuiWebID']);
						}
					}

					if(empty($repeat)){
						if(m("j_recordxiao")->addAll($add)){
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
						$add['Bi'] = I('post.Bi',0);
						$add['Click'] = I('post.Click',0);
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