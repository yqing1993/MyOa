<?php
class LogAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('Log',3);
	}

	public function index(){
		$this->display();
	}

	public function LogPage(){
		$data = DataPage(array('database'=>'record','order'=>'addtime desc'));

		echo json_encode($data);
	}

	/*DATA处理页面*/
	public function LogData(){
		$id = I('post.id');

		if($id != ''){
			$operate = I('post.operate','');

			if($operate=='del'){
				$StartTime = I('post.StartTime','');
				$OverTime = I('post.OverTime','');

				$para = '';
				if($StartTime != ''){
					$para .= " and addtime >= '".$StartTime."'";
				}

				if($OverTime != ''){
					$para .= " and addtime <= '".$OverTime."'";
				}

				if($para != ''){
					$count = m("record")->where("1=1".$para)->count();
					if($count > 0){

						m("record")->where("1=1".$para)->delete();

						records("LogDel"," 清除了 ".date("Y-m-d H:i:s",$StartTime)." 到 ".date("Y-m-d H:i:s",$OverTime)." 的 共 ".$count." 条 操作日志");

						alert("清除成功",1);
					}else{
						alert("该时间段内，没有操作日志");
					}
				}else{
					alert("至少要选择一个时间点");
				}

			}else{
				alert("不知道你要干啥");
			}

		}else{
			alert("出错，提交的ID为空");
		}
	}
}