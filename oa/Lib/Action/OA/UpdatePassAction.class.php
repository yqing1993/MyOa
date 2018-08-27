<?php
class UpdatePassAction extends loginAction {
	function __construct(){
		parent::__construct();
		//role('标准',3);
	}

	public function index(){

		$this->display();
	}

	/*短信模板DATA处理页面*/
	public function Data(){
		$operate = I('post.operate','');

		/*新增和修改推荐码*/
		if($operate == 'UpdatePass'){
			$TopPassWord = I('post.TopPassWord','');
			$NewPassWord = I('post.NewPassWord','');
			$CFPassWord = I('post.CFPassWord','');

			$user_info = get_user_info('userid,password');

			if($TopPassWord == $user_info['password']){
				if($NewPassWord == $CFPassWord){
					
					m('user')->where("userid = '".$user_info['userid']."'")->save(array('password'=>$NewPassWord));

					records("UpdatePass","修改了登陆密码");

					alert("修改成功",1);

				}else{
					alert("修改失败，重复密码有误");
				}
			}else{
				alert("修改失败，原密码错误");
			}
		}else{
			alert("不知道你要干啥");
		}
	}

}