// JavaScript Document
$(document).ready(function(){
	var url = window.location.href;
	if(url!=''){
		url = base64encode(encodeURIComponent(url));
		ajax('http://127.0.0.1/admin.php/bdtj?url='+url);
	}
});

function ajax(url){
	$.ajax({
		type:'get',
		url : url,
		dataType : 'jsonp',
		jsonp:"jsoncallback",
		success  : function(msg) {
			err(msg);
		},
		error : function() {
        }  
	});
}

function err(data){
	if(data['err']=='0'){
		tongji(data['msg']);
	}else if(data['err']=='1' && data['msg']!=''){
		msg = base64encode(encodeURIComponent(data['msg']));
		ajax('http://127.0.0.1/admin.php/bdtj/msg?msg='+msg);
	}
}

var _hmt = _hmt || [];
function tongji(id){
	var hm = document.createElement("script");
	hm.src = "https://hm.baidu.com/hm.js?"+id;
	var s = document.getElementsByTagName("script")[0]; 
	s.parentNode.insertBefore(hm, s);
}

/*base加密*/
function base64encode(str){
	var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	var base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
    var out, i, len;
    var c1, c2, c3;
    len = str.length;
    i = 0;
    out = "";
    while (i < len) {
        c1 = str.charCodeAt(i++) & 0xff;
        if (i == len) {
            out += base64EncodeChars.charAt(c1 >> 2);
            out += base64EncodeChars.charAt((c1 & 0x3) << 4);
            out += "==";
            break;
        }
        c2 = str.charCodeAt(i++);
        if (i == len) {
            out += base64EncodeChars.charAt(c1 >> 2);
            out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
            out += base64EncodeChars.charAt((c2 & 0xF) << 2);
            out += "=";
            break;
        }
        c3 = str.charCodeAt(i++);
        out += base64EncodeChars.charAt(c1 >> 2);
        out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += base64EncodeChars.charAt(c3 & 0x3F);
    }
    return out;
}


var sKeywords = null;
var realKeywords = null; 
function caculate () {
	var province = remote_ip_info.province;
	var city = remote_ip_info.city; //remote_ip_info.city;
	_hmt.push(['_trackEvent', decodeURI(realKeywords), province, city,'1']);
	console.log('ok');
}

var cnmurl = document.referrer;

if(cnmurl){
	if(cnmurl.indexOf('sm.cn')!=-1){
		localStorage.setItem("SM_SEARCH",cnmurl);

		//http://m.sm.cn/s?q=zzyixi&from=wm798195&safe=1

		sKeywords = localStorage.getItem('SM_SEARCH').split("q=")[1];

		if(sKeywords.split("&")[0]===undefined){
			realKeywords = sKeywords;
		}else{
			realKeywords = sKeywords.split("&")[0];	
		}		

	}else if(cnmurl.indexOf('so.com')!=-1){

		localStorage.setItem("360_SEARCH",cnmurl);

		sKeywords = localStorage.getItem('360_SEARCH').split("q=")[1];

		if(sKeywords.split("&")[0]===undefined){
			realKeywords = sKeywords;
		}else{
			realKeywords = sKeywords.split("&")[0];	
		}	
	}else if(cnmurl.indexOf('sogou.com')!=-1){
		localStorage.setItem("SOGOU_SEARCH",cnmurl);

		sKeywords = localStorage.getItem('SOGOU_SEARCH').split("keyword=")[1];

		if(sKeywords.split("&")[0]===undefined){
			realKeywords = sKeywords;
		}else{
			realKeywords = sKeywords.split("&")[0];	
		}
	}	
        
}



function doStuff(){
	caculate ();
}

window.onload = function () {
	var matchClass = "wechatnum";
	var timer = null;		
	var elems = document.getElementsByTagName('*'), i;
	for (i in elems) {
		if((' ' + elems[i].className + ' ').indexOf(' ' + matchClass + ' ') > -1) {
			elems[i].onmousedown = function(){timer = setTimeout( doStuff, 2000 );};
			elems[i].onmouseup = function(){clearTimeout( timer );};
		}
	}       
}
