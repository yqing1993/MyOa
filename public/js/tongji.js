// JavaScript Document


var data = {};//需要提交的data数据
data.id = randomString();//获取随机的字串符ID
localStorage.setItem("ID",data.id);//把ID存入终端，主要是用ID来识别用户是否复制了微信号
data.time = getNowFormatDate();//获取当前时间
data.province = remote_ip_info.province;//获得IP省份
data.city = remote_ip_info.city;//获得IP城市
data.from_url = document.referrer;//获得来源网址
data.now_url = window.location.href;//获得当前网址
var datastr = encodeURIComponent(JSON.stringify(data));
datastr = base64encode(datastr);



$(document).ready(function(){
	
	/*记录用户访问情况*/
	$.ajax({
		type:'get',
		url : 'http://121.42.46.35/index.php/tongji.html?data='+datastr,
		dataType : 'jsonp',
		jsonp:"jsoncallback",
		success  : function(msg) {
			alert(msg.id);
		},
		error : function() {
        }  
	});
	
	

	/*复制事件，访客复制了微信号，向数据库里面传输复制事件*/
	var copy = 0;
	function caculates(){
		if(copy!=0){//二次复制的话，就不用上传复制事件了；
			return;
		}
		var id = localStorage.getItem('ID');
		id = base64encode(id);
		$.ajax({
			type:'get',
			url : 'http://121.42.46.35/index.php/tongji/copy.html?id='+id+'&copy=1',
			dataType : 'jsonp',
			jsonp:"jsoncallback",
			success  : function(msg) {
				alert(msg.id);
			},
			error : function() {
			}  
		});
	}
});

	
/*得到当前日期时间*/
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    var strDate = _time(date.getDate());
	var hours = _time(date.getHours());
	var minutes = _time(date.getMinutes());
	var seconds = _time(date.getSeconds());
	
	
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + hours + seperator2 + minutes
            + seperator2 + seconds;
    return currentdate;
}

//时间前面加0
function _time(data){
	if(data>=0 && data<=9){
		data = "0" + data;
	}
	return data;
}

/*随机生成字串符*/
function randomString(len) {
	len = len || 32;
	var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
	var maxPos = $chars.length;
	var pwd = '';
	for (i = 0; i < len; i++) {
		pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
	}
	return pwd;
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

