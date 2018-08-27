<?php
class TongjiSetAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('TongjiSet',3);
	}

	public function index(){
		$RightUrl = require_once(ROOT."/oa/Conf/Check/RightUrl.php");
		$this->assign('RightUrl',implode(",",$RightUrl['RightUrl']));

		$this->display();
	}

	public function Data(){
		$operate = I('post.operate','');

		if($operate == 'AddUrl'){
			$RightUrl = I('post.RightUrl','');

			if($RightUrl != ''){
				$RightUrl_arr = explode(",",$RightUrl);
				if(!empty($RightUrl_arr)){
					F('RightUrl',array('RightUrl'=>$RightUrl_arr),'oa/Conf/Check/');

					records("TongjiSet","重新设置了着陆页统计授权域名");

					alert('设置成功',1);
				}else{
					alert('授权数组为空');
				}
			}else{
				alert('授权域名不能为空');
			}
		}else{
			alert("不知道你要干啥");
		}
	}

}