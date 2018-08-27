<?php
class TAction extends Action {
	public function add(){
		header('Content-Type:text/html;Charset=utf-8');

		/*当前页面*/
		$NowUrl = isset($_GET['NowUrl']) ? $_GET['NowUrl'] : '';
		$NowUrl = $this->unescape($NowUrl);
		$NowUrl = urldecode($NowUrl);
		$NowUrl = html_entity_decode($NowUrl);//转义字符，html字符转为字符串 例如：&amp; 转为 &
		//$NowUrl = 'http://slim.afaai.com/xgyj8-1/?cy=bd-xg-sl-yj3-d-sj1';
		preg_match('/(^http[s]*\:\/\/[^?]*)/',$NowUrl,$host);

		if(!empty($host) && isset($host[1]) && $host[1]!= ''){
			$TuiWebInfo = m('j_tuiweb')->where("TuiWebUrl = '".$host[1]."' and status = '0'")->field('id,ProjectID,PlatformID,HuID,ZhaoWebID,WechatID')->find();
			if(!empty($TuiWebInfo)){
				$add = array('TuiWebID'=>$TuiWebInfo['id']);
				$add = array_merge($add,$TuiWebInfo);
				unset($add['id']);

				/*当前网址*/
				$NowUrlID = m('l_nowurl')->add(array('url'=>$NowUrl));
				if($NowUrlID){
					$add['NowUrlID'] = $NowUrlID;
				}

				/*来源网址*/
				// $FromUrl = isset($_GET['FromUrl']) ? $_GET['FromUrl'] : '';
				// if($FromUrl != ''){
				// 	$FromUrl = $this->unescape($FromUrl);
				// 	$FromUrl = urldecode($FromUrl);
				// 	$FromUrl = urldecode($FromUrl);
				// 	$FromUrl = html_entity_decode($FromUrl);//转义字符，html字符转为字符串 例如：&amp; 转为 &

				// 	$FromUrlID = m('l_fromurl')->add(array('url'=>$FromUrl));
				// 	if($FromUrlID){
				// 		$add['FromUrlID'] = $FromUrlID;
				// 	}

				// 	匹配关键字
				// 	$NowKeyWords = array();
				// 	$TopKeyWords = array();
				// 	preg_match('/^http[s]*\:\/\/([^\/|^\?]*)/',$FromUrl,$host);
				// 	if(strstr($host[1],'baidu.com')){
				// 		preg_match('/[\&|\?](word|wd)=([^\&]*)/',$FromUrl,$NowKeyWords);
				// 		preg_match('/[\&|\?](oq)=([^\&]*)/',$FromUrl,$TopKeyWords);
				// 	}elseif(strstr($host[1],'sm.cn')){
				// 		preg_match('/[\&|\?](q)=([^\&]*)/',$FromUrl,$NowKeyWords);
				// 	}
				// }


				/*当前关键字*/
				$NowKeyWords = isset($_GET['NowKeyWords']) ? $_GET['NowKeyWords'] : '';
				if($NowKeyWords != ''){
					$info = m('l_keywords')->where("type = '0' and KeyWords = '".$NowKeyWords."'")->field("id")->find();
					if(!empty($info)){
						$add['KeyWordsID'] = $info['id'];
					}else{
						$KeyWordsID = m('l_keywords')->add(array('type'=>0,'KeyWords'=>$NowKeyWords));
						if($KeyWordsID){
							$add['KeyWordsID'] = $KeyWordsID;
						}
					}
				}

				/*上一个关键字*/
				$TopKeyWords = isset($_GET['TopKeyWords']) ? $_GET['TopKeyWords'] : '';
				if($TopKeyWords != ''){
					$info = m('l_keywords')->where("type = '1' and KeyWords = '".$TopKeyWords."'")->field("id")->find();
					if(!empty($info)){
						$add['TopKeyWordsID'] = $info['id'];
					}else{
						$TopKeyWordsID = m('l_keywords')->add(array('type'=>1,'KeyWords'=>$TopKeyWords));
						if($TopKeyWordsID){
							$add['TopKeyWordsID'] = $TopKeyWordsID;
						}
					}
				}

				/*ip*/
				$add['ip'] = get_client_ip(1);

				/*省份城市*/
				if($add['ip'] > 0){
					import('ORG.Net.IpLocation');// 导入IpLocation类
					$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
					$area = $Ip->getlocation(long2ip($add['ip'])); // 获取某个IP地址所在的位置
					if(!empty($area) && isset($area['country']) && $area['country'] != ''){
						$citys = C('citys');
						$dizhi = $area['country'];
						$provice = '';
						$provice_str = '';
						$city = '';
						$city_str = '';

						for($p=1;$p<count($citys);$p++){
							preg_match("/^".preg_replace("/省$/u","",$citys[$p][0])."省*/u",$dizhi,$pipei);
							if(!empty($pipei)){
								$provice = $p;
								$provice_str = $pipei[0];
								break;
							}
						}

						if($provice != ''){
							$dizhi = preg_replace("/^".$provice_str."/u","",$dizhi);
							for($c=1;$c<count($citys[$provice][1]);$c++){
								preg_match("/^".preg_replace("/市$/u","",$citys[$provice][1][$c])."市*/u",$dizhi,$pipei);
								if(!empty($pipei)){
									$city = $c;
									$city_str = $pipei[0];
									break;
								}
							}
						}else{
							for($p=1;$p<count($citys);$p++){
								for($c=1;$c<count($citys[$p][1]);$c++){
									preg_match("/^".preg_replace("/市$/u","",$citys[$p][1][$c])."市*/u",$dizhi,$pipei);
									if(!empty($pipei)){
										$provice = $p;
										$provice_str = $citys[$p][0];

										$city = $c;
										$city_str = $pipei[0];
										break;
									}
								}
							}
						}

						$add['provice'] = $provice != '' ? $provice : 0;
						$add['city'] = $city != '' ? $city : 0;
					}
				}

				/*系统类型*/
				$system = $this->getOS();
				if($system != ''){
					$info = m('l_agent')->where("Akey = '0' and Avalue = '".$system."'")->field("id")->find();

					if(!empty($info)){
						$add['SystemID'] = $info['id'];
					}else{
						$add['SystemID'] = m('l_agent')->add(array('Akey'=>'0','Avalue'=>$system));
					}
				}

				/*浏览器*/
				$Browser = $this->browse_info();
				if($Browser != ''){
					$info = m('l_agent')->where("Akey = '1' and Avalue = '".$Browser."'")->field("id")->find();
					if(!empty($info)){
						$add['BrowserID'] = $info['id'];
					}else{
						$add['BrowserID'] = m('l_agent')->add(array('Akey'=>'1','Avalue'=>$Browser));
					}
				}

				/*屏幕尺寸*/
				$screen = isset($_GET['screen']) ? $_GET['screen'] : '0*0';
				if($screen != ''){
					$info = m('l_agent')->where("Akey = '2' and Avalue = '".$screen."'")->field("id")->find();
					if(!empty($info)){
						$add['ScreenID'] = $info['id'];
					}else{
						$add['ScreenID'] = m('l_agent')->add(array('Akey'=>'2','Avalue'=>$screen));
					}
				}

				$add['addtime'] = (isset($_GET['addtime']) && $_GET['addtime'] != '') ? $_GET['addtime'] : time();

				$add['TimeDian'] = date("H", $add['addtime']);



				$add['LookTime'] = 0;
				$add['LookHeight'] = 1.00;

				$id = m('l_tj')->add($add);

				echo $_GET['jsoncallback'] . "(".json_encode(array('error'=>'0','id'=>$id)).")";
			}
		}

	}

