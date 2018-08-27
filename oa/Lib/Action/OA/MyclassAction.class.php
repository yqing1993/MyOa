<?php
class MyclassAction extends Action {
	function __construct(){
		parent::__construct();
		//role('Myclass',3);
	}

	public function index(){
		//dump($_POST);die;
		//$userid = I('get.userid','');
		$userid = $_SESSION['userid'];
		//$Model = new Model();
		$DepartmentID = $_SESSION['DepartmentID'];
		$AllUser = getAllUsers($DepartmentID);

		$this->assign('rsl',$AllUser);

		if($userid == ''){
			R('login');
			role('Myclass',3);
		}
		$this->assign('userid', $userid);


		$this->assign('ActionName', $this->getActionName());


		//对应时间的班次和微信号




		/*全部班次*/
		$AllClassCi = m('classci')->where("status = 0 and id>28")->field('id,ClassCiName,Wechat')->order('rank desc,id asc')->select();

		//$AllClassCi = m('classci')->where("status = 0 and id>6")->field('id,ClassCiName,Wechat')->order('rank desc,id asc')->select();
		$arr1 = array();
		foreach($AllClassCi as $k=>$v2)
		{
			$arr1[$k]['wechat'] = Jiema($v2['Wechat']);
			$AllClassCi[$k]['wechat'] = $arr1[$k]['wechat'];
		}
		//$data['arr'] = $AllClassCi;
		//$v2 = $arr1['wechat'];
		//$AllClassCi[] = $arr1['wechat'];

		//dump($data['arr']);die;
		//把班次表的id查出来拼成字符串
		/*foreach($AllClassCi as $k=>$v)
		{
			$rs1[$k]['id'] = $v['id'];
		}
		//拼成数组

		//$a = explode(',',rtrim($rs1,','));


		//当前周第一天与最后一天的时间戳
		$Datetime = weektime();
		$FirstDays = $Datetime[0];
		$LastDays = $Datetime[1];

		//循环数组，查找classcicopy表是否有数据，没有就把现在的数据插入进去

		$arr = array();

		foreach($rs1['id'] as $k=>$vo)
		{
			$where['cid'] = $vo;
			dump($rs1);
			$where['ctime'] = array(array('egt',$FirstDays),array('elt',$LastDays));
			$aa = m('classcicopy')->where($where)->order("id desc")->find();
			echo m('classcicopy')->getLastSql();die;

			//$w = json_decode($aa['Wechat']);
			if(empty($aa))
			{
				$data['cid'] = $vo;
				$rs = ClassCiInsert($vo);
				$aaa = m('classcicopy')->where($data)->order("id desc")->find();
				//$aaa['wechat'] = Jiema($aaa['Wechat']);
				$arr[] = $aaa;
				//dump($arr);die;
			}else
			{
				//$aa['wechat'] = Jiema($aa['Wechat']);
				$arr[] = $aa;
			}
		}
		dump($aa);die;*/

        $time = m('classpai')->where("userid = '$userid'")->max('addtime');
       // $a = m('classpai')->getLastSql();
        //dump($time);die;
        $data = m('classpai')->where("addtime = '$time' and userid = '$userid'")->field('days,ClassCiInfo')->select();
        foreach ($data as $key =>$value){
            $a = json_decode($value['ClassCiInfo'],true);
            $data[$key]['ClassCiInfo'] = $a[0]['ClassCiName'];
        }

        $time = date('Y-m-d H:i:s',$time);
        //dump($a);die;


		$this->assign('data',$data);
		//$this->assign('AllWechat',$arr);
		$this->assign('time',$time);


		$this->assign('AllClassCi', $AllClassCi);


		/*班制*/
		$this->assign('ClassType',C('ClassType'));

		$this->display();
	}

	/*计算week*/
	function week(){
		$time = time();
		$TopWeek = strtotime("+0 day", $time);
		$TopWeek = strtotime(date('Y-m-d', ($TopWeek - ((date('w',$TopWeek) == 0 ? 7 : date('w',$TopWeek)) - 1) * 24 * 3600)));//上周一

		$NextWeek = strtotime("+7 day", $time);
		$NextWeek = strtotime(date('Y-m-d', ($NextWeek + (7 - (date('w', $NextWeek) == 0 ? 7 : date('w', $NextWeek))) * 24 * 3600)));//下下周日

		$_POST['StartTime'] = $TopWeek;
		$_POST['EndTime'] = $NextWeek;

	}

