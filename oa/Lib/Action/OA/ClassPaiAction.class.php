<?php
class ClassPaiAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('ClassPai',3);
	}

	public function index(){

		$this->assign('ActionName', $this->getActionName());

		/*全部班次*/
		$AllClassCi = m('classci')->where("status = 0")->field('id,ClassCiName')->order('rank desc,id asc')->select();
		$AllClassCi = !empty($AllClassCi) ? array_column($AllClassCi, 'ClassCiName', 'id') : array();
		$this->assign('AllClassCi', $AllClassCi);

		/*班制*/
		$this->assign('ClassType',C('ClassType'));

		$this->display("index");
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

	public function ClassPaiData(){
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

			$PaiData = m('classpai')->where("day >= ".$FirstDays." and day <= ".$LastDays)->select();
			//echo json_encode($PaiData);die;
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
			$AllUser = m('classuser')->where("status = 0")->order('rank desc,id asc')->field('userid,username')->select();
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

    /**
     * 上传排班
     */
	public function upload(){
	    $file = $_FILES['file'];
	    //echo json_encode($file);
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts = array('xls','xlsx');// 设置附件上传类型
        $upload->savePath =  './Public/Uploads/';// 设置附件上传目录

        $info = $upload->uploadOne($_FILES['file']);
        $filename = $info[0]['savepath'].$info[0]['savename'];
        $exts = $info[0]['extension'];

        //判断是何类型
        $name = $info[0]['name'];
        $name = explode('.',$name);
        if(count($name) >2){
            //周排班
            $type = 1;
        }else{
            //月排班
            $type = 2;
        }
        //$name  = $name[0];
        //echo json_encode($name);die;
        //echo json_encode($info);die;
        if(!$info) {// 上传错误提示错误信息
            alert($this->error($upload->getError()));
        }else{// 上传成功
            $this->dealFile($filename, $exts,$type);
        }
    }

    public function dealFile($filename, $exts='xls',$type){

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.IOFactory");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            vendor("PHPExcel.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            vendor("PHPExcel.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }

        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        $data = array();
        if($type == 1){
            //周排班
            $date = array();
            //获取第一行的日期
            for($a=3;$a<$allRow;$a++){
                    $b = 'A'.$a;
                $c = $currentSheet->getCell($b)->getValue();
                $date[] = $c;
            }
            $date = array_filter($date);
            $date = array_values($date);
            //echo json_encode($date);die;
            //过滤汉字 转为时间格式
            $year = date('Y',time());
            for($j=0;$j<count($date);$j++){
                //$date[$j] = substr($date[$j],9,4);
                $date[$j] = str_replace('）','-',$date[$j]);
                $date[$j] = str_replace('（','-',$date[$j]);
                $date[$j] = explode('-',$date[$j]);
                $date[$j] = $date[$j][1];
                $date[$j] = str_replace('.','-',$date[$j]);
                $date[$j] = $year.'-'.$date[$j];
                $date[$j] = strtotime($date[$j]);
                $date[$j] = date('Y-m-d',$date[$j]);
            }
            //echo json_encode($date);die;
            //$allColumn = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转化为列索引数字 'A->0,Z->25
            for($colIndex='C';$colIndex<$allColumn;$colIndex++){        //循环读取每个单元格的内容。注意行从1开始，列从A开始
                for($rowIndex=3;$rowIndex<$allRow;$rowIndex++){
                    $addr = $colIndex.$rowIndex;
                    $cell = $currentSheet->getCell($addr)->getValue();
                    $key = $colIndex.'2';
                    $value = $currentSheet->getCell($key)->getValue();
                    if($cell instanceof PHPExcel_RichText){ //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    //$cell = array_filter($cell);
                    $data[$value][] = $cell;
                    unset($data['']);
                }
            }
            //echo json_encode($data);die;
            $new_arr = array();
            foreach ($data as $k => $v){
               for($s=0;$s<count($v)/2;$s++){
                $new_arr[$k][$s]['早班'] = $v[2*$s];
                $new_arr[$k][$s]['晚班'] = $v[2*$s+1];
                    if($new_arr[$k][$s]['早班'] == '休'){
                       if($new_arr[$k][$s]['晚班'] == '休'){
                           unset($new_arr[$k][$s]['早班']);
                           unset($new_arr[$k][$s]['晚班']);
                           $new_arr[$k][$s]['休息'] = '休息';
                       }else{
                           unset($new_arr[$k][$s]['早班']);
                       }
                    }elseif ($new_arr[$k][$s]['早班'] == '发货'){
                        unset($new_arr[$k][$s]['早班']);
                        unset($new_arr[$k][$s]['晚班']);
                        $new_arr[$k][$s]['班次'] = '行政班';
                    }elseif ($new_arr[$k][$s]['早班'] == '班'){
                        if($new_arr[$k][$s]['晚班'] == '休')
                        unset($new_arr[$k][$s]['早班']);
                        unset($new_arr[$k][$s]['晚班']);
                        $new_arr[$k][$s]['班次'] = '行政班';
                    }else{
                        unset($new_arr[$k][$s]['晚班']);
                    }
                }
            }
            $new_arr['date'] = $date;
            $this->save_week($new_arr);
            //echo json_encode($new_arr);die;
        }elseif ($type == 2){
            $date = array();
            //获取第一行的日期
            for($a='C';$a<=$allColumn;$a++){
                $b = $a.'1';
                $c = $currentSheet->getCell($b)->getValue();
                $date[] = $c;
            }
            //echo json_encode($date);die;
            //过滤汉字 转为时间格式
            $year = date('Y',time());
            for($j=0;$j<count($date);$j++){
                $date[$j] = str_replace('月','-',$date[$j]);
                $date[$j] = explode('日',$date[$j])[0];
                $date[$j] = $year.'-'.$date[$j];
                $date[$j] = strtotime($date[$j]);
                $date[$j] = date('Y-m-d',$date[$j]);
            }
            //echo json_encode($date);die;
            for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){        //循环读取每个单元格的内容。注意行从1开始，列从A开始
                for($colIndex='B';$colIndex<=$allColumn;$colIndex++){
                    $addr = $colIndex.$rowIndex;
                    $cell = $currentSheet->getCell($addr)->getValue();
                    if($cell instanceof PHPExcel_RichText){ //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    $data[$rowIndex][] = $cell;
                }
            }
            $data = array_values($data);
            $data['date'] = $date;
        }

        if(is_file($filename)){
            unlink($filename);
        }
        $this->save_month($data);

    }
    public function save_week($data){
        $add = array();
        $date = $data['date'];
        unset($data['date']);
        //echo json_encode($data);die;
//        foreach ($data as $key => $value){
//            $data[$key]['name'] = $key;
//        }
        //echo json_encode($data);die;
        foreach($data as $key => $value){
            foreach($value as $k => $v){
                //根据username 获取 userid DepartmentID
                $username = $key;
                $info = m('user')->where("username = '$username'")->field('userid,DepartmentID')->find();
                $userid = $info['userid'];
                $DepartmentID = $info['DepartmentID'];
                $DepartmentName = m('department')->where("id = '$DepartmentID'")->field('DepartmentName')->find();
                $DepartmentName = $DepartmentName['DepartmentName'];
                $UserInfo['username'] = $username;
                $UserInfo['DepartmentName'] = $DepartmentName;
                //unset($value['name']);
                $new = array_values($value);
                //echo json_encode($new);die;
                for($a=0;$a<count($new);$a++){
                    $add[$key][$a]['StartTime'] = strtotime($date[$a].' 00:30:00');
                    $add[$key][$a]['EndTime'] = strtotime($date[$a].' 09:30:00');
                    $add[$key][$a]['day'] = strtotime($date[$a]);
                    $add[$key][$a]['days'] = $date[$a];
                    $add[$key][$a]['userid'] = $userid;
                    $add[$key][$a]['DepartmentID'] = $DepartmentID;
                    $add[$key][$a]['UserInfo'] = json_encode($UserInfo,JSON_UNESCAPED_UNICODE);
                    $add[$key][$a]['addtime'] = time();
                    foreach ($new[$a] as $m => $n){
                        if($m == '班次'){
                            //行政班
                            $add[$key][$a]['ClassType'] = '0';
                            $CiData = array();
                            $CiData[] = m('classci')->where("ClassCiName = '行政班'")->field('id,ClassCiName,Wechat')->find();
                            $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                        }elseif($m == '休息') {
                            $add[$key][$a]['ClassType'] = '0';
                            $CiData = array();
                            $CiData[] = m('classci')->where("ClassCiName = '休息'")->field('id,ClassCiName,Wechat')->find();
                            $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                        }else{
                            if($m == '早班'){
                                $add[$key][$a]['ClassType'] = '1';
                            }elseif($m == '晚班'){
                                $add[$key][$a]['ClassType'] = '2';
                            }
                            //判断是什么班制
                            $tem = explode('+',$n);
                            if(count($tem) >1){
                                //多班制
                                $CiData = array();
                                for($t=0;$t<count($tem);$t++){
                                    $ClassCiName = $tem[$t].'班';
                                    $CiData[] = m('classci')->where("ClassCiName = '$ClassCiName'")->field('id,ClassCiName,Wechat')->find();
                                }
                                $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                            }else{
                                //单班制
                                $ClassCiName = $n.'班';
                                $CiData = array();
                                $CiData[] = m('classci')->where("ClassCiName = '$ClassCiName'")->field('id,ClassCiName,Wechat')->find();
                                $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }
            }
        }

        //数据入库 关联数组转为索引数组
        $add = array_values($add);
        //echo json_encode($add);die;

        foreach ($add as $c => $d){
            $res[] = m('classpai')->addAll($d);
        }
        for($l=0;$l<count($res);$l++){
            if(!$res[$l]){
                alert('排班导入错误，请重新导入');
            }
        }

        alert('导入成功！',1);

        echo json_encode($res);die;
    }
    public function save_month($data){
        //拼装sql
        $add = array();
        $date = $data['date'];
        unset($data['date']);
        foreach ($data as $key => $value){
            for($a=0;$a<count($date);$a++){
                $add[$key][$a]['day'] = strtotime($date[$a]);
                $add[$key][$a]['days'] = $date[$a];
                $info = m('user')->where("username = '$value[0]'")->field('userid,DepartmentID')->find();
                $userid = $info['userid'];
                $DepartmentID = $info['DepartmentID'];
                $add[$key][$a]['days'] = $date[$a];
                $add[$key][$a]['userid'] = $userid;
                $add[$key][$a]['DepartmentID'] = $DepartmentID;
                $DepartmentName = m('department')->where("id = '$DepartmentID'")->field('DepartmentName')->find();
                $DepartmentName = $DepartmentName['DepartmentName'];
                $UserInfo['username'] = $value[0];
                $UserInfo['DepartmentName'] = $DepartmentName;
                $add[$key][$a]['UserInfo'] = json_encode($UserInfo,JSON_UNESCAPED_UNICODE);
                $add[$key][$a]['ClassType'] = 0;
                $add[$key][$a]['StartTime'] = strtotime($date[$a].' 00:30:00');
                $add[$key][$a]['EndTime'] = strtotime($date[$a].' 09:30:00');
                if($value[$a+1] == '班'){
                    $CiData = array();
                    $CiData[] = m('classci')->where("ClassCiName = '行政班'")->field('id,ClassCiName,Wechat')->find();
                    $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                }elseif ($value[$a+1] == '休'){
                    $CiData = array();
                    $CiData[] = m('classci')->where("ClassCiName = '休息'")->field('id,ClassCiName,Wechat')->find();
                    $add[$key][$a]['ClassCiInfo'] = json_encode($CiData,JSON_UNESCAPED_UNICODE);
                }
                $add[$key][$a]['addtime'] = time();
            }
        }
        //echo json_encode($add);die;
        //数据入库
        foreach ($add as $k => $v){
            $res[] = m('classpai')->addAll($v);
        }
        for($l=0;$l<count($res);$l++){
            if(!$res[$l]){
                alert('排班导入错误，请重新导入');
            }
        }

        alert('导入成功！',1);
    }
}