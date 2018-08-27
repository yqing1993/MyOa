<?php
class adminAction extends Action {
	function __construct(){
		parent::__construct();
	}

	public function login(){
		$this->display("./oa/Tpl/OA/login_index.html");
//        $this->display();
	}

	public function notmac(){
		if(isset($_SERVER["PATH_INFO"]) && $_SERVER["PATH_INFO"] != '' && strstr($_SERVER["PATH_INFO"],'/notmac/window/web/login/')){
			$this->display("./oa/Tpl/OA/login_index_notmac.html");
		}else{
			$this->error("网址有误","/order.php/");
		}
	}

	public function logins(){
		$user = I('user','');
		$password = I('password','');
		$verify = I('verify','');
		$mac = I('mac','');

		//dump($user);die;

		if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] != '' && strstr($_SERVER["HTTP_REFERER"],'/notmac/window/web/login/')){
			//免MAC验证登陆过来的
		}

		if($verify != ''){
			if(isset($_SESSION['verify']) && md5($verify) == $_SESSION['verify']){
				if($user!=''){
					$info = m("user")->where("(userid = '".$user."' or username = '".$user."')")->find();
					if(!empty($info)){

						if($info['status'] != '0'){
							alert("该账号已被暂停，请联系管理员");
							exit();
						}

						if($password != $info['password']){
							session('verify',null);
							alert("密码错误",3);
							exit();
						}

						$MacStatus = 0;
						if($mac == ''){
							if($info['MacSwitch'] == '1'){
								//不需要MAC验证
							}else{
								alert("MAC地址不能为空");
								exit();
							}
						}else{
							$mac_arr = explode(",",$info['mac']);
							$status = 0;
							if(!empty($mac_arr)){
								foreach($mac_arr as $k=>$v){
									if($mac == md5($v)){
										$status = 1;
										break;
									}
								}
							}
						
							if($status == 1){
								$MacStatus = 1;
							}
						}
						
						/*设置session*/
                        $role = json_decode($info['role'])[0];
						session("userid",$info['userid']);
						session("MacStatus",$MacStatus);
						session("time",time());
						session("DepartmentID",$info['DepartmentID']);
						session("Duty",$info['Duty']);
						session("DepartmentName",$info['DepartmentName']);
						session("role",$role);
						session("username",$info['username']);
						session("id",$info['id']);
						//dump($info);die;

						/*写入操作日志*/
						$ip = get_client_ip();
						records("login","登陆成功，登陆IP：".$ip);

						alert("登陆成功",10);

					}else{
						alert("该用户不存在");
					}
				}else{
					alert("用户名不能为空");
				}
			}else{
				alert("验证码有误",3);
			}
		}else{
			alert("请输入验证码");
		}
	}

	public function loginout(){
		/*删除session*/
		session(null);
		echo '<script>window.location = "http://'.$_SERVER['HTTP_HOST'].'/oa.php/admin/login/";</script>';
		//$this->display("./order/Tpl/login_mac.html");
	}

}