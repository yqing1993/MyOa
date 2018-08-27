<?php
class LIDProjectAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDProject',3);
	}

	public $WechatTime = array();
	public function _WechatTime($WechatID,$time){
		$ProjectID = false;
		if(isset($this->WechatTime[$WechatID]) && !empty($this->WechatTime[$WechatID])){
			foreach($this->WechatTime[$WechatID] as $k => $v) {
				if($v['StartTime'] <= $time && $v['EndTime'] >= $time){
					return $v['ProjectID'];
				}
			}
		}

		if(!$ProjectID){
			$info = m('j_wechat_time')->where("WechatID = '".$WechatID."' and addtime <= '".$time."'")->field('ProjectID,addtime')->order('addtime desc')->find();
			$this->WechatTime[$WechatID][] = array(
				'StartTime'=>$info['addtime'],
				'EndTime'=>$time,
				'ProjectID'=>$info['ProjectID']
			);

			$ProjectID =  $info['ProjectID'];
		}
		
		return $ProjectID;
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());
		$this->display("index");
	}

	public function LIDProjectLook(){
		$operate = I('operate','');
		//$operate = 'Look';
		if($operate == 'Look'){
			
			/*全部日期*/
			$day = Day(0);
			$FirstDays = strtotime($day[0]);
			$LastDays = strtotime(end($day).' 23:59:59');

			$data = array();
			/*表格头信息*/
			$data['head']['header']['date'] = array('name'=>'日期','width'=>'150');
			/*需要展现的字段*/
			$data['field'] = array(
				'rmb'=>array('name'=>'消费','width'=>'80'),
				// 'click'=>array('name'=>'点击','width'=>'80'),
				// 'djdj'=>array('name'=>'点击单价','width'=>'80'),
				// 'zf'=>array('name'=>'增粉','width'=>'80'),
				// 'zfl'=>array('name'=>'增粉率','width'=>'80'),
				// 'xzdj'=>array('name'=>'新增单价','width'=>'80'),
				// 'zx'=>array('name'=>'咨询','width'=>'80'),
				// 'zxl'=>array('name'=>'咨询率','width'=>'80'),
				'fl'=>array('name'=>'发量','width'=>'80'),
				'ql'=>array('name'=>'签量','width'=>'80'),
				'fe'=>array('name'=>'发额','width'=>'80'),
				'qe'=>array('name'=>'签额','width'=>'80'),
				'ccl'=>array('name'=>'产出率','width'=>'80')
			);

			foreach($day as $k=>$v){
				$NowDay = strtotime($v);
				$data['data'][$NowDay]['list'] = array();
				$data['data'][$NowDay]['name'] = $v;
			}

			/*查找所有消费数据*/
			$AllXiao = m('j_recordxiao')->where("StartTime >= '".$FirstDays."' and StartTime <= '".$LastDays."'")->field('ProjectID,ProjectInfo,HuID,WechatID,WechatInfo,StartTime,EndTime,Bi,Click')->select();
			
			foreach($AllXiao as $k=>$v){
				$NowDay = strtotime(date('Y-m-d',$v['StartTime']));

				$Key = $v['ProjectID'];
				$Fan = m('j_recordchong')->where("HuID = '".$v['HuID']."' and Day <= '".$v['StartTime']."'")->field('Fan')->order('Day desc')->find();
				$FanDian = !empty($Fan) ? $Fan['Fan'] : 0;

				foreach($data['field'] as $k1=>$v1){
					if($k1 == 'rmb'){
						$money = round($v['Bi'] / (1 + ($FanDian / 100)));
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $money : $money;
					}elseif($k1 == 'click'){
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['Click'] : $v['Click'];
					}elseif($k1 == 'djdj'){
						$data['data'][$NowDay]['list'][$Key][$k1] = round(($data['data'][$NowDay]['list'][$Key]['rmb'] / $data['data'][$NowDay]['list'][$Key]['click']),2);
					}
				}
			}

			$AllXiaoWechatID = array_unique(!empty($AllXiao) ? array_column($AllXiao, 'WechatID') : array());
			$AllWechat = m('j_wechat')->where("ProjectID != '0' and WechatType = 'sq' and status = '0'")->field('WechatID')->select();
			$AllWechatID = !empty($AllWechat) ? array_column($AllWechat, 'WechatID') : array();
			$AllWechatID = array_unique(array_merge($AllWechatID, $AllXiaoWechatID));

			/*新增咨询数据*/
			// $WechatNew = array();
			// if(!empty($AllWechatID)){
			// 	$WechatNew = DataApi(array(
			// 		'type'=>'Sql',
			// 		'data'=>array(
			// 			'database'=>'record_wechat_new',
			// 			'para'=>" and RecordTime >= '".$FirstDays."' and RecordTime <= '".$LastDays."' and WechatID in ('".implode("','", $AllWechatID)."')",
			// 			'field'=>'WechatID,RecordTime,NewNum,ConsultNum',
			// 			'order'=>'RecordTime desc'
			// 		)));
			// }

			// foreach($WechatNew as $k=>$v){
			// 	$NowDay = strtotime(date('Y-m-d',$v['RecordTime']));
			// 	$Key = $this->_WechatTime($v['WechatID'],$v['RecordTime']);

			// 	if($Key){
			// 		foreach($data['field'] as $k1=>$v1){
			// 			if($k1 == 'zf'){
			// 				$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['NewNum'] : $v['NewNum'];
			// 			}elseif($k1 == 'zx'){
			// 				$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['ConsultNum'] : $v['ConsultNum'];
			// 			}
			// 		}
			// 	}
			// }

			/*发量、签量、发额、签款*/
			$WechatOrder = array();
			$AddWechat = m('j_wechat')->where("ProjectID != '0' and WechatType = 'sh' and status = '0'")->field('WechatID')->select();
			$AddWechat = !empty($AddWechat) ? array_column($AddWechat, 'WechatID') : array();
			$AllWechatID = array_merge($AllWechatID, $AddWechat);
			if(!empty($AllWechatID)){
				$WechatOrder = DataApi(array(
					'type'=>'Sql',
					'data'=>array(
						'database'=>'order',
						'para'=>" and ((cztime >= '".$FirstDays."' and cztime <= '".$LastDays."' and OrderStatus = 'yqs') or (fhtime >= '".$FirstDays."' and fhtime <= '".$LastDays."') ) and SellID in ('".implode("','", $AllWechatID)."') and recycle = '0'",
						'field'=>'SellID,addtime,fhtime,cztime,money,OrderStatus',
						'order'=>'addtime desc'
					)));
			}
			foreach($WechatOrder as $k=>$v){
				$Key = $this->_WechatTime($v['SellID'],$v['addtime']);
				if($Key){
					foreach($data['field'] as $k1=>$v1){
						if($k1 == 'fl' || $k1 == 'fe'){
							$NowDay = strtotime(date('Y-m-d',$v['fhtime']));
							if($v['fhtime'] >= $FirstDays && $v['fhtime'] <= $LastDays){
								if($k1 == 'fl'){
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + 1 : 1;
								}else{
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['money'] : $v['money'];
								}
							}
						}elseif($k1 == 'ql' || $k1 == 'qe'){
							$NowDay = strtotime(date('Y-m-d',$v['cztime']));
							if($v['cztime'] >= $FirstDays && $v['cztime'] <= $LastDays && $v['OrderStatus'] == 'yqs'){
								if($k1 == 'ql'){
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + 1 : 1;
								}else{
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['money'] : $v['money'];
								}
							}
						}
					}
				}
			}


			/*所有已知*/
			$AllField = m('j_project')->where("status = '0'")->order('rank desc')->field('id,ProjectName')->select();

			/*建立表格头*/
			if(!empty($AllField)){
				foreach($AllField as $k=>$v){
					$data['head']['list'][$v['id']] = $v['ProjectName'];
				}
			}

			if(!empty($data['data'])){
				foreach($data['data'] as $k=>$v){
					if(!empty($v['list'])){
						foreach($v['list'] as $k1=>$v1){
							if(!isset($data['head']['list'][$k1])){

								$info = m('j_wechat_time')->where("ProjectID = '".$k1."'")->order('addtime desc')->field('ProjectName')->find();
								$data['head']['list'][$k1] = (isset($info['ProjectName']) && $info['ProjectName']!='') ? $info['ProjectName'] : '未知项目';
							}
						}
					}
				}
			}


			/*日期首末*/
			$data['date'] = array($day[0],end($day));

			/*行汇总*/
			$data['RowSum'] = 0;
			if(isset($data['head']['list']) && count($data['head']['list'])>1){
				$data['RowSum'] = 1;
			}
			
			/*列汇总*/
			$data['ColSum'] = 1;
			
			$data['error'] = 0;
			echo json_encode($data);
			//print_r($data);
			exit();
		}else{
			alert('不知道你要干啥');
		}
	}
}