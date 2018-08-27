<?php
class LHeXinAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LHeXin',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		/*所有项目*/
		$AllProject = m('j_project')->where("status='0'")->order('rank desc,id asc')->field('id,ProjectName')->select();
		$AllTjProject = m()->query("
				select C.* from (
					select A.ProjectInfo,B.* from wp_j_recordxiao as A,(
						select ProjectID from wp_l_tj where 1=1 ".(!empty($AllProject) ? " and ProjectID not in ('".implode("','", array_column($AllProject, 'id'))."')" : "")." group by ProjectID
					) as B where B.ProjectID = A.ProjectID order by id desc
				) as C group by C.ProjectID
			");
		if(!empty($AllTjProject)){
			foreach($AllTjProject as $k=>$v){
				$info = json_decode($v['ProjectInfo'], true);
				if(!empty($info) && isset($info['ProjectName'])){
					$AllProject[] = array('id'=>$v['ProjectID'], 'ProjectName'=>$info['ProjectName']);
				}
			}
		}
		$this->assign('AllProject', $AllProject);

		/*所有户*/
		$AllHu = m('j_hu')->where("status='0'")->order('rank desc,id asc')->field('id,HuName')->select();
		$AllTjHu = m()->query("
				select C.* from (
					select A.HuInfo,B.* from wp_j_recordxiao as A,(
						select HuID from wp_l_tj where 1=1 ".(!empty($AllHu) ? " and HuID not in ('".implode("','", array_column($AllHu, 'id'))."')" : "")." group by HuID
					) as B where B.HuID = A.HuID order by id desc
				) as C group by C.HuID
			");
		if(!empty($AllTjHu)){
			foreach($AllTjHu as $k=>$v){
				$info = json_decode($v['HuInfo'], true);
				if(!empty($info) && isset($info['HuName'])){
					$AllHu[] = array('id'=>$v['HuID'], 'HuName'=>$info['HuName']);
				}
			}
		}
		$this->assign('AllHu', $AllHu);

		/*所有着陆页*/
		$AllZhaoWeb = m('j_zhaoweb')->where("status='0'")->order('rank desc,id asc')->field('id,ZhaoWebName')->select();
		$AllTjZhaoWeb = m()->query("
				select C.* from (
					select A.ZhaoWebInfo,B.* from wp_j_recordxiao as A,(
						select ZhaoWebID from wp_l_tj where 1=1 ".(!empty($AllZhaoWeb) ? " and ZhaoWebID not in ('".implode("','", array_column($AllZhaoWeb, 'id'))."')" : "")." group by ZhaoWebID
					) as B where B.ZhaoWebID = A.ZhaoWebID order by id desc
				) as C group by C.ZhaoWebID
			");
		if(!empty($AllTjZhaoWeb)){
			foreach($AllTjZhaoWeb as $k=>$v){
				$info = json_decode($v['ZhaoWebInfo'], true);
				if(!empty($info) && isset($info['ZhaoWebName'])){
					$AllZhaoWeb[] = array('id'=>$v['ZhaoWebID'], 'ZhaoWebName'=>$info['ZhaoWebName']);
				}
			}
		}
		$this->assign('AllZhaoWeb', $AllZhaoWeb);

		$this->display();
	}

	public function KeyWords(){
		$para = '';

		$Search_arr = array(
			array("ProjectID = '", 'ProjectID', "'"),
			array("HuID in ('", 'HuID', "')"),
			array("ZhaoWebID in ('", 'ZhaoWebID', "')"),
			array("addtime >= '", 'StartTime', "'"),
			array("addtime <= '", 'EndTime', "'")
		);
		$para .= SearchPara($Search_arr);


		/*关键字*/
		$KeyWords = I('post.KeyWords','');
		if(!empty($KeyWords)){
			$para .= " and KeyWordsID in (select id from wp_l_keywords where KeyWords in ('".implode("','", $KeyWords)."') and type = '0')";
		}

		/*创意*/
		$ChuangYi = I('post.ChuangYi','');
		if(!empty($ChuangYi)){
			$para .= " and NowUrlID in (select id from wp_l_nowurl where url regexp '".implode("|", $ChuangYi)."')";
		}



		$page = I("post.page",1);
		$rows = I("post.rows",10);
		$sortOrder = I('post.sortOrder','desc');

		$sort = I("post.sort",'zfwl');

		$LR = 'left';
		$LR2 = 'left';
		$zfwl = array('sort'=>'','rows'=>'');
		$zfzl = array('sort'=>'','rows'=>'');
		$LookTimeAVG = array('sort'=>'','rows'=>'');
		if($sort == 'zfwl'){
			$zfwl['sort'] = " order by zfwl ".$sortOrder;
			$zfwl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
		}else if($sort == 'zfzl'){
			$zfzl['sort'] = " order by zfzl ".$sortOrder;
			$zfzl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR = 'right';
		}else if($sort == 'LookTimeAVG'){
			$LookTimeAVG['sort'] = " order by LookTimeAVG ".$sortOrder;
			$LookTimeAVG['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR2 = 'right';
		}
			
		$list = m()->query("
			select K.KeyWords,T.* from (
				select T.*,C.zfzl from (
					
					select T.*,L.LookTimeAVG from (

						select KeyWordsID,count(KeyWordsID) as zfwl from wp_l_tj where 1=1 ".$para." group by KeyWordsID".$zfwl['sort'].$zfwl['rows']."

					) as T ".$LR2." join (

						select AVG(LookTime) as LookTimeAVG,KeyWordsID from wp_l_tj where 1=1 ".$para." and LookTime != 0 group by KeyWordsID".$LookTimeAVG['sort'].$LookTimeAVG['rows']."

					) as L on T.KeyWordsID = L.KeyWordsID

				) as T ".$LR." join (

					select KeyWordsID,count(KeyWordsID) as zfzl from wp_l_tj where 1=1 ".$para." and copy = 1 group by KeyWordsID".$zfzl['sort'].$zfzl['rows']."

				) as C on T.KeyWordsID = C.KeyWordsID
			) as T left join wp_l_keywords K on T.KeyWordsID = K.id
		");
		
		$count = m()->query("
			select count(KeyWordsID) as zfwl from (
				select KeyWordsID from wp_l_tj where 1=1 ".$para . ($sort == 'zfzl' ? " and copy = 1" : '')." group by KeyWordsID
			) T
		");

		if(!empty($list)){
			foreach ($list as $k => $v) {
				$CopyInfo = m('')->query("
					select C.WID,count(C.WID) as CopyNum from (
						select C.* from (
							select WID,TjID from wp_l_copy order by addtime desc
						) as C group by C.TjID
					) as C where C.TjID in (
						select id from wp_l_tj where 1=1 ".$para." and KeyWordsID = '".$v['KeyWordsID']."' and copy = 1
					) group by C.WID order by CopyNum desc
				");

				$list[$k]['CopyInfo'] = $CopyInfo;
			}
		}

		// print_r ($list);
		// exit();

		//alert($para);

		echo json_encode(array(
			'rows'=>$list,
			'total'=>isset($count[0]['zfwl']) ? $count[0]['zfwl'] : 0
		));
	}

	public function TuiWeb(){
		$para = '';

		$Search_arr = array(
			array("ProjectID = '", 'ProjectID', "'"),
			array("HuID in ('", 'HuID', "')"),
			array("ZhaoWebID in ('", 'ZhaoWebID', "')"),
			array("addtime >= '", 'StartTime', "'"),
			array("addtime <= '", 'EndTime', "'")
		);
		$para .= SearchPara($Search_arr);


		/*关键字*/
		$KeyWords = I('post.KeyWords','');
		if(!empty($KeyWords)){
			$para .= " and KeyWordsID in (select id from wp_l_keywords where KeyWords in ('".implode("','", $KeyWords)."') and type = '0')";
		}

		/*创意*/
		$ChuangYi = I('post.ChuangYi','');
		if(!empty($ChuangYi)){
			$para .= " and NowUrlID in (select id from wp_l_nowurl where url regexp '".implode("|", $ChuangYi)."')";
		}



		$page = I("post.page",1);
		$rows = I("post.rows",10);
		$sortOrder = I('post.sortOrder','desc');

		$sort = I("post.sort",'zfwl');

		$LR = 'left';
		$LR2 = 'left';
		$zfwl = array('sort'=>'','rows'=>'');
		$zfzl = array('sort'=>'','rows'=>'');
		$LookTimeAVG = array('sort'=>'','rows'=>'');
		if($sort == 'zfwl'){
			$zfwl['sort'] = " order by zfwl ".$sortOrder;
			$zfwl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
		}else if($sort == 'zfzl'){
			$zfzl['sort'] = " order by zfzl ".$sortOrder;
			$zfzl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR = 'right';
		}else if($sort == 'LookTimeAVG'){
			$LookTimeAVG['sort'] = " order by LookTimeAVG ".$sortOrder;
			$LookTimeAVG['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR2 = 'right';
		}
			
		$list = m()->query("
			select T.*,C.zfzl from (
				
				select T.*,L.LookTimeAVG from (

					select TuiWebID,count(TuiWebID) as zfwl from wp_l_tj where 1=1 ".$para." group by TuiWebID".$zfwl['sort'].$zfwl['rows']."

				) as T ".$LR2." join (

					select AVG(LookTime) as LookTimeAVG,TuiWebID from wp_l_tj where 1=1 ".$para." and LookTime != 0 group by TuiWebID".$LookTimeAVG['sort'].$LookTimeAVG['rows']."

				) as L on T.TuiWebID = L.TuiWebID

			) as T ".$LR." join (

				select TuiWebID,count(TuiWebID) as zfzl from wp_l_tj where 1=1 ".$para." and copy = 1 group by TuiWebID".$zfzl['sort'].$zfzl['rows']."

			) as C on T.TuiWebID = C.TuiWebID
		");
		
		$count = m()->query("
			select count(TuiWebID) as zfwl from (
				select TuiWebID from wp_l_tj where 1=1 ".$para . ($sort == 'zfzl' ? " and copy = 1" : '')." group by TuiWebID
			) T
		");

		if(!empty($list)){
			foreach ($list as $k => $v) {
				$CopyInfo = m('')->query("
					select C.WID,count(C.WID) as CopyNum from (
						select C.* from (
							select WID,TjID from wp_l_copy order by addtime desc
						) as C group by C.TjID
					) as C where C.TjID in (
						select id from wp_l_tj where 1=1 ".$para." and TuiWebID = '".$v['TuiWebID']."' and copy = 1
					) group by C.WID order by CopyNum desc
				");

				$TjInfo = m('l_tj')->where("TuiWebID = '".$v['TuiWebID']."'")->order('addtime desc')->field('addtime')->find();
				$info = m('j_recordxiao')->where("TuiWebID = '".$v['TuiWebID']."' and StartTime <= '".$TjInfo['addtime']."'")->field('ProjectInfo,PlatformInfo,HuInfo,ZhaoWebInfo,WechatInfo,TuiWebInfo')->order('StartTime desc')->find();

				$info['CopyInfo'] = $CopyInfo;

				$list[$k] = array_merge($v, $info);
			}
		}

		/*新增咨询*/
		$WechatNew = array();
		$AllWechatID = m('l_tj')->where("1=1 " . $para . ($sort == 'zfzl' ? " and copy = 1" : ''))->group("WechatID")->field("WechatID")->select();
		if(!empty($AllWechatID)){
			$AllWechatID = array_column($AllWechatID, 'WechatID');
			$WechatNew = DataApi(array(
				'type'=>'Sql',
				'data'=>array(
					'database'=>'record_wechat_new',
					'para'=>(SearchPara(array(array("RecordTime >= '", 'StartTime', "'"),array("RecordTime <= '", 'EndTime', "'"))))." and WechatID in ('".implode("','", $AllWechatID)."')",
					'field'=>'WechatID,RecordTime,NewNum,ConsultNum',
					'order'=>'RecordTime desc'
				)));
		

			if(!empty($WechatNew)){
				$AllTuiWebID = array_column($list, 'TuiWebID');

				$AllTuiData = m('j_recordxiao')->where("1=1 ".(SearchPara(array(array("StartTime >= '", 'StartTime', "'"),array("StartTime < '", 'EndTime', "'")))) . " and TuiWebID in ('".implode("','", $AllTuiWebID)."')")->field("TuiWebID,WechatID,StartTime,EndTime")->select();

				if(!empty($AllTuiData)){
					foreach($WechatNew as $k=>$v){

						foreach($AllTuiData as $k1=>$v1) {

							if($v['WechatID'] == $v1['WechatID'] && $v1['StartTime'] <= $v['RecordTime'] && $v1['EndTime'] >= $v['RecordTime']){

								foreach($list as $k2=>$v2){
									if($v2['TuiWebID'] == $v1['TuiWebID']){
										$list[$k2]['xzl'] = isset($list[$k2]['xzl']) ? $list[$k2]['xzl'] + $v['NewNum'] : $v['NewNum'];
										$list[$k2]['zxl'] = isset($list[$k2]['zxl']) ? $list[$k2]['zxl'] + $v['ConsultNum'] : $v['ConsultNum'];
										break;
									}
								}

								break;
							}
						}

					}
				}
			}

		}

		// print_r ($list);
		// exit();


		//alert($para);

		echo json_encode(array(
			'rows'=>$list,
			'total'=>isset($count[0]['zfwl']) ? $count[0]['zfwl'] : 0
		));
	}

	public function TimeDian(){
		$para = '';

		$Search_arr = array(
			array("ProjectID = '", 'ProjectID', "'"),
			array("HuID in ('", 'HuID', "')"),
			array("ZhaoWebID in ('", 'ZhaoWebID', "')"),
			array("addtime >= '", 'StartTime', "'"),
			array("addtime <= '", 'EndTime', "'")
		);
		$para .= SearchPara($Search_arr);


		/*关键字*/
		$KeyWords = I('post.KeyWords','');
		if(!empty($KeyWords)){
			$para .= " and KeyWordsID in (select id from wp_l_keywords where KeyWords in ('".implode("','", $KeyWords)."') and type = '0')";
		}

		/*创意*/
		$ChuangYi = I('post.ChuangYi','');
		if(!empty($ChuangYi)){
			$para .= " and NowUrlID in (select id from wp_l_nowurl where url regexp '".implode("|", $ChuangYi)."')";
		}



		$page = I("post.page",1);
		$rows = I("post.rows",10);
		$sortOrder = I('post.sortOrder','desc');

		$sort = I("post.sort",'zfwl');

		$LR = 'left';
		$LR2 = 'left';
		$zfwl = array('sort'=>'','rows'=>'');
		$zfzl = array('sort'=>'','rows'=>'');
		$LookTimeAVG = array('sort'=>'','rows'=>'');
		if($sort == 'zfwl'){
			$zfwl['sort'] = " order by zfwl ".$sortOrder;
			$zfwl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
		}else if($sort == 'zfzl'){
			$zfzl['sort'] = " order by zfzl ".$sortOrder;
			$zfzl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR = 'right';
		}else if($sort == 'LookTimeAVG'){
			$LookTimeAVG['sort'] = " order by LookTimeAVG ".$sortOrder;
			$LookTimeAVG['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR2 = 'right';
		}
			
		$list = m()->query("
			select T.*,C.zfzl from (
				
				select T.*,L.LookTimeAVG from (

					select TimeDian,count(TimeDian) as zfwl from wp_l_tj where 1=1 ".$para." group by TimeDian".$zfwl['sort'].$zfwl['rows']."

				) as T ".$LR2." join (

					select AVG(LookTime) as LookTimeAVG,TimeDian from wp_l_tj where 1=1 ".$para." and LookTime != 0 group by TimeDian".$LookTimeAVG['sort'].$LookTimeAVG['rows']."

				) as L on T.TimeDian = L.TimeDian

			) as T ".$LR." join (

				select TimeDian,count(TimeDian) as zfzl from wp_l_tj where 1=1 ".$para." and copy = 1 group by TimeDian".$zfzl['sort'].$zfzl['rows']."

			) as C on T.TimeDian = C.TimeDian
		");
		
		$count = m()->query("
			select count(TimeDian) as zfwl from (
				select TimeDian from wp_l_tj where 1=1 ".$para . ($sort == 'zfzl' ? " and copy = 1" : '')." group by TimeDian
			) T
		");


		$TimeDian = C('TimeDian');
		if(!empty($list)){
			foreach ($list as $k => $v) {
				$CopyInfo = m('')->query("
					select C.WID,count(C.WID) as CopyNum from (
						select C.* from (
							select WID,TjID from wp_l_copy order by addtime desc
						) as C group by C.TjID
					) as C where C.TjID in (
						select id from wp_l_tj where 1=1 ".$para." and TimeDian = '".$v['TimeDian']."' and copy = 1
					) group by C.WID order by CopyNum desc
				");
				
				$list[$k]['TimeDians'] = $list[$k]['TimeDian'];//后面计算新增咨询使用
				$list[$k]['TimeDian'] = $TimeDian[$v['TimeDian']][0] . " - " . $TimeDian[$v['TimeDian']][1];
				$list[$k]['CopyInfo'] = $CopyInfo;
			}
		}

		/*新增咨询*/
		$WechatNew = array();
		$AllWechatID = m('l_tj')->where("1=1 " . $para . ($sort == 'zfzl' ? " and copy = 1" : ''))->group("WechatID")->field("WechatID")->select();
		if(!empty($AllWechatID)){
			$AllWechatID = array_column($AllWechatID, 'WechatID');

			$ApiPara = SearchPara(array(array("FenTime >= '", 'StartTime', "'"),array("FenTime <= '", 'EndTime', "'")));
			$ApiPara = "(" . ($ApiPara != '' ? "(1=1 ".$ApiPara.") or " : '') . "(1=1 ".SearchPara(array(array("RTime >= '", 'StartTime', "'"),array("RTime <= '", 'EndTime', "'"))) . " and Consult = 1))";

			$WechatNew = DataApi(array(
				'type'=>'Sql',
				'data'=>array(
					'database'=>'need_list',
					'para'=> " and " . $ApiPara . " and WechatID in ('".implode("','", $AllWechatID)."')",
					'field'=>'FenTime,RTime,Consult'
				)));

			if(!empty($WechatNew)){
				foreach($WechatNew as $k=>$v){

					if($v['FenTime'] > 0){
						$RTimeDian = Date("H",$v['FenTime']);
						foreach($list as $k2=>$v2){
							if($v2['TimeDians'] == $RTimeDian){
								$list[$k2]['xzl'] = isset($list[$k2]['xzl']) ? $list[$k2]['xzl'] + 1 : 1;
							}
						}
					}

					if($v['Consult'] == '1'){
						$RTimeDian = Date("H",$v['RTime']);
						foreach($list as $k2=>$v2){
							if($v2['TimeDians'] == $RTimeDian){
								$list[$k2]['zxl'] = isset($list[$k2]['zxl']) ? $list[$k2]['zxl'] + 1 : 1;
							}
						}
					}

				}
			}
		}


		// print_r ($list);
		// exit();


		//alert($para);

		echo json_encode(array(
			'rows'=>$list,
			'total'=>isset($count[0]['zfwl']) ? $count[0]['zfwl'] : 0
		));
	}

	public function provice(){
		$para = '';

		$Search_arr = array(
			array("ProjectID = '", 'ProjectID', "'"),
			array("HuID in ('", 'HuID', "')"),
			array("ZhaoWebID in ('", 'ZhaoWebID', "')"),
			array("addtime >= '", 'StartTime', "'"),
			array("addtime <= '", 'EndTime', "'")
		);
		$para .= SearchPara($Search_arr);


		/*关键字*/
		$KeyWords = I('post.KeyWords','');
		if(!empty($KeyWords)){
			$para .= " and KeyWordsID in (select id from wp_l_keywords where KeyWords in ('".implode("','", $KeyWords)."') and type = '0')";
		}

		/*创意*/
		$ChuangYi = I('post.ChuangYi','');
		if(!empty($ChuangYi)){
			$para .= " and NowUrlID in (select id from wp_l_nowurl where url regexp '".implode("|", $ChuangYi)."')";
		}
		$para .= " and provice != 0 and city != 0";



		$page = I("post.page",1);
		$rows = I("post.rows",10);
		$sortOrder = I('post.sortOrder','desc');

		$sort = I("post.sort",'zfwl');

		$LR = 'left';
		$LR2 = 'left';
		$zfwl = array('sort'=>'','rows'=>'');
		$zfzl = array('sort'=>'','rows'=>'');
		$LookTimeAVG = array('sort'=>'','rows'=>'');
		if($sort == 'zfwl'){
			$zfwl['sort'] = " order by zfwl ".$sortOrder;
			$zfwl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
		}else if($sort == 'zfzl'){
			$zfzl['sort'] = " order by zfzl ".$sortOrder;
			$zfzl['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR = 'right';
		}else if($sort == 'LookTimeAVG'){
			$LookTimeAVG['sort'] = " order by LookTimeAVG ".$sortOrder;
			$LookTimeAVG['rows'] = " limit ".(($page - 1)*$rows).",".$rows;
			$LR2 = 'right';
		}
			
		$list = m()->query("
			select T.*,C.zfzl from (
				
				select T.*,L.LookTimeAVG from (

					select provice,count(provice) as zfwl from wp_l_tj where 1=1 ".$para." group by provice".$zfwl['sort'].$zfwl['rows']."

				) as T ".$LR2." join (

					select AVG(LookTime) as LookTimeAVG,provice from wp_l_tj where 1=1 ".$para." and LookTime != 0 group by provice".$LookTimeAVG['sort'].$LookTimeAVG['rows']."

				) as L on T.provice = L.provice

			) as T ".$LR." join (

				select provice,count(provice) as zfzl from wp_l_tj where 1=1 ".$para." and copy = 1 group by provice".$zfzl['sort'].$zfzl['rows']."

			) as C on T.provice = C.provice
		");
		
		$count = m()->query("
			select count(provice) as zfwl from (
				select provice from wp_l_tj where 1=1 ".$para . ($sort == 'zfzl' ? " and copy = 1" : '')." group by provice
			) T
		");


		$citys = C('citys');
		if(!empty($list)){
			foreach ($list as $k => $v) {
				$CopyInfo = m('')->query("
					select C.WID,count(C.WID) as CopyNum from (
						select C.* from (
							select WID,TjID from wp_l_copy order by addtime desc
						) as C group by C.TjID
					) as C where C.TjID in (
						select id from wp_l_tj where 1=1 ".$para." and provice = '".$v['provice']."' and copy = 1
					) group by C.WID order by CopyNum desc
				");
				
				$list[$k]['provice'] = $citys[$v['provice']][0];
				$list[$k]['CopyInfo'] = $CopyInfo;
			}
		}
		// print_r ($list);
		// exit();


		//alert($para);

		echo json_encode(array(
			'rows'=>$list,
			'total'=>isset($count[0]['zfwl']) ? $count[0]['zfwl'] : 0
		));
	}

}