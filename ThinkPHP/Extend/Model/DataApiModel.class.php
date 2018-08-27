<?php
class DataApiModel extends Model{
	public $AppSecret = 'MmxkKl12.!k890.@Q12421SAXsd';
	public $birthday = 202052019;//时间验证，随机时间值
	public $TimeNum = 35;//一个签名，只允许存活多秒

	public function Get($arr){
		$CodeStr = $this->encode($arr);

		$ch = curl_init();
		$timeout = 20;
		curl_setopt ($ch, CURLOPT_URL, C('OrderUrl')."oadd.php/SjoaddApi/");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, array('Str'=>$CodeStr));
		
		$content = curl_exec($ch);
		curl_close($ch);
		
		
		$content = $this->decode($content);
		
		return $content;
	}
	
	public function encode($arr){
		$arr = array('data'=>$arr,'time'=>(time() - $this->birthday));

		$ArrStr = json_encode($arr);
		$ArrStr = base64_encode($ArrStr);
		$ArrStrCode = encrypt($ArrStr,'E');

		$AppSecret = $this->AppSecret;
		$Sign = md5($ArrStrCode.$AppSecret);

		$Str = base64_encode(json_encode(array('Str'=>$ArrStrCode,'Sign'=>$Sign)));

		return $Str;
	}

	public function decode($Str){

		$Str = base64_decode($Str);
		$StrArr = json_decode($Str, true);

		$arr = array();
		$AppSecret = $this->AppSecret;
		if(md5($StrArr['Str'].$AppSecret) == $StrArr['Sign']){
			$Str = encrypt($StrArr['Str'],'D');
			$Str = base64_decode($Str);

			$arr = json_decode($Str, true);
			$GetTime = $arr['time'] + $this->birthday;
			if(time() - $GetTime <= $this->TimeNum){
				return $arr['data'];
			}else{
				$arr = array();
			}
		}
		return $arr;
	}
}