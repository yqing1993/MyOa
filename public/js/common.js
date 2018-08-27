// JavaScript Document

function _type(data){
	if(data){
		if(data['type']=='alert'){
			//alert(data['data']['msg']);
			dd_alert(data['data']['msg'],data['data']['title'],data['data']['buttonname']);
			return;
		}
		
	}else{
		dd_alert("ajax返回的数据为空",'','');//如果服务器有弹窗提醒，那就运行弹窗提醒
	}
}

function dd_alert(msg,title,buttonname){
	alert(msg);return false;
	title = title==""?"提示":title;
	buttonname = buttonname==''?"确定":buttonname;
    DingTalkPC.device.notification.alert({
        message: msg,
        title: title,//可传空
        buttonName: buttonname,
        onSuccess : function() {
        },
        onFail : function(err) {}
    });
}

function chuli(val){
	if(val!=''){
		$('#J-bg').show();
		$('.J-bg-con').html(val);
		var con_width = $('.J-bg-con').width()/2;
		$('.J-bg-con').css('margin-left','-'+con_width+'px');
	}else{
		$('#J-bg').hide();
	}
}

function tishi(val){
	clearInterval(window.tishi_time);
	$('.tishi').html(val);
	
	var tishi_width = $('.tishi').width()+20;
	tishi_width = tishi_width/2;
	$('.tishi').css('margin-left','-'+tishi_width+'px');
	
	$('.tishi').fadeIn(500);
	window.tishi_time = setTimeout(function(){$('.tishi').fadeOut(500);},2000);
}

/*设置cookie*/
function setCookie(name,value,time){
	time = time!=''?time:3600;
	var exp = new Date();
	exp.setTime(exp.getTime() + time*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name){
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg)) return unescape(arr[2]);
	else return null;
}
function delCookie(name){
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval=getCookie(name);
	if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}


/*时间戳转时间*/
function getDates(num,type) {
    var currentdate = ''
	if(num>0){
	    var date = new Date(num*1000);
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
		

		if(type==1){
			currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
	            + " " + hours + seperator2 + minutes
	            + seperator2 + seconds;
		}else if(type==2){
			currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
		}else if(type==3){
			currentdate = month + seperator1 + strDate + " " + hours + seperator2 + minutes;
		}else if (type == 4){
			currentdate = hours + seperator2 + minutes + seperator2 + seconds;
		}
	}

    return currentdate;
}
//时间前面加0
function _time(data){
	if(data>=0 && data<=9){
		data = "0" + data;
	}
	return data;
}