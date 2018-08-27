<?php
class LXiangXiAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LXiangXi',3);
	}

	public function index(){
		$this->assign('ActionName', $this->getActionName());

		/*时间点*/
		$this->assign('TimeDian', C('TimeDian'));

		/*城市*/
		$this->assign('citys', C('citys'));

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

		/*所有平台*/
		$AllPlatform = m('j_platform')->where("status='0'")->order('rank desc,id asc')->field('id,PlatformName')->select();
		$AllTjPlatform = m()->query("
				select C.* from (
					select A.PlatformInfo,B.* from wp_j_recordxiao as A,(
						select PlatformID from wp_l_tj where 1=1 ".(!empty($AllPlatform) ? " and PlatformID not in ('".implode("','", array_column($AllPlatform, 'id'))."')" : "")." group by PlatformID
					) as B where B.PlatformID = A.PlatformID order by id desc
				) as C group by C.PlatformID
			");
		if(!empty($AllTjPlatform)){
			foreach($AllTjPlatform as $k=>$v){
				$info = json_decode($v['PlatformInfo'], true);
				if(!empty($info) && isset($info['PlatformName'])){
					$AllPlatform[] = array('id'=>$v['PlatformID'], 'PlatformName'=>$info['PlatformName']);
				}
			}
		}
		$this->assign('AllPlatform', $AllPlatform);

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

		/*所有推广页*/
		$AllTuiWeb = m('j_tuiweb')->where("status='0'")->order('rank desc,id asc')->field('id,TuiWebName')->select();
		$AllTjTuiWeb = m()->query("
				select C.* from (
					select A.TuiWebInfo,B.* from wp_j_recordxiao as A,(
						select TuiWebID from wp_l_tj where 1=1 ".(!empty($AllTuiWeb) ? " and TuiWebID not in ('".implode("','", array_column($AllTuiWeb, 'id'))."')" : "")." group by TuiWebID
					) as B where B.TuiWebID = A.TuiWebID order by id desc
				) as C group by C.TuiWebID
			");
		if(!empty($AllTjTuiWeb)){
			foreach($AllTjTuiWeb as $k=>$v){
				$info = json_decode($v['TuiWebInfo'], true);
				if(!empty($info) && isset($info['TuiWebName'])){
					$AllTuiWeb[] = array('id'=>$v['TuiWebID'], 'TuiWebName'=>$info['TuiWebName']);
				}
			}
		}
		$this->assign('AllTuiWeb', $AllTuiWeb);

		/*所有微信号*/
		$AllWechat = m('j_wechat')->where("WechatType = 'sq' and status='0'")->order('rank desc,id asc')->field('WechatID,WechatName')->select();
		$AllTjWechat = m()->query("
				select C.* from (
					select A.WechatInfo,B.* from wp_j_recordxiao as A,(
						select WechatID from wp_l_tj where 1=1 ".(!empty($AllWechat) ? " and WechatID not in ('".implode("','", array_column($AllWechat, 'WechatID'))."')" : "")." group by WechatID
					) as B where B.WechatID = A.WechatID order by id desc
				) as C group by C.WechatID
			");
		if(!empty($AllTjWechat)){
			foreach($AllTjWechat as $k=>$v){
				$info = json_decode($v['WechatInfo'], true);
				if(!empty($info) && isset($info['WechatName'])){
					$AllWechat[] = array('WechatID'=>$v['WechatID'], 'WechatName'=>$info['WechatName']);
				}
			}
		}
		$this->assign('AllWechat', $AllWechat);

		$this->display();
	}

	public function LXiangXiPage(){
		$para = '';

		$Search_arr = array(
			array("ProjectID = '", 'ProjectID', "'"),
			array("PlatformID = '", 'PlatformID', "'"),
			array("HuID in ('", 'HuID', "')"),
			array("ZhaoWebID in ('", 'ZhaoWebID', "')"),
			array("TuiWebID in ('", 'TuiWebID', "')"),
			array("WechatID in ('", 'WechatID', "')"),
			array("addtime >= '", 'StartTime', "'"),
			array("addtime <= '", 'EndTime', "'")
		);
		$para .= SearchPara($Search_arr);

		/*地域*/
		$city = I('post.city','');
		$city_para_arr = '';
		if(!empty($city)){
			foreach($city as $k=>$v){
				$pc = explode('-', $v);
				if(!empty($pc) && isset($pc[1])){
					$city_para_arr[]= "(provice = '".$pc[0]."' and city = '".$pc[1]."')";
				}
			}
		}
		if(!empty($city_para_arr)){
			$para .= " and (".implode(' or ', $city_para_arr).")";
		}

		/*时间点*/
		$TimeDian = I('post.TimeDian','');
		if(!empty($TimeDian)){
			$TimeDianType = I('post.TimeDianType','');
			if($TimeDianType == 'fw'){
				$para .= SearchPara(array(array("TimeDian in ('", 'TimeDian', "')")));
			}elseif($TimeDianType == 'fz'){
				$para .= " and id in (select TjID from wp_l_copy where 1=1 ".SearchPara(array(array("TimeDian in ('", 'TimeDian', "')")))." group by TjID)";
			}
		}

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
		$sort = I("post.sort",'addtime');
		$sortOrder = I('post.sortOrder','desc');

//select T.*,O.Avalue as Screen from ( select T.*,O.Avalue as Browser from ( select T.*,O.Avalue as System from ( select T.*,K.KeyWords as TopKeyWords from ( select T.*,K.KeyWords from ( select * from wp_l_tj where 1=1 and addtime >= '1516118400' and addtime <= '1516204799' and id in (select TjID from wp_l_copy where 1=1 and TimeDian in ('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23') and TjID in (select id from wp_l_tj where 1=1 and addtime >= '1516118400' and addtime <= '1516204799' and copy = 1) group by TjID) order by addtime desc limit 0,10) as T left join wp_l_keywords K on T.KeyWordsID = K.id ) as T left join wp_l_keywords K on T.TopKeyWordsID = K.id ) as T left join wp_l_agent O on T.SystemID = O.id ) as T left join wp_l_agent O on T.BrowserID = O.id ) as T left join wp_l_agent O on T.ScreenID = O.id

		$list = m()->query("
			select T.*,O.Avalue as Screen  from (
				select T.*,O.Avalue as Browser  from (
					select T.*,O.Avalue as System  from (
						select T.*,K.KeyWords as TopKeyWords from (
							select T.*,K.KeyWords from (
								select * from wp_l_tj where 1=1 ".$para." order by ".$sort." ".$sortOrder." limit ".(($page - 1)*$rows).",".$rows.
							") as T left join wp_l_keywords K on T.KeyWordsID = K.id
						) as T left join wp_l_keywords K on T.TopKeyWordsID = K.id
					) as T left join wp_l_agent O on T.SystemID = O.id
				) as T left join wp_l_agent O on T.BrowserID = O.id
			) as T left join wp_l_agent O on T.ScreenID = O.id

		");
		//print_r ($list);exit();
		//$list = m('l_tj')->where(" 1=1 ".$para)->field()->order($sort." ".$sortOrder)->page($page.','.$rows)->select();
		$count = m('l_tj')->where(" 1=1 ".$para)->count();

		if(!empty($list)){
			foreach($list as $k=>$v){
				$info = m('j_recordxiao')->where("TuiWebID = '".$v['TuiWebID']."' and StartTime <= '".$v['addtime']."'")->field('ProjectInfo,PlatformInfo,HuInfo,ZhaoWebInfo,WechatInfo,TuiWebInfo')->order('StartTime desc')->find();

				$CopyInfo = m('l_copy')->where("TjID = '".$v['id']."'")->order('addtime asc')->field('WID,times,TimeDian')->select();

				$info['CopyInfo'] = $CopyInfo;

				$list[$k] = array_merge($v,$info);
			}
		}

		/*总复制量*/
		$CopyCount = m('l_tj')->where(" 1=1 ".$para." and id in (select TjID from wp_l_copy where 1=1 group by TjID)")->count();

		/*平均复制时常*/
		$CopyTimeAVG = m()->query("
			select AVG(C.times) as CopyTimeAVG from (
				select C.times from (
					select times,TjID from wp_l_copy where TjID in (
						select id from wp_l_tj where 1=1 ".$para."
					) order by addtime desc
				) as C group by C.TjID
			) as C
		");

		/*平均停留时长*/
		$LookTimeAVG = m()->query("select AVG(LookTime) as LookTimeAVG from wp_l_tj where 1=1 ".$para." and LookTime != 0");

		/*平均浏览高度*/
		$LookHeightAVG = m()->query("select AVG(LookHeight) as LookHeightAVG from wp_l_tj where 1=1 ".$para." and LookTime != 0");

		$data = array(
			'CopyCount'=>$CopyCount,
			'CopyTimeAVG'=>isset($CopyTimeAVG[0]['CopyTimeAVG']) ? $CopyTimeAVG[0]['CopyTimeAVG'] : 0,
			'LookTimeAVG'=>isset($LookTimeAVG[0]['LookTimeAVG']) ? $LookTimeAVG[0]['LookTimeAVG'] : 0,
			'LookHeightAVG'=>isset($LookHeightAVG[0]['LookHeightAVG']) ? $LookHeightAVG[0]['LookHeightAVG'] : 0,
			'rows'=>(!empty($list) ? $list : array()),
			'total'=>$count
		);

		echo json_encode($data);
	}


}