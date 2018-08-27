// JavaScript Document
DingTalkPC.ready(function(res){
  /*{
      authorizedAPIList: ['device.notification.alert'], //已授权API列表
      unauthorizedAPIList: [''], //未授权API列表
  }*/
   // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
});

setTimeout(function(){
	if(DingTalkPC.userid==0){
		$.ajax({
			url: '/index.php/deltoken/',
			type:"post",
			data:{"id":"dd"},
			dataType:'json',
			timeout: 900,
			success: function (data) {
				if(data['error']=='0'){
					location.reload();
				}else{
					dd_alert("Token重置失败，请联系管理员");
				}
			},
			error: function () {
			}
		});
	}
},2500);


/*把员工id传到服务器，服务器根据员工id，来判断员工权限*/
function start(){
	if(DingTalkPC.userid!=0){
		$.ajax({
			url: '/index.php/daylog/userid.html',
			type:"post",
			data:{"id":DingTalkPC.userid},
			dataType:'json',
			timeout: 900,
			success: function (data) {
				_type(data);
				get_list();//请求日志列表
			},
			error: function () {
			}
		});
	}
}

$("#daylog_button").on("click",function(){
	doUpload();
});

function doUpload(){  
	var wenjian = $("#file").val();
	var beizhu = $("#beizhu").val();
	
	if(wenjian=='' && beizhu==''){
		dd_alert("内容和附件都为空，没有任何信息");
		return false;
	}
	
	chuli("正在提交日志，请稍后……");
	
	setTimeout(function(){
		var wj_state = 0;
		if(wenjian!=''){
			var formData = new FormData($( "#uploadForm" )[0]);
			wj_state = 1;
		}
		
		/*把备注文字加密，方便传输*/
		if(beizhu!=''){
			var beizhus = encodeURIComponent(JSON.stringify(beizhu));
			beizhus = base64encode(beizhus);
		}
		if(wj_state==1){//带文件上传
			$.ajax({
				url: '/index.php/daylog/upload?userid='+DingTalkPC.userid+'&beizhu='+beizhus+'&status='+wj_state,
				type: 'POST',
				data: formData,
				async: false,
				cache: false, 
				contentType: false,
				processData: false,
				dataType:"json",
				success: function (data) {
					succ(data);
					
				},
				error: function() { 
					chuli("");
					dd_alert("日志提交失败，可刷新页面重试，或联系技术人员"); 
				}
			});
		}else if(wj_state==0){//不带文件上传
			$.ajax({
				url: '/index.php/daylog/upload?userid='+DingTalkPC.userid+'&beizhu='+beizhus+'&status='+wj_state,
				type: 'POST',
				data: {"dd":"jj"},
				dataType:"json",
				success: function (data) {
					succ(data);
					
				},
				error: function() {
					chuli("");
					dd_alert("日志提交失败，可刷新页面重试，或联系技术人员"); 
				}
			});
		}
	},1000);
		
		
}

function succ(data){
	$("#file").val("");
	$("#beizhu").val("");
	$(".wjm").empty();
	chuli("");
	
	_type(data);
	dd_alert(data['msg']);
	
	get_list();
}

function get_list(page){
	page = page==""?1:page;
	$.ajax({
		url: '/index.php/daylog/getlist/p/'+page,
		type:"post",
		data:{"userid":DingTalkPC.userid},
		dataType:'json',
		timeout: 900,
		success: function (data) {
			_type(data);
		},
		error: function () {
		}
	});
}

function page(){
	$(".page a").on("click",function(){
		var url = $(this).attr("href");
		var page = url.split("/");
		page = page.pop().replace(".html", "");
		
		get_list(page);
		return false;
	});
}

/*验证json返回的数据类型*/
function _type(data){
	if(data){
		if(data['type']=='alert'){
			dd_alert(data['data']['msg'],data['data']['title'],data['data']['buttonname']);
			return;
		}
		
		if(data['type']=='daylog_title'){
			$(".daylog_title").empty().append(data['data']);
		}
		
		if(data['type']=='list'){
			_html(data['data']['q'],data['data']['d']);
			page();
			on();
		}
		
	}else{
		dd_alert("ajax返回的数据为空",'','');//如果服务器有弹窗提醒，那就运行弹窗提醒
	}
}

function _html(q,data){
	var html_str = "<center>日志列表为空</center>";
	
	var del = "";
	if(q=="1"){
		del = '<a href="javascript:;">删除</a>';
	}
	if(data['list']!=null){
		html_str = '';
		for(var i=0;i<data['list'].length;i++){
			html_str += '<li data-id="'+data['list'][i]["id"]+'"><div class="title">'+(data['list'][i]["upload_title"]!=""?data['list'][i]["upload_title"]:"没有附件")+'</div><div class="text">'+(data['list'][i]["beizhu"]!=""?"<a>查看</a><i>"+data['list'][i]["beizhu"]+"</i>":"")+'</div><div class="people">'+data['list'][i]["user_name"]+'</div><div class="time">'+getNowFormatDate(data['list'][i]["time"])+'</div><div class="operate">'+del+(data['list'][i]["upload_title"]!=""?' <a href="javascript:;" server-title="'+data['list'][i]["server_title"]+'" upload-title="'+data['list'][i]["upload_title"]+'">下载</a>':"")+'</div></li>';
		}
	}
	$(".daylog_list_info").empty().append(html_str);
	$(".page").empty().append(data['page']);
}

$("#file").change(function(){
	var val = $("#file").val()
	var val_arr = val.split('\\');
	$(".wjm").empty().append(val_arr.pop());
});

function on(){
	$(".daylog_list .daylog_list_info li .text a").off("mouseover");
	$(".daylog_list .daylog_list_info li .text a").off("mouseleave");
	$(".daylog_list .daylog_list_info li .operate a").off("click");
	
	
	$(".daylog_list .daylog_list_info li .text a").on("mouseover",function(){
		$(this).next().show();
	});
	$(".daylog_list .daylog_list_info li .text a").on("mouseleave",function(){
		$(this).next().hide();
	});
	
	$(".daylog_list .daylog_list_info li .operate a").on("click",function(){
		var state = $(this).html();
		if(state=="下载"){
			var server_title = $(this).attr("server-title");
			var name = $(this).attr("upload-title");
			download(server_title,name);
		}else if(state=="删除"){
			var id = $(this).parent().parent().attr("data-id");
			DingTalkPC.device.notification.confirm({
				message: "您确定删除这个文件吗？",
				title: "提示",
				buttonLabels: ['确定', '取消'],
				onSuccess : function(result) {
					if(result.buttonIndex==0){
						$.ajax({
							url: '/index.php/daylog/del/',
							type:"post",
							data:{"userid":DingTalkPC.userid,"id":id},
							dataType:'json',
							timeout: 900,
							success: function (data) {
								_type(data);
								get_list();
							},
							error: function () {
							}
						});
					}
				},
				onFail : function(err) {}
			});
		}
	});
}
function download(server_title,name){
	host = window.location.host;
	DingTalkPC.biz.util.downloadFile({
		url: 'http://'+host+'/index.php/daylog/download?filename='+server_title, //要下载的文件的url
		name: name, //定义下载文件名字
		onProgress: function(msg){
		},
		onSuccess : function(result) {
			dd_alert("下载成功");
		},
		onFail : function() {}
	})
}
	
/*得到当前日期时间*/
function getNowFormatDate(num) {
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
	