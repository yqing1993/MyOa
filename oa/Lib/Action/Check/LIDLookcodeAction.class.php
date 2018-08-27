<?php
class LIDLookcodeAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDLookcode',3);
	}

	public function index(){
			ob_end_clean();
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.C('OrderUrl').'oadd.php/LookCodeData/');


		
	}
	public 	function curl_post($url, $post){
			$options = array(
				CURLOPT_RETURNTRANSFER =>true,
				CURLOPT_HEADER =>false,
				CURLOPT_POST =>true,
				CURLOPT_POSTFIELDS => $post,
			);
			$ch = curl_init($url);
			curl_setopt_array($ch, $options);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
	}

}	