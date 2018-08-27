<?php
class TjVisitLookAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('TjVisitLook',3);
	}

	function search(){
		$para = '';
		$platformID = I('post.platformID','');
		$para .= $platformID != '' ? " and platformID = '".$platformID."'" : '';

		$StartTime = I('post.StartTime','');
		$EndTime = I('post.EndTime','');
		$para .= $StartTime != '' ? " and addtime >= ".$StartTime : '';
		$para .= $EndTime != '' ? " and addtime <= ".$EndTime : '';

		return $para;
	}

	public function index(){
		$AllPlatform = C('platform');
		$this->assign('AllPlatform',$AllPlatform);

		$citys = C('citys');
		$this->assign('citys',$citys);

		$this->display();
	}

	public function KeyWordsPage(){
		$TopKeyWords = m('list_keywords')->where("type = '1'")->field("id")->select();
		$TopKeyWords = array_column($TopKeyWords,'id');
		$para = !empty($TopKeyWords) ? "and KeyWordsID not in ('".implode("','",$TopKeyWords)."')" : '';

		$para .= $this->search();

		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
		$data['list'] = m('list_visit')->where(" 1=1 ".$para)->group('KeyWordsID')->field('KeyWordsID,count(KeyWordsID) as num')->order('num desc')->page($p.','.$limit)->select();

		import("ORG.Util.Page");
		$count = m('list_visit')->query("select count(*) from (select KeyWordsID from `wp_list_visit` where 1=1 ".$para." group by KeyWordsID) as A");
		$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		if(!empty($data['list'])){
			$AllKeyWordsID = array_column($data['list'],'KeyWordsID');
			$AllKeyWords = m('list_keywords')->where("id in ('".implode("','",$AllKeyWordsID)."')")->field('id,KeyWords')->select();
			$AllKeyWords = array_column($AllKeyWords,'KeyWords','id');
			foreach($data['list'] as $k=>$v){
				$data['list'][$k]['KeyWords'] = (isset($AllKeyWords[$v['KeyWordsID']]) && $AllKeyWords[$v['KeyWordsID']] != '') ? $AllKeyWords[$v['KeyWordsID']] : '未知关键字' ;
			}
		}

		echo json_encode($data);
	}

	public function WebPage(){
		$para = $this->search();
		$para = str_replace(array('platformID','addtime'),array('a.platformID','a.addtime'),$para);

		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
    	$data['list'] = m()->table('wp_list_visit a, wp_list_nowurl b')->where('a.NowUrlID = b.id'.$para)->group('b.url')->field('b.url,a.platformID,count(b.url) as num')->order('num desc')->page($p.','.$limit)->select();

		import("ORG.Util.Page");
		$count = m()->query("select count(*) from (select a.KeyWordsID from wp_list_visit AS a, wp_list_nowurl AS b where a.NowUrlID = b.id ".$para." group by b.url) as C");
		$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		// if(!empty($data['list'])){
		// 	$AllKeyWordsID = array_column($data['list'],'KeyWordsID');
		// 	$AllKeyWords = m('list_keywords')->where("id in ('".implode("','",$AllKeyWordsID)."')")->field('id,KeyWords')->select();
		// 	$AllKeyWords = array_column($AllKeyWords,'KeyWords','id');
		// 	foreach($data['list'] as $k=>$v){
		// 		$data['list'][$k]['KeyWords'] = (isset($AllKeyWords[$v['KeyWordsID']]) && $AllKeyWords[$v['KeyWordsID']] != '') ? $AllKeyWords[$v['KeyWordsID']] : '未知关键字' ;
		// 	}
		// }

		echo json_encode($data);
	}

	public function VisitTimePage(){
		$para = $this->search();
		$order = 'id desc';

		$keywords = I('post.keywords','');
		if($keywords != ''){
			$keywords = str_replace("，",",",$keywords);
			$keywords = explode(",",$keywords);
			if(!empty($keywords)){
				$keywordsID = m('list_keywords')->where("type = 0 and KeyWords in ('".implode("','",$keywords)."')")->field("id")->select();
				$keywordsID = array_column($keywordsID,'id');
				$para .= " and KeyWordsID in ('".implode("','",$keywordsID)."')";
			}
		}

		$LookTime = I('post.LookTime','');
		if($LookTime != '' && ($LookTime == 'desc' || $LookTime == 'asc')){
			$para .= " and LookTime != 0";
			$order = 'LookTime '.$LookTime;
		}

		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
		$data['list'] = m('list_visit')->where(" 1=1 ".$para)->order($order)->page($p.','.$limit)->select();

		import("ORG.Util.Page");
		$count = m('list_visit')->where(" 1=1 ".$para)->count();

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		if(!empty($data['list'])){
			$AllNowUrlID = array_column($data['list'],'NowUrlID');
			$AllNowUrl = m('list_nowurl')->where("id in ('".implode("','",$AllNowUrlID)."')")->field('id,url')->select();
			$AllNowUrl = array_column($AllNowUrl,'url','id');

			$AllKeyWordsID = array_column($data['list'],'KeyWordsID');
			$AllTopKeyWordsID = array_column($data['list'],'TopKeyWordsID');
			$AllKeyWordsID = array_merge($AllKeyWordsID,$AllTopKeyWordsID);
			$AllKeyWords = m('list_keywords')->where("id in ('".implode("','",$AllKeyWordsID)."')")->field('id,KeyWords')->select();
			$AllKeyWords = array_column($AllKeyWords,'KeyWords','id');

			$AllID = array_column($data['list'],'id');
			$AllCopy = m('list_copy')->where("VisitID in ('".implode("','",$AllID)."')")->field('VisitID,StrID,num,times')->select();
			$AllCopyData = array();
			if(!empty($AllCopy)){

				$AllCopyID = array_column($AllCopy,'StrID');
				$AllCopyStr = m('list_copystr')->where("id in ('".implode("','",$AllCopyID)."')")->field('id,str')->select();
				$AllCopyStr = array_column($AllCopyStr,'str','id');

				foreach($AllCopy as $k=>$v){
					$AllCopyData[$v['VisitID']][] = array($AllCopyStr[$v['StrID']]."（".$v['num']." - ".$v['times']."秒）");
				}
			}

			$AllFromUrlID = array_column($data['list'],'FromUrlID');
			$AllFromUrl = array();
			if(!empty($AllFromUrlID)){
				$AllFromUrl = m('list_fromurl')->where("id in ('".implode("','",$AllFromUrlID)."')")->select();
				$AllFromUrl = array_column($AllFromUrl,'url','id');
			}

			$AllsystemID = array_column($data['list'],'systemID');
			$AllBrowserID = array_column($data['list'],'BrowserID');
			$AllscreenID = array_column($data['list'],'screenID');
			$AllInfoID = array_merge($AllsystemID,$AllBrowserID,$AllscreenID);
			$AllInfo = m('list_agent')->where("id in ('".implode("','",$AllInfoID)."')")->field("id,Avalue")->select();
			$AllInfo = array_column($AllInfo,'Avalue','id');


			foreach($data['list'] as $k=>$v){
				
				$data['list'][$k]['NowUrl'] = (isset($AllNowUrl[$v['NowUrlID']]) && $AllNowUrl[$v['NowUrlID']] != '') ? $AllNowUrl[$v['NowUrlID']] : '未知网址' ;

				$data['list'][$k]['KeyWords'] = (isset($AllKeyWords[$v['KeyWordsID']]) && $AllKeyWords[$v['KeyWordsID']] != '') ? $AllKeyWords[$v['KeyWordsID']] : '未知关键字' ;

				$data['list'][$k]['TopKeyWords'] = (isset($AllKeyWords[$v['TopKeyWordsID']]) && $AllKeyWords[$v['TopKeyWordsID']] != '') ? $AllKeyWords[$v['TopKeyWordsID']] : '未知关键字' ;

				$data['list'][$k]['FromUrl'] = (isset($AllFromUrl[$v['FromUrlID']]) && $AllFromUrl[$v['FromUrlID']] != '') ? $AllFromUrl[$v['FromUrlID']] : '#' ;

				$data['list'][$k]['Copy'] = (isset($AllCopyData[$v['id']]) && $AllCopyData[$v['id']] != '') ? $AllCopyData[$v['id']] : array() ;

				$data['list'][$k]['Other'] = (isset($AllInfo[$v['systemID']]) && $AllInfo[$v['systemID']] != '') ? $AllInfo[$v['systemID']] : '未知系统' ;
				$data['list'][$k]['Other'] .= (isset($AllInfo[$v['BrowserID']]) && $AllInfo[$v['BrowserID']] != '') ? ' - '.$AllInfo[$v['BrowserID']] : ' - 未知浏览器' ;
				$data['list'][$k]['Other'] .= (isset($AllInfo[$v['screenID']]) && $AllInfo[$v['screenID']] != '') ? ' - '.$AllInfo[$v['screenID']] : ' - 未知屏幕' ;

			}
		}

		echo json_encode($data);
	}



}