	public function copy(){
		$add = array();
		$add['TjID'] = isset($_GET['TjID']) ? $_GET['TjID'] : '';
		$add['WID'] = isset($_GET['WID']) ? $_GET['WID'] : '';

		if($add['TjID'] != '' || $add['WID'] != ''){
			$add['times'] = isset($_GET['times']) ? $_GET['times'] : 0;
			$add['addtime'] = isset($_GET['addtime']) ? $_GET['addtime'] : time();
			$add['TimeDian'] = date("H", $add['addtime']);

			m('l_copy')->add($add);
			if($add['TjID'] != ''){
				m('l_tj')->where("id = '".$add['TjID']."'")->save(array('copy'=>'1'));
			}
		}
	}

	public function returns(){
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		if($id != ''){
			$return_add = array();
			if(isset($_GET['LookHeight']) && $_GET['LookHeight'] != ''){
				$return_add['LookHeight'] = $_GET['LookHeight'];
			}

			if(isset($_GET['LookTime']) && $_GET['LookTime'] != ''){
				$return_add['LookTime'] = $_GET['LookTime'];
			}

			if(!empty($return_add)){
				m('l_tj')->where("id = '".$id."'")->save($return_add);
			}
		}
	}


	public function unescape($str){ 
	    $ret = ''; 
	    $len = strlen($str); 
	    for ($i = 0; $i < $len; $i ++) { 
	        if ($str[$i] == '%' && $str[$i + 1] == 'u') { 
	            $val = hexdec(substr($str, $i + 2, 4)); 
	            if ($val < 0x7f) 
	                $ret .= chr($val); 
	            else  
	                if ($val < 0x800) 
	                    $ret .= chr(0xc0 | ($val >> 6)) . 
	                     chr(0x80 | ($val & 0x3f)); 
	                else 
	                    $ret .= chr(0xe0 | ($val >> 12)) . 
	                     chr(0x80 | (($val >> 6) & 0x3f)) . 
	                     chr(0x80 | ($val & 0x3f)); 
	            $i += 5; 
	        } else  
	            if ($str[$i] == '%') 
	            { 
	                $ret .= urldecode(substr($str, $i, 3)); 
	                $i += 2; 
	            } else 
	                $ret .= $str[$i]; 
	    } 
	    return $ret; 
	}

	function getOS(){
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);  
  
		if(strpos($agent, 'windows nt')) {
			$platform = 'windows';
		}elseif(strpos($agent, 'macintosh')) {
			$platform = 'mac';
		}elseif(strpos($agent, 'ipod')) {
			$platform = 'ipod';
		}elseif(strpos($agent, 'ipad')) {
			$platform = 'ipad';
		}elseif(strpos($agent, 'iphone')) {
			$platform = 'iphone';
		}elseif (strpos($agent, 'android')) {
			$platform = 'android';
		}elseif(strpos($agent, 'unix')) {
			$platform = 'unix';
		}elseif(strpos($agent, 'linux')) {
			$platform = 'linux';
		}else{
			$platform = 'other';
		}
		return $platform;  
	}

	function browse_info() {
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$br = $_SERVER['HTTP_USER_AGENT'];
			if (preg_match('/MSIE/i', $br)) {
				$br = 'MSIE';
			} else if (preg_match('/Firefox/i', $br)) {
				$br = 'Firefox';
			} else if (preg_match('/Chrome/i', $br)) {
				$br = 'Chrome';
			} else if (preg_match('/Safari/i', $br)) {
				$br = 'Safari';
			} else if (preg_match('/Opera/i', $br)) {
				$br = 'Opera';
			} else {
				$br = 'Other';
			}
			return $br;
		} else {
			return 'unknow';
		}
	}
}