	public function MyclassData(){
		//dump($_POST);
		$operate = I('operate','');
		//$operate = 'Look';
		if($operate == 'Look'){

			/*全部日期*/
			$StartTime = I('post.StartTime','');
			if($StartTime == ''){
				$this->week();
			}

			$day = Day();

			$FirstDays = strtotime($day[0]);
			$LastDays = strtotime(end($day).' 23:59:59');

			$data = array();

			/*表格头信息*/
			$data['head']['header']['name'] = array('name'=>'姓名','width'=>'150');
            $userid = $_SESSION['userid'];
            $PaiData = m('classpai')->where("day >= $FirstDays and day <= $LastDays and userid = '$userid' ")->select();
            //echo  json_encode(m('classpai')->getLastSql());DIE;

            //echo json_encode($_SESSION);die;

			//echo json_encode($where);die;
//			if($_SESSION['Duty'] == 'yg')
//			{
//				$PaiData = m('classpai')->where("day >= $FirstDays and day <= $LastDays and userid = '$userid' ")->select();
//            //echo json_encode(m('classpai')->getLastSql());DIE;
//			}else
//			{
//				$result = I('post.userid','');
//				$rs = explode(',',$result);
//				$where['day'] = array(array('egt',$FirstDays),array('elt',$LastDays));
//				$where['userid'] = array('in',$rs);
//				$PaiData = m('classpai')->where($where)->select();
//				//echo json_encode($PaiData);DIE;
//			}


			//echo json_encode($PaiAllUser);die;
			if(!empty($PaiData)){
				foreach ($PaiData as $k => $v) {
					$data['data'][$v['userid']]['list'][$v['day']] = array(
						'id'=>$v['id'],
						'ClassType'=>$v['ClassType'],
						'ClassCiInfo'=>$v['ClassCiInfo']
					);
					if(!isset($data['data'][$v['userid']]['name'])){
						$UserInfo = json_decode($v['UserInfo'], true);
						if(!empty($UserInfo)){
							$data['data'][$v['userid']]['name'] = $UserInfo['username'];
						}
					}
				}
			}

			/*排班全部人员*/
			$PaiAllUser = array();
			if(!empty($data['data'])){
				foreach($data['data'] as $k=>$v){
					if(!in_array($k, $PaiAllUser)){
						$PaiAllUser[] = $k;
					}
				}
			}


			/*全部人员*/
			$DepartmentID = $_SESSION['DepartmentID'];
			if($_SESSION['Duty'] != 'yg')
			{
				$AllUser = getAllUsers($DepartmentID);
			}else
			{
				$AllUser = m('classuser')->where("status = 0 and userid = '".$userid."'")->order('rank desc,id asc')->field('userid,username')->select();
			}


			//超级管理获取所有排班人员


				if(!empty($AllUser)){
					foreach($AllUser as $k=>$v){
						if(!in_array($v['userid'], $PaiAllUser)){
							$data['data'][$v['userid']] = array('list'=>array(),'name'=>$v['username']);
						}
					}
				}

			/*建立表格头*/
			if(!empty($day)){
				$week = array('日','一','二','三','四','五','六');
				foreach($day as $k=>$v){
					$data['head']['list'][strtotime($v)] = (date("Y-m-d",time()) == $v ? '今天' : date('d', strtotime($v))) ."<br>". $week[date('w', strtotime($v))];
				}
			}

			/*日期首末*/
			$data['date'] = array($day[0],end($day));
			/*$StartDate = $day[0];
			$EndDate = end($day);*/
			/*$data['a'] = $FirstDays;
			$data['b'] = $LastDays;*/

			$Ttime = time();

			if($Ttime<$FirstDays || $Ttime>$LastDays)
			{
				/*全部班次*/
				$AllClassCi = m('classci')->where("status = 0 and id>28")->field('id,ClassCiName,Wechat')->order('rank desc,id asc')->select();

				//把班次表的id查出来拼成字符串
				foreach($AllClassCi as $k=>$v)
				{
					$rs1[$k]['id'] = $v['id'];
				}
				//拼成数组

				//$a = explode(',',rtrim($rs,','));
				//$data['aa'] = $rs1;
				//当前日期第一天与最后一天的时间戳
				/*$FirstDays = strtotime($day[0]);
                $LastDays = strtotime(end($day));*/

				//循环数组，查找classcicopy表是否有数据，没有就把现在的数据插入进去
				$arr = array();
				foreach($rs1 as $k=>$vo)
				{
					$where['cid'] = $vo['id'];
					$where['ctime'] = array(array('egt',$FirstDays),array('elt',$LastDays));
					$aa = m('classcicopy')->where($where)->order("id desc")->find();
					if(empty($aa))
					{
						$data1['cid'] = $vo['id'];
						$rs = ClassCiInsert($vo['id']);
						$aaa = m('classcicopy')->where($data1)->order("id desc")->find();
						$aaa['wechat'] = Jiema($aaa['Wechat']);
						$arr[] = $aaa;
						//dump($arr);die;
					}else
					{
						$aa['wechat'] = Jiema($aa['Wechat']);
						$arr[] = $aa;
					}
				}
				//print_r($arr);
				$data['arr'] = $arr;

			}else
			{
				$AllClassCi = m('classci')->where("status = 0 and id>28")->field('id,ClassCiName,Wechat')->order('rank desc,id asc')->select();
				$arr1 = array();
				foreach($AllClassCi as $k=>$v2)
				{
					$arr1[$k]['wechat'] = Jiema($v2['Wechat']);
					$AllClassCi[$k]['wechat'] = $arr1[$k]['wechat'];
					$AllClassCi[$k]['classname'] = $v2['ClassCiName'];
				}
				$data['arr'] = $AllClassCi;
			}

			$data['error'] = 0;
			echo json_encode($data);
			//print_r($data);
			exit();
		}elseif($operate == 'AddUpdate'){

			$data = isset($_POST['data']) ? $_POST['data'] : '';
			if($data != ''){
				$data = json_decode($data, true);
				if(!empty($data)){
					$add = array();
					$save = array();
					$del = array();

					/*全部班次*/
					$arr = m('classci')->where("status = 0")->field('id,ClassCiName,Wechat')->select();
					$ClassCiInfo_arr = array();
					if(!empty($arr)){
						foreach($arr as $k=>$v){
							$ClassCiInfo_arr[$v['id']] = $v;
						}
					}

					/*全部人员*/
					$arr = m('user')->where("status = 0")->field('userid,username,DepartmentID,DepartmentName')->select();
					$UserInfo_arr = array();
					if(!empty($arr)){
						foreach($arr as $k=>$v){
							$UserInfo_arr[$v['userid']] = $v;
						}
					}

					/*班制*/
					$ClassType = C('ClassType');


					foreach($data as $k=>$v){
						if($v['id'] != ''){
							$ClassCi = $v['ClassCi'];
							if($ClassCi == '' || empty($ClassCi)){
								if($v['id'] != 'new'){
									$del[] = $v['id'];
								}
							}elseif(is_array($ClassCi)){
								$one = array();

								if($v['day'] == null || $v['day'] == ''){
									alert('有错误，时间不能为空');
								}else{
									if($v['ClassType'] == null || $v['ClassType'] == ''){
										alert('有错误，班制不能为空');
									}else{
										$one['day'] = $v['day'];
										$one['days'] = Date("Y-m-d", $v['day']);
										$one['ClassType'] = $v['ClassType'];
										$one['StartTime'] = strtotime(isset($ClassType[$one['ClassType']]) ? ($one['days'] . " " . $ClassType[$one['ClassType']]['start']) : 0);
										$one['EndTime'] = strtotime(isset($ClassType[$one['ClassType']]) ? ($one['days'] . " " . $ClassType[$one['ClassType']]['end']) : 0);
									}
								}


								if($v['userid'] == null || $v['userid'] == ''){
									alert('有错误，人员ID不能为空');
								}else{
									if(!isset($UserInfo_arr[$v['userid']])){
										alert("未找到该人员，userid=".$v['userid']."，请刷新页面重试");
									}else{
										$one['userid'] = $v['userid'];
										$one['DepartmentID'] = $UserInfo_arr[$v['userid']]['DepartmentID'];

										$new = $UserInfo_arr[$v['userid']];
										unset($new['userid']);
										unset($new['DepartmentID']);
										$one['UserInfo'] = json_encode($new, JSON_UNESCAPED_UNICODE);
									}
								}

								/*班次*/
								$Add_ClassCi = array();
								foreach($ClassCi as $k1=>$v1){
									if(!isset($ClassCiInfo_arr[$v1])){
										alert("未找到该班次，ID=".$v1."，请刷新页面重试");
									}else{
										$Add_ClassCi[] = $ClassCiInfo_arr[$v1];
									}
								}

								if(!empty($Add_ClassCi)){
									$one['ClassCiInfo'] =  json_encode($Add_ClassCi, JSON_UNESCAPED_UNICODE);
									$one['addtime'] = time();

									if($v['id'] == 'new'){
										$add[] = $one;
									}else{
										$save[$v['id']] = $one;
									}

								}else{
									alert("有错误，增加和修改，班次不能为空");
								}
							}
						}
					}

					if(!empty($add)){
						m('classpai')->addAll($add);
					}

					if(!empty($save)){
						foreach($save as $k=>$v){
							m('classpai')->where("id = ".$k)->save($v);
						}
					}

					if(!empty($del)){
						m('classpai')->where("id in ('".implode("','", $del)."')")->delete();
					}

					alert("提交成功",1);
				}else{
					alert("数据转换数组出错，数组为空");
				}
			}else{
				alert("提交的数据不能为空");
			}
		}else{
			alert('不知道你要干啥');
		}
	}
}