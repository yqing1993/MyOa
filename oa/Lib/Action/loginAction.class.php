<?php
class loginAction extends Action {
	function __construct(){
		parent::__construct();

		if(!isset($_SESSION['userid']) || !isset($_SESSION['time']) || $_SESSION['userid']==''){
			if(isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST')){
				alert("登录超时，请重新登录");
			}else{
				echo '<script>window.location = "http://'.$_SERVER['HTTP_HOST'].'/oa.php/admin/login/";</script>';
			}
			exit();
		}else{
			$time = time();
			$num = $time - $_SESSION['time'];
			if($num > 18000){

				/*POST登录超时*/
				if(isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST')){
					alert("登录超时，请重新登录");
				}else{
					echo '<script>alert("登录超时，请重新登录");window.location = "http://'.$_SERVER['HTTP_HOST'].'/oa.php/admin/login/";</script>';
					exit();
				}

			}else{
				session("time",$time);
			}
		}
	}
}







