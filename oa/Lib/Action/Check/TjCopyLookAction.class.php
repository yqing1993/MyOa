<?php
class TjCopyLookAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('TjCopyLook',3);
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

	public function CopyWebPage(){
		$para = $this->search();
		$para = str_replace(array('platformID','addtime'),array('a.platformID','a.addtime'),$para);


		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
		$data['list'] = m()->table('wp_list_visit a, wp_list_nowurl b')->where("a.NowUrlID = b.id and a.copy = '1'".$para)->group('b.url')->field('b.url,a.platformID,count(a.copy) as CopyNum')->order('CopyNum desc')->page($p.','.$limit)->select();

		import("ORG.Util.Page");
		$count = m()->query("select count(*) from (select a.copy from wp_list_visit AS a, wp_list_nowurl AS b where a.NowUrlID = b.id and a.copy = '1'".$para." group by b.url) as C");
		$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		if(!empty($data['list'])){
			foreach($data['list'] as $k=>$v){
				$count = array();
				$count = m()->query("select count(*) from (select b.url from wp_list_visit AS a, wp_list_nowurl AS b where a.NowUrlID = b.id and b.url = '".$v['url']."'".$para.") as C");
				$data['list'][$k]['NowUrlNum'] = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;
			}
		}


		echo json_encode($data);
	}

	public function CopyListPage(){
		$id = I('post.id');
		//$id = 'http://shoulian.rundjk.cn/bdshoulian1/';
		if($id != ''){
			$para = $this->search();
			$para = str_replace(array('platformID','addtime'),array('A.platformID','A.addtime'),$para);

			$data = array();
	    	$p = I("get.p",1);
	    	$limit = 10;

				
			$data['list'] = m()->query("
				select C.KeyWords,D.* from wp_list_keywords AS C,(
					select A.id,A.KeyWordsID from wp_list_visit AS A, wp_list_nowurl AS B where A.NowUrlID = B.id and A.copy = '1' and B.url = '".$id."'".$para." order by A.id desc limit ".(($p - 1)*$limit).",".$limit."
				) AS D where C.id in (D.KeyWordsID);
			");


			import("ORG.Util.Page");
			$count = m()->query("
				select count(*) from wp_list_visit AS A, wp_list_nowurl AS B where A.NowUrlID = B.id and A.copy = '1' and B.url = '".$id."'".$para."
			");
			$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

			$Page = new \Page($count,$limit);
			$data['page'] = $Page->show();

			if(!empty($data['list'])){
				foreach($data['list'] as $k=>$v){
					$CopyInfo = m()->query("
						select A.str,B.* from wp_list_copystr AS A,(
							select StrID,num,times,copytime from wp_list_copy where VisitID = '".$v['id']."'
						) AS B where A.id in (B.StrID)");

					$data['list'][$k]['copy'] = $CopyInfo;
				}
			}

			echo json_encode($data);

		}else{
			alert("出错，提交的ID为空");
		}
	}

	public function CopyKeyWordsPage(){
		$para = $this->search();


		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
			
		$data['list'] = m()->query("
			select C.KeyWords,D.* from wp_list_keywords AS C,(
				select KeyWordsID,count(KeyWordsID) as num from wp_list_visit where copy = '1'".$para." group by KeyWordsID order by num desc limit ".(($p - 1)*$limit).",".$limit."
			) AS D where C.id in (D.KeyWordsID);
		");


		import("ORG.Util.Page");
		$count = m()->query("
			select count(*) from wp_list_visit where copy = '1'".$para." group by KeyWordsID
		");
		$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		// if(!empty($data['list'])){
		// 	foreach($data['list'] as $k=>$v){
		// 		$CopyInfo = m()->query("
		// 			select A.str,B.* from wp_list_copystr AS A,(
		// 				select StrID,num,times,copytime from wp_list_copy where VisitID = '".$v['id']."'
		// 			) AS B where A.id in (B.StrID)");

		// 		$data['list'][$k]['copy'] = $CopyInfo;
		// 	}
		// }

		echo json_encode($data);
	}

	public function CopyTimePage(){
		$para = $this->search();
		$para = str_replace(array('platformID','addtime'),array('A.platformID','A.addtime'),$para);

		$data = array();
    	$p = I("get.p",1);
    	$limit = 30;
		$data['list'] = m()->query("
			select E.url,F.* from wp_list_nowurl AS E,(
				select C.KeyWords,D.* from wp_list_keywords AS C,(
					select A.NowUrlID,A.platformID,A.KeyWordsID,A.addtime,B.* from wp_list_visit AS A,(
						select VisitID from wp_list_copy group by VisitID order by id desc
					) AS B where A.id = B.VisitID".$para." limit ".(($p - 1)*$limit).",".$limit."
				) AS D where D.KeyWordsID = C.id
			) AS F where F.NowUrlID = E.id
		");

		import("ORG.Util.Page");
		$count = m()->query("
			select count(*) from wp_list_visit AS A,(
				select VisitID from wp_list_copy group by VisitID order by id desc
			) AS B where A.id = B.VisitID".$para."
		");
		$count = isset($count[0]['count(*)']) ? $count[0]['count(*)'] : 0;

		$Page = new \Page($count,$limit);
		$data['page'] = $Page->show();

		if(!empty($data['list'])){
			foreach($data['list'] as $k=>$v){
				$data['list'][$k]['CopyInfo'] = m()->query("
					select A.str,B.* from wp_list_copystr A,(
						select * from wp_list_copy where VisitID = '".$v['VisitID']."' order by id desc
					) AS B where A.id = B.StrID
				");
			}
		}
		echo json_encode($data);
	}



}