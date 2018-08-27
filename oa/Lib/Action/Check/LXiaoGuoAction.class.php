<?php
class LXiaoGuoAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LXiaoGuo',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		$this->display();
	}

	public function LXiaoGuoPage(){

		/*全部日期*/
		$day = Day();
		$FirstDays = strtotime($day[0]);
		$LastDays = strtotime(end($day).' 23:59:59');

		$data = array();
		foreach($day as $k=>$v){
			$NowFirstDays = strtotime($v);
			$NowLastDays = strtotime($v.' 23:59:59');

			$zfwl = m('l_tj')->where("addtime >= '".$NowFirstDays."' and addtime <= '".$NowLastDays."'")->count();

			$zfzl = m()->query("
				select count(*) as count from (
					select TjID from wp_l_copy where addtime >= '".$NowFirstDays."' and addtime <= '".$NowLastDays."' group by TjID
				) as C
			");

			$data[strtotime($v)] = array(
				'day'=>$v,
				'zfwl'=>$zfwl,
				'zfzl'=>isset($zfzl[0]['count']) ? $zfzl[0]['count'] : 0,
			);
		}

		/*新增咨询数据*/
		$AllWechat = m('l_tj')->where("addtime >= '".$FirstDays."' and addtime <= '".$LastDays."'")->group('WechatID')->field('WechatID')->select();
		$AllWechatID = !empty($AllWechat) ? array_column($AllWechat, 'WechatID') : array();
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

		foreach($WechatNew as $k=>$v){
			$NowDay = strtotime(date('Y-m-d',$v['RecordTime']));

			$data[$NowDay]['xz'] = isset($data[$NowDay]['xz']) ? $data[$NowDay]['xz'] + $v['NewNum'] : $v['NewNum'];
			$data[$NowDay]['zx'] = isset($data[$NowDay]['zx']) ? $data[$NowDay]['zx'] + $v['ConsultNum'] : $v['ConsultNum'];
		}

		echo json_encode(array(
			'date'=>array($day[0],end($day)),
			'rows'=>array_values($data)
		));
	}

	public function LXiaoGuoKeyWordsTop(){

		/*全部日期*/
		$day = Day();
		$FirstDays = strtotime($day[0]);
		$LastDays = strtotime(end($day).' 23:59:59');


			
		$KeyWords = m()->query("
			select K.KeyWords,T.* from (
				select KeyWordsID,count(KeyWordsID) as zfzl from wp_l_tj where addtime >= '".$FirstDays."' and addtime <= '".$LastDays."' and copy = '1' group by KeyWordsID order by zfzl desc limit 50
			) AS T left join wp_l_keywords K on T.KeyWordsID = K.id
		");

		if(!empty($KeyWords)){
			foreach($KeyWords as $k=>$v){
				$count = m('l_tj')->where("addtime >= '".$FirstDays."' and addtime <= '".$LastDays."' and KeyWordsID = '".$v['KeyWordsID']."'")->count();
				$KeyWords[$k]['zfwl'] = $count;
			}
		}

		// print_r ($KeyWords);
		// exit();
		
		echo json_encode(array(
			'date'=>array($day[0],end($day)),
			'rows'=>array_values($KeyWords)
		));
	}

	public function LXiaoGuoTuiWebTop(){

		/*全部日期*/
		$day = Day();
		$FirstDays = strtotime($day[0]);
		$LastDays = strtotime(end($day).' 23:59:59');

			
		$TuiWeb = m()->query("
			select T.*,count(*) as zfzl from (
				select TuiWebID,addtime from wp_l_tj where addtime >= '".$FirstDays."' and addtime <= '".$LastDays."' and copy = '1' order by addtime desc
			) as T group by T.TuiWebID order by zfzl desc limit 10
		");

		if(!empty($TuiWeb)){
			foreach($TuiWeb as $k=>$v){
				$count = m('l_tj')->where("addtime >= '".$FirstDays."' and addtime <= '".$LastDays."' and TuiWebID = '".$v['TuiWebID']."'")->count();

				$info = m('j_recordxiao')->where("TuiWebID = '".$v['TuiWebID']."' and StartTime <= '".$v['addtime']."'")->field('ProjectInfo,PlatformInfo,HuInfo,ZhaoWebInfo,WechatInfo,TuiWebInfo')->order('StartTime desc')->find();

				$info['zfwl'] = $count;
				
				$TuiWeb[$k] = array_merge($v,$info);
			}
		}
		// print_r ($TuiWeb);
		// exit();

		
		echo json_encode(array(
			'date'=>array($day[0],end($day)),
			'rows'=>array_values($TuiWeb)
		));
	}

}