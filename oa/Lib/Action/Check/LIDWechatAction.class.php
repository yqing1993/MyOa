<?php
class LIDWechatAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDWechat',3);
	}

	public function index(){
		$AllWechat = m('j_wechat')->where("WechatType = 'sq' and status='0'")->order('rank desc,id asc')->field('WechatID')->select();
		//dump($AllWechat);die;
		$AllWechatID = array_column($AllWechat, 'WechatID');
		$chatid=D("DataApi")->encode(implode(",", $AllWechatID));
		if(!empty($AllWechatID)){
			ob_end_clean();
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.C('OrderUrl').'oadd.php/LIDWechat/index/?LookCode='.$chatid);
		}
	}

}