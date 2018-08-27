<?php
class LIDZhaoAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDZhao',3);
	}

	public $AllXiao = array();
	public function _WechatTime($WechatID,$time){
		$ProjectID = false;
		if(!empty($this->AllXiao)){
			foreach($this->AllXiao as $k => $v) {
				if($v['WechatID'] == $WechatID && $v['StartTime'] <= $time && $v['EndTime'] >= $time){
					return $v['ZhaoWebID'];
				}
			}
		}
		return $ProjectID;
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$this->display("index");
	}

	public function LIDZhaoLook(){
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
				'click'=>array('name'=>'点击','width'=>'70'),
				'zf'=>array('name'=>'增粉','width'=>'60'),
				'zfl'=>array('name'=>'增粉率','width'=>'60'),
				'zx'=>array('name'=>'咨询','width'=>'60'),
				'zxl'=>array('name'=>'咨询率','width'=>'60'),
				'fl'=>array('name'=>'发量','width'=>'60'),
				'fhl'=>array('name'=>'发货率','width'=>'70'),
			);

			/*首先获得全部*/
			$AllZhaoWeb = m('j_zhaoweb')->where("status = '0'")->order('rank desc,id asc')->field('id,ZhaoWebName')->select();
			$AllZhaoWebID = !empty($AllZhaoWeb) ? array_column($AllZhaoWeb,'id') : array();
			$AllXiaoZhaoWeb = m()->query("
				select A.* from (select ZhaoWebID,ZhaoWebInfo from wp_j_recordxiao ".(!empty($AllZhaoWebID) ? "where StartTime >= '".$FirstDays."' and StartTime <= '".$LastDays."' and ZhaoWebID not in ('".implode("','", $AllZhaoWebID)."')" : "")." order by StartTime desc) as A group by A.ZhaoWebID
				");

			if(!empty($AllXiaoZhaoWeb)){
				foreach($AllXiaoZhaoWeb as $k=>$v){
					$info = json_decode($v['ZhaoWebInfo'],true);
					$AllZhaoWeb[] = array('id'=>$v['ZhaoWebID'],'ZhaoWebName'=>$info['ZhaoWebName']);
				}
			}
			$data['AllCheck'] = $AllZhaoWeb;

			/*处理已选*/
			$checked = isset($_POST['checked']) ? $_POST['checked'] : '';
			$checked_arr = array();
			if($checked!=''){
				$checked_arr = json_decode($checked, true);
			}
			$data['checked'] = $checked_arr;//已选员工

			foreach($day as $k=>$v){
				$NowDay = strtotime($v);
				$data['data'][$NowDay]['list'] = array();
				$data['data'][$NowDay]['name'] = $v;
			}

			/*查找所有消费数据*/
			$AllXiao = m('j_recordxiao')->where("StartTime >= '".$FirstDays."' and StartTime <= '".$LastDays."'".(!empty($checked_arr) ? " and ZhaoWebID in ('".implode("','", $checked_arr)."')" : ''))->field('HuID,WechatID,ZhaoWebID,StartTime,EndTime,Bi,Click')->select();
			
			foreach($AllXiao as $k=>$v){
				$NowDay = strtotime(date('Y-m-d',$v['StartTime']));

				//$Fan = m('j_recordchong')->where("HuID = '".$v['HuID']."' and Day <= '".$v['StartTime']."'")->field('Fan')->order('Day desc')->find();
				//$FanDian = !empty($Fan) ? $Fan['Fan'] : 0;

				$Key = $v['ZhaoWebID'];
				$Key = "z".$Key;
				foreach($data['field'] as $k1=>$v1){
					if($k1 == 'xf'){
						$money = round($v['Bi'] / (1 + ($FanDian / 100)));
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $money : $money;
					}elseif($k1 == 'click'){
						$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + $v['Click'] : $v['Click'];
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
					$Key = "z".$Key;
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
						'para'=>" and fhtime >= '".$FirstDays."' and fhtime <= '".$LastDays."' and SellID in ('".implode("','", $AllWechatID)."') and recycle = '0'",
						'field'=>'SellID,addtime,fhtime,cztime,money,OrderStatus',
						'order'=>'addtime desc'
					)));
			}

			foreach($WechatOrder as $k=>$v){
				$Key = $this->_WechatTime($v['SellID'],$v['addtime']);
				if($Key){
					$Key = "z".$Key;
					foreach($data['field'] as $k1=>$v1){
						if($k1 == 'fl'){
							$NowDay = strtotime(date('Y-m-d',$v['fhtime']));
							if($v['fhtime'] >= $FirstDays && $v['fhtime'] <= $LastDays){
								$data['data'][$NowDay]['list'][$Key][$k1] = isset($data['data'][$NowDay]['list'][$Key][$k1]) ? $data['data'][$NowDay]['list'][$Key][$k1] + 1 : 1;
							}
						}
					}
				}
			}


			/*所有已知*/
			$AllField = m('j_zhaoweb')->where("status = '0'".(!empty($checked_arr) ? " and id in ('".implode("','", $checked_arr)."')" : ''))->order('rank desc,id asc')->field('id,ZhaoWebName')->select();

			/*建立表格头*/
			if(!empty($AllField)){
				foreach($AllField as $k=>$v){
					$data['head']['list']["z".$v['id']] = $v['ZhaoWebName'];
				}
			}

			if(!empty($data['data'])){
				foreach($data['data'] as $k=>$v){
					if(!empty($v['list'])){
						foreach($v['list'] as $k1=>$v1){
							if(!isset($data['head']['list'][$k1])){
								$name = '未知着陆页';
								$info = m('j_recordxiao')->where("ZhaoWebID = '".(str_replace("z", "", $k1))."'")->order('StartTime desc')->field('ZhaoWebInfo')->find();
								if(isset($info['ZhaoWebInfo']) && $info['ZhaoWebInfo']!=''){
									$info = json_decode($info['ZhaoWebInfo'], true);
									if(!empty($info)){
										$name = $info['ZhaoWebName'];
									}
								}
								
								$data['head']['list'][$k1] = $name;
							}
						}
					}
				}
			}

			/*添加url*/
			$UrlArr = array();
			foreach($data['head']['list'] as $k=>$v){
				$info = m('j_recordxiao')->where("ZhaoWebID = '".(str_replace("z", "", $k))."'")->order('StartTime desc')->field('TuiWebInfo')->find();
				if(isset($info['TuiWebInfo']) && $info['TuiWebInfo']!=''){
					$info = json_decode($info['TuiWebInfo'], true);
					if(!empty($info)){
						$UrlArr[$k] = $info['TuiWebUrl'];
					}
				}
			}
			$data['UrlArr'] = $UrlArr;

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