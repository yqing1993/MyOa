<?php
class LIDHuAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDHu',3);
	}

	public $AllXiao = array();
	public function _WechatTime($WechatID,$time){
		$ProjectID = false;
		if(!empty($this->AllXiao)){
			foreach($this->AllXiao as $k => $v) {
				if($v['WechatID'] == $WechatID && $v['StartTime'] <= $time && $v['EndTime'] >= $time){
					return $v['HuID'];
				}
			}
		}
		return $ProjectID;
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$this->display("index");
	}

	public function LIDHuLook(){
		$operate = I('operate','');
		$operate = 'Look';
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
				'ys'=>array('name'=>'预算','width'=>'60'),
				'xf'=>array('name'=>'消费','width'=>'60'),
				// 'sy'=>array('name'=>'剩余','width'=>'80'),
				// 'cz'=>array('name'=>'充值','width'=>'80'),
				'fd'=>array('name'=>'返点','width'=>'60'),
				'click'=>array('name'=>'点击','width'=>'60'),
				'djdj'=>array('name'=>'点击单价','width'=>'70'),
				'zf'=>array('name'=>'增粉','width'=>'60'),
				'xzdj'=>array('name'=>'增粉单价','width'=>'70'),
				'zfl'=>array('name'=>'增粉率','width'=>'60'),
				'zx'=>array('name'=>'咨询','width'=>'60'),
				'zxl'=>array('name'=>'咨询率','width'=>'60'),
				'fl'=>array('name'=>'发量','width'=>'60'),
				'ql'=>array('name'=>'签量','width'=>'60'),
				'fe'=>array('name'=>'发额','width'=>'60'),
				'qe'=>array('name'=>'签额','width'=>'60')
			);

			/*首先获得全部*/
			$AllProject = m('j_project')->where("status = '0'")->order('rank desc,id asc')->field('id,ProjectName')->select();
			$AllProjectID = !empty($AllProject) ? array_column($AllProject,'id') : array();
			$AllXiaoProject = m()->query("
				select A.* from (select ProjectID,ProjectInfo from wp_j_recordxiao ".(!empty($AllProjectID) ? "where StartTime >= '".$FirstDays."' and StartTime <= '".$LastDays."' and ProjectID not in ('".implode("','", $AllProjectID)."')" : "")." order by StartTime desc) as A group by A.ProjectID
				");

			if(!empty($AllXiaoProject)){
				foreach($AllXiaoProject as $k=>$v){
					$info = json_decode($v['ProjectInfo'],true);
					$AllProject[] = array('id'=>$v['ProjectID'],'ProjectName'=>$info['ProjectName']);
				}
			}
			$data['AllCheck'] = $AllProject;

			/*处理已选*/
			$checked = isset($_POST['checked']) ? $_POST['checked'] : '';
			$checked_arr = array();
			if($checked!=''){
				$checked_arr = json_decode($checked, true);
			}
			if(empty($checked_arr)){
				$checked_arr[] = $data['AllCheck'][0]['id'];
			}

			$data['checked'] = $checked_arr;//已选员工

			foreach($day as $k=>$v){
				$NowDay = strtotime($v);
				$data['data'][$NowDay]['list'] = array();
				$data['data'][$NowDay]['name'] = $v;
			}

			/*查找所有消费数据*/
			$AllXiao = m('j_recordxiao')->where("StartTime >= '".$FirstDays."' and StartTime <= '".$LastDays."'".(!empty($checked_arr) ? " and ProjectID in ('".implode("','", $checked_arr)."')" : ''))->field('ProjectID,HuID,WechatID,StartTime,EndTime,Bi,Click')->select();
			
			foreach($AllXiao as $k=>$v){
				$NowDay = strtotime(date('Y-m-d',$v['StartTime']));

				$Key = $v['HuID'];
				$Key = "h".$Key;
				$Fan = m('j_recordchong')->where("HuID = '".$v['HuID']."' and Day <= '".$v['StartTime']."'")->field('Fan')->order('Day desc')->find();
				$FanDian = !empty($Fan) ? $Fan['Fan'] : 0;

				foreach($data['field'] as $k1=>$v1){
					if($k1 == 'xf'){
						$money = round($v['Bi'] / (1 + ($FanDian / 100)));
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $money : $money;
					}elseif($k1 == 'fd'){
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] : $FanDian;
					}elseif($k1 == 'click'){
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['Click'] : $v['Click'];
					}
				}
			}

			/*预算*/
			foreach($data['data'] as $k=>$v){
				foreach($v['list'] as $k1=>$v1){
					$info = m('j_recordyuhu')->where("HuID = '".(str_replace("h", "", $k1))."' and Day <= '".$k."'")->order('Day desc')->field('Money')->find();
					if(!empty($info)){
						$data['data'][$k]['list'][$k1]['ys'] = isset($data['data'][$k]['list'][$k1]['ys']) ? $data['data'][$k]['list'][$k1]['ys'] : round($info['Money']);
					}
				}
			}

			$AllWechatID = array_unique(!empty($AllXiao) ? array_column($AllXiao, 'WechatID') : array());

			/*新增咨询数据*/
			$WechatNew = array();
			if(!empty($AllWechatID)){
				$WechatNew = DataApi(array(
					'type'=>'Sql',
					'data'=>array(
						'database'=>'record_wechat_new',
						'para'=>" and RecordTime >= '".$FirstDays."' and RecordTime <= '".$LastDays."' and WechatID in ('".implode("','", $AllWechatID)."')",
						'field'=>'WechatID,RecordTime,NewNum,ConsultNum',
						'order'=>'RecordTime desc'
					)));
			}

			$this->AllXiao = $AllXiao;
			foreach($WechatNew as $k=>$v){
				$NowDay = strtotime(date('Y-m-d',$v['RecordTime']));
				$Key = $this->_WechatTime($v['WechatID'],$v['RecordTime']);

				if($Key){
					$Key = "h".$Key;
					foreach($data['field'] as $k1=>$v1){
						if($k1 == 'zf'){
							$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['NewNum'] : $v['NewNum'];
						}elseif($k1 == 'zx'){
							$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['ConsultNum'] : $v['ConsultNum'];
						}
					}
				}
			}


			/*发量、签量、发额、签款*/
			$WechatOrder = array();
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
					$Key = "h".$Key;
					foreach($data['field'] as $k1=>$v1){
						if($k1 == 'fl' || $k1 == 'fe'){
							$NowDay = strtotime(date('Y-m-d',$v['fhtime']));
							if($v['fhtime'] >= $FirstDays && $v['fhtime'] <= $LastDays){
								if($k1 == 'fl'){
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + 1 : 1;
								}else{
									$data['data'][$NowDay]['list'][$Key][$k1] = round(isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['money'] : $v['money']);
								}
							}
						}elseif($k1 == 'ql' || $k1 == 'qe'){
							$NowDay = strtotime(date('Y-m-d',$v['cztime']));
							if($v['cztime'] >= $FirstDays && $v['cztime'] <= $LastDays && $v['OrderStatus'] == 'yqs'){
								if($k1 == 'ql'){
									$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + 1 : 1;
								}else{
									$data['data'][$NowDay]['list'][$Key][$k1] = round(isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['money'] : $v['money']);
								}
							}
						}
					}
				}
			}


			/*所有已知*/
			$AllField = m('j_hu')->where("status = '0'")->order('rank desc,id asc')->field('id,HuName')->select();

			/*建立表格头*/
			if(!empty($AllField)){
				foreach($AllField as $k=>$v){
					$data['head']['list']["h".$v['id']] = $v['HuName'];
				}
			}

			if(!empty($data['data'])){
				foreach($data['data'] as $k=>$v){
					if(!empty($v['list'])){
						foreach($v['list'] as $k1=>$v1){
							if(!isset($data['head']['list'][$k1])){
								$name = '未知户';
								$info = m('j_recordxiao')->where("HuID = '".(str_replace("h", "", $k1))."'")->order('StartTime desc')->field('HuInfo')->find();
								if(isset($info['HuInfo']) && $info['HuInfo']!=''){
									$info = json_decode($info['HuInfo'], true);
									if(!empty($info)){
										$name = $info['HuName'];
									}
								}
								
								$data['head']['list'][$k1] = $name;
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