var Action = 'Approve';
$(document).ready(function(){
	$("#myTab > li:first-child").addClass("active");
	$("#myTabContent > .tab-pane:first-child").addClass("active");
	//$('#Cheap-myModal').modal('show');

	/*打开modal窗口*/
	$(".modal_sub").click(function(){
		var now_id = active();
		$("#"+now_id+"-myModal").modal('show');
	});

    /*点击确定按钮*/
	$(".modal .modal-footer button").click(function(){
		var status = $(this).attr("data-value");
		var dom = $(this).parents('.modal');
		if(status!=''){
			Enter(status,dom);
		}
	});

	$("#myTab li").click(function(){
		$(this).find('.badge').html('');
		setTimeout(function(){
			post_data("/"+Action+"/"+active()+"Page/",{"operate":'page'},'','0');
		},50);
	});

	$('[data-toggle="collapse"]').each(function(i){
		$(this).attr('href','#collapse'+(i+1));
		$(this).parent().parent().parent().children(".panel-collapse").attr('id','collapse'+(i+1));
	});
});


function Enter(status,dom){

	var data = m_r_val(dom,'input[type="hidden"],textarea','obj');

	if(data.id == ''){
		toastr.error("出错，ID为空，请联系技术");
		return false;
	}

	data['type'] = active();
	data['operate'] = 'ApproveStatus';
	data['ApproveStatus'] = status;
	post_data("/"+Action+"/Data/",data,dom,'');

}

/*返回modal里面的值*/
function m_r_val(id,type,fs){
	if(fs=='obj'){
		var data = {};
		id.find(type).each(function(){
			var name = $(this).attr("name");
			if(name!=undefined && name!=''){
				val = $(this).val();
				data[name] = val;
			}
		});
	}else if(fs=='arr'){
		var data =  new Array();
		var i = 0;
		id.find(type).each(function(){
			var name = $(this).attr("name");
			if(name!=undefined && name!=''){
				var val = $(this).val();
				data[i] = [name,val];
				i++;
			}
		});
	}

	return data;
}

/*生成html*/
function list_html(data){
	var html = '';
	if(data['list']!=null && data['list'].length>0){
		var now_active = active();
		if(now_active == 'Cheap'){
			for(var i=0;i<data['list'].length;i++){

				/*生成赠品*/
				var Gift_html = '';
				if(data['list'][i]['GiftInfo'] != ''){
					var Gift_arr = JSON.parse(data['list'][i]['GiftInfo']);
					if(Gift_arr!=null && Gift_arr.length>0){
						for(var g_i=0;g_i<Gift_arr.length;g_i++){
							Gift_html += Gift_arr[g_i]['GiftName']+'：<b>'+Gift_arr[g_i]['num']+Gift_arr[g_i]['unit']+'</b><br/>';
						}
					}
				}

				html += '<tr data-id="'+data['list'][i]['id']+'"><td><input type="checkbox" /></td><td>'+data['list'][i]['id']+'</td><td>'+data['list'][i]['OrderID']+'</td><td>'+data['list'][i]['info']['ClientName']+'</td><td>'+Gift_html+'</td><td>'+data['list'][i]['CheapMoney']+'</td><td>'+data['list'][i]['info']['price']+'</td><td>'+get_obj_val(data['list'][i],'SellUserInfo.username')+'</td><td>'+getDates(data['list'][i]['addtime'],1)+'</td><td>'+json_parse_val(data['list'][i]['ApproveUserInfo'],'username')+'</td><td>'+getDates(data['list'][i]['ApproveTime'],1)+'</td><td>'+status_html(data['list'][i]['ApproveStatus'])+'</td><td><button class="btn btn-default btn-xs"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button><button class="btn btn-danger btn-xs hidden"><i class="glyphicon glyphicon-trash"></i><span>删除</span></button></td></tr>';
			}

		}else if(now_active == 'OrderTo'){
			for(var i=0;i<data['list'].length;i++){
				html += '<tr data-id="'+data['list'][i]['id']+'"><td><input type="checkbox" /></td><td>'+data['list'][i]['id']+'</td><td>'+data['list'][i]['OrderID']+'</td><td>'+data['list'][i]['info']['ClientName']+'</td><td>'+json_parse_val(data['list'][i]['SellUserInfo'],'username')+'</td><td>'+json_parse_val(data['list'][i]['SellIDInfo'],'WechatName')+'</td><td>'+data['list'][i]['username']+'</td><td>'+getDates(data['list'][i]['addtime'],1)+'</td><td>'+json_parse_val(data['list'][i]['ApproveUserInfo'],'username')+'</td><td>'+getDates(data['list'][i]['ApproveTime'],1)+'</td><td>'+status_html(data['list'][i]['ApproveStatus'])+'</td><td><button class="btn btn-default btn-xs"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button><button class="btn btn-danger btn-xs hidden"><i class="glyphicon glyphicon-trash"></i><span>删除</span></button></td></tr>';
			}

		}else if(now_active == 'Deposit' || now_active == 'Transfer'){
			for(var i=0;i<data['list'].length;i++){
				html += '<tr data-id="'+data['list'][i]['id']+'"><td><input type="checkbox" /></td><td>'+data['list'][i]['id']+'</td><td>'+data['list'][i]['OrderID']+'</td><td>'+data['list'][i]['info']['ClientName']+'</td><td>'+data['list'][i]['money']+'</td><td>'+json_parse_val(data['list'][i]['PaysType'],'value')+'</td><td>'+data['list'][i]['username']+'</td><td>'+getDates(data['list'][i]['addtime'],1)+'</td><td>'+json_parse_val(data['list'][i]['ApproveUserInfo'],'username')+'</td><td>'+getDates(data['list'][i]['ApproveTime'],1)+'</td><td>'+status_html(data['list'][i]['ApproveStatus'])+'</td><td><button class="btn btn-default btn-xs"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button><button class="btn btn-danger btn-xs hidden"><i class="glyphicon glyphicon-trash"></i><span>删除</span></button></td></tr>';
			}

		}else if(now_active == 'Service'){
			for(var i=0;i<data['list'].length;i++){
				html += '<tr data-id="'+data['list'][i]['id']+'"><td><input type="checkbox" /></td><td>'+data['list'][i]['id']+'</td><td>'+data['list'][i]['OrderID']+'</td><td>'+data['list'][i]['info']['ClientName']+'</td><td>'+json_parse_val(data['list'][i]['ServiceWay'],'value')+'</td><td>'+data['list'][i]['ReturnNum']+'</td><td>'+data['list'][i]['ReturnMoney']+'</td><td>'+data['list'][i]['username']+'</td><td>'+getDates(data['list'][i]['addtime'],1)+'</td><td>'+json_parse_val(data['list'][i]['ApproveUserInfo'],'username')+'</td><td>'+getDates(data['list'][i]['ApproveTime'],1)+'</td><td>'+status_html(data['list'][i]['ApproveStatus'])+'</td><td><button class="btn btn-default btn-xs"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button><button class="btn btn-danger btn-xs hidden"><i class="glyphicon glyphicon-trash"></i><span>删除</span></button></td></tr>';
			}
		}
	}

	append($("."+active()+"-table"),html);

	$(".tab-pane.active").find('.page-wrap .page').html(data.page);
	on();

}


/*添加内容到table里面*/
function append(dom,html){
	if(html==''){
		var td_num = dom.find("thead tr td").length;
		html = '<tr><td class="table-null" colspan="'+td_num+'"><i class="glyphicon glyphicon-question-sign"></i><span>暂无数据</span></td></tr>';
	}
	dom.children("tbody").html(html);
}

function on(){
	/*页数*/
	$(".page a").off("click").click(function(e){
		e.preventDefault();
		var num = $(this).attr("href");
		var arr = num.split("/");
		num = arr.pop().replace(".html","");
		if(num!='' && !isNaN(num)){
			post_data('/'+Action+'/'+active()+'Page/p/'+num,{"operate":'page'},'','');
			$(".tab-content-main").animate({scrollTop:0},300);
		}
	});

	/*td按钮*/
	$(".tab-table tbody tr td button").off("click").click(function(){
		var button = $(this).text();
		var data = {};
		data.id = $(this).parents("tr").attr("data-id");
		if(data.id!=''){
			if(button=='查看详情'){
				data.operate = 'Look';
				data.type = active();
				post_data('/'+Action+'/Data/',data,$(this),'0');
			}
		}else{
			toastr.error("获取的ID为空，无法操作，请联系技术人员");
		}
	});
}

/*处理返回的msg数据*/
function msg_chuli(data,msg,dom){
	chuli("");

	if(msg['msg'] != undefined){
		show_msg(msg);
	}

	if(data.operate=='page'){
		list_html(msg);
		ShowNum();
	}else if(data.operate=='Look'){
		if(msg['error']=='1'){

			var now_myModal = $('#'+data.type+'-myModal');
			var weizhi = new Array();

			if(msg.ApproveUserInfo != '') msg.ApproveUserInfo = JSON.parse(msg.ApproveUserInfo);

			now_myModal.find('[data-type="value"],input[type="hidden"]').each(function(){
				var name = $(this).attr("name");
				if(name != undefined){
					var val = get_obj_val(msg,name);
					if(name == 'Order.addtime' || name == 'Order.cztime' || name == 'addtime' || name == 'ApproveTime'){
						val = getDates(val,1);
					}

					val = (val == '' && weizhi.indexOf(name)>=0)?"未知":val;

					var tagName = $(this)[0].tagName;
					if(tagName == 'INPUT' || tagName == 'TEXTAREA'){
						$(this).val(val);
					}else{
						$(this).html(val);
					}
				}
			});

			/*结算信息*/
			var PriceInfo = '￥'+msg['Order']['price']+'（套餐价格）';
			PriceInfo += parseFloat(msg['Order']['SaleMoney']) > 0?' - ￥'+msg['Order']['SaleMoney']+'（'+msg['Order']['Sale']+'折优惠）':"";
			PriceInfo += parseFloat(msg['Order']['CheapMoney']) > 0?' - ￥'+msg['Order']['CheapMoney']+'（审批优惠）':"";
			PriceInfo += parseFloat(msg['Order']['RecommendMoney']) > 0?' - ￥'+msg['Order']['RecommendMoney']+'（推荐码优惠）':"";

			/*已付金额*/
			var yfMoney = '';
			yfMoney += parseFloat(msg['Order']['DepositMoney']) > 0?' + ￥'+msg['Order']['DepositMoney']+'（订金）':"";
			yfMoney += parseFloat(msg['Order']['TransferMoney']) > 0?' + ￥'+msg['Order']['TransferMoney']+'（转账）':"";
			yfMoney = yfMoney.replace(/^\s\+\s/,'');
			yfMoney = yfMoney=='' ? '￥0.00' : (yfMoney + ' = ￥'+(parseFloat(msg['Order']['DepositMoney']) + parseFloat(msg['Order']['TransferMoney'])));
			now_myModal.find('[name="yfMoney"]').html(yfMoney);
			
			/*还需支付*/
			var hxzf = parseFloat(msg['Order']['money']) - parseFloat(msg['Order']['DepositMoney']) - parseFloat(msg['Order']['TransferMoney']);
			var hxMoney = hxzf > 1 ? '￥'+(parseFloat(msg['Order']['money']) - parseFloat(msg['Order']['DepositMoney']) - parseFloat(msg['Order']['TransferMoney'])) : '￥0.00';
			now_myModal.find('[name="hxMoney"]').html(hxMoney);

			/*优惠券*/
			var PriceInfo_dom = now_myModal.find('[name="hxMoney"]').parent().parent().parent().parent().parent();
			PriceInfo_dom.find('.panel-heading h4 kbd').remove();
			if(parseFloat(msg['Order']['PreferentialMoney']) > 0){
				PriceInfo += ' - ￥'+msg['Order']['PreferentialMoney']+'（优惠券优惠）';
				PriceInfo_dom.find(".panel-heading h4").append('<kbd class="pull-right" style="background-color: #777;font-size:12px;"><i class="glyphicon glyphicon-barcode"></i> 优惠券 '+msg['Order']['preferential']+'</kbd>');
			}

			PriceInfo += ' = ￥'+msg['Order']['money'];
			now_myModal.find('[name="PriceInfo"] ').css('color', null).css('font-weight', null).html(PriceInfo);


			if(data.type == 'Cheap'){
				/*优惠券信息*/
				var PreferentialMoney = parseFloat(msg.PreferentialMoney) > 0 ? (msg.PreferentialMoney+"元") : "";
				var PreferentialCode = msg.PreferentialCode != '' ? ("（劵："+msg.PreferentialCode+"）") : "";
				now_myModal.find('[name="PreferentialMoney"]').html(PreferentialMoney + PreferentialCode);

				/*赠品写进去*/
				GiftHtml = '';
				if(msg.GiftInfo!=null && msg.GiftInfo.length>0){
					for(var i=0;i<msg.GiftInfo.length;i++){
						GiftHtml += '<tr><td>'+msg.GiftInfo[i]['GiftName']+'</td><td>'+msg.GiftInfo[i]['money']+'</td><td>'+msg.GiftInfo[i]['inventory']+'</td><td>'+msg.GiftInfo[i]['limit']+'</td><td>'+msg.GiftInfo[i]['ps']+'</td><td>'+msg.GiftInfo[i]['num']+'</td></tr>';
					}
					GiftHtml = '<thead><tr><td>赠品名称</td><td>市场价值</td><td>库存</td><td>单次上限</td><td>简介</td><td>申请数量</td></tr></thead><tbody>'+GiftHtml+'</tbody>';
				}
				now_myModal.find(".panel-gift-table").html(GiftHtml);
			}else if(data.type == 'OrderTo'){
				now_myModal.find('[name="SellUserName"]').html(json_parse_val(msg.SellUserInfo,'username'));
				now_myModal.find('[name="SellIDName"]').html(json_parse_val(msg.SellIDInfo,'WechatName'));
			}else if(data.type == 'Deposit' || data.type == 'Transfer'){
				now_myModal.find('[name="PaysType"]').html(json_parse_val(msg.PaysType,'value'));

				if(data.type == 'Transfer'){
					/*结算信息*/
					// var PriceInfo = '￥'+msg.Order['price']+'（套餐价格）';
					// PriceInfo += parseFloat(msg.Order['SaleMoney']) > 0?' - ￥'+msg.Order['SaleMoney']+'（'+msg.Order['Sale']+'折优惠）':"";
					// PriceInfo += parseFloat(msg.Order['CheapMoney']) > 0?' - ￥'+msg.Order['CheapMoney']+'（审批优惠）':"";
					// PriceInfo += parseFloat(msg.Order['RecommendMoney']) > 0?' - ￥'+msg.Order['RecommendMoney']+'（推荐码优惠）':"";
					// PriceInfo += parseFloat(msg.Order['PreferentialMoney']) > 0?' - ￥'+msg.Order['PreferentialMoney']+'（优惠券优惠）':"";
					// PriceInfo += parseFloat(msg.Order['DepositMoney']) > 0?' - ￥'+msg.Order['DepositMoney']+'（已付定金）':"";

					var sfje = msg.Order['money'] - msg.Order['DepositMoney'] - msg.Order['TransferMoney'];
					// PriceInfo += parseFloat(msg.Order['money']) > 0?' = ￥<b style="color:red;">'+sfje+'</b>（实付金额）':"";

					// now_myModal.find('[name="PriceInfo"]').html(PriceInfo);

					var TransferMoney = get_obj_val(msg,"money");
					var jieyu = TransferMoney - sfje;
					now_myModal.find('[name="hxMoney"]').css({'color':'red','font-weight':'bold'});

					/*用于转账支付审批 如果实付金额和转账金额不等情况下，隐藏同意按钮*/
					$('#Transfer-myModal').find('.modal-footer button[data-value="sptg"]').attr("disabled",false).html("同意申请");
					if(jieyu < -1){
						$('#Transfer-myModal').find('.modal-footer button[data-value="sptg"]').attr("disabled",true).html("实付金额和转账金额不等");
					}

				}
			}else if(data.type == 'Service'){
				now_myModal.find('[name="ServiceWay"]').html(json_parse_val(msg.ServiceWay,'value'));
			}


			/*审批状态写进去*/
			now_myModal.find('[name="ApproveStatus"]').html(status_html(msg.ApproveStatus));

			/*审批截图加载一下*/
			ImagePath = '';
			if(msg.ImagePath != ''){
				msg.ImagePath = JSON.parse(msg.ImagePath);
				if(msg.ImagePath != null && msg.ImagePath.length > 0){
					for(var i=0;i<msg.ImagePath.length;i++){
						msg.ImagePath[i]['path'] = msg.ImagePath[i]['path'].replace(/^\./,"");
						ImagePath += '<a target="_blank" href="'+msg.ImagePath[i]['path']+'" class="img-thumbnail"><img src="'+msg.ImagePath[i]['path']+'" /></a>';
					}
				}
			}
			now_myModal.find('[name="ImagePath"]').html(ImagePath);

			/*同意申请 拒绝申请 撤销审批 按钮显示隐藏*/
			if(msg.ApproveStatus != ''){
				msg.ApproveStatus = JSON.parse(msg.ApproveStatus);
				SetApproveStatus(now_myModal,msg.ApproveStatus.key);
			}

			/*textare里面内容清空*/
			now_myModal.find('[name="ApproveMsg"]').val("");
			

			/*开始加载留言*/
			post_data("/"+Action+"/OrderMsg/",{"operate":"OrderMsgList","OrderID":msg.OrderID},now_myModal,'0');

			/*开始加载操作记录*/
			post_data("/"+Action+"/OrderLog/",{"operate":"OrderLogList","OrderID":msg.OrderID},now_myModal,'0');


			now_myModal.modal('show');
			$(".modal-body-content").animate({scrollTop:0},300);
			on();

		}
		
	}else if(data.operate == 'OrderMsgList'){
		/*订单留言*/
		var html = '';
		if(msg.error == '1'){
			if(msg['list'] != null && msg['list'].length>0){
				for(var i=0;i<msg['list'].length;i++){
					html += '<dl><dt>'+msg['list'][i]['username']+' '+getDates(msg['list'][i]['addtime'],1)+'</dt><dd>'+msg['list'][i]['content']+'</dd></dl>';
				}
			}
		}
		dom.find('[name="OrderMsgList"]').html((html==''?'<p>暂无留言</p>':html));
	}else if(data.operate == 'OrderLogList'){
		/*订单详情里面的操作日志*/
		var html = '';
		if(msg.error == '1'){
			if(msg['list'] != null && msg['list'].length>0){
				for(var i=0;i<msg['list'].length;i++){
					html += '<dl><dt>'+msg['list'][i]['username']+' '+getDates(msg['list'][i]['addtime'],1)+'</dt><dd>'+msg['list'][i]['content']+'</dd></dl>';
				}
			}
		}
		dom.find('[name="OrderLogList"]').html((html==''?'<p>暂无日志</p>':html));
	}else if(data.operate == 'ApproveStatus'){
		if(msg.error == '1'){
			$('.modal').modal('hide');
			setTimeout(function(){
				post_data("/"+Action+"/"+active()+"Page/",{"operate":'page'},'','');
			},1000);
		}else if(msg.error == '2'){
			if(confirm(msg.confirm)){
				data.cf = 'YES';
				post_data("/"+Action+"/Data/",data,dom,'');
              }
		}
		//SetApproveStatus(dom,msg.ApproveStatus);
	}
}

/*编辑model框获取数据*/
function updata_get_data(name,data){
	var val = '';
	if(data!=null && data){
		for(var i in data){
			if(i == name){
				return data[i];
			}
		}
	}
	return val;
}

/*读取字串符json数据*/
function json_parse_val(obj,key){
	var val = "";
	if(obj!=''){
		obj = JSON.parse(obj);
		if(obj[key]!=null){
			val = obj[key];
		}
	}
	return val;
}

/*ajax*/
function post_data(url,data,dom,time){
	chuli("正在处理，请稍后……");
	time = (time!='')?time:500;

	setTimeout(function(){
		$.ajax({
			url: '/order.php'+url,
			type:"post",
			data: data,
			dataType:'json',
			success: function (msg) {
				msg_chuli(data,msg,dom);
			},
			error: function () {
				chuli("");
				toastr.error("提交数据，产生错误");
			}
		})
	},time);

}

function SetApproveStatus(dom,key){
	dom.find('.modal-footer button[data-value="sptg"]').show();
	dom.find('.modal-footer button[data-value="spbtg"]').show();
	dom.find('.modal-footer button[data-value="cxsp"]').hide();
	if(key == 'sptg' || key == 'spbtg'){
		dom.find('.modal-footer button[data-value="sptg"]').hide();
		dom.find('.modal-footer button[data-value="spbtg"]').hide();
		dom.find('.modal-footer button[data-value="cxsp"]').show();
	}
}


/*读取对象值*/
function get_obj_val(obj,name,str){
	val = '';

	arr = name.split(".");
	if(arr.length == 1){
		val = !nulls(obj[arr[0]])?obj[arr[0]]:"";
	}else if(arr.length == 2){
		val = (!nulls(obj[arr[0]]) && !nulls(obj[arr[0]][arr[1]]))?obj[arr[0]][arr[1]]:"";
	}else if(arr.length == 3){
		val = (!nulls(obj[arr[0]]) && !nulls(obj[arr[0]][arr[1]]) && !nulls(obj[arr[0]][arr[1]][arr[2]]))?obj[arr[0]][arr[1]][arr[2]]:"";
	}

	val = (val=='' && str != undefined && str!='')?str:val;

	return val;
}

function nulls(obj){
	if(obj != null){
		return false;
	}else{
		return true;
	}
}

/*加载数据*/
setTimeout(function(){
	if(active()){
		post_data("/"+Action+"/"+active()+"Page/",{"operate":'page'},'','0');

		/*开始轮询*/
		//ShowNum();
    	setInterval('ShowNum()',10000);
	}else{
		toastr.warning("您不是审批人");
	}
},100);


/*轮询*/
var ShowNum_dom = $("#myTab");
var ShowNumStart = null;
function ShowNum(){
	clearTimeout(ShowNumStart);
	ShowNumStart = setTimeout(function(){
		$.ajax({
			url: '/order.php/'+Action+'/ShowNum/',
			type:"post",
			data: {"type":"1"},
			dataType:'json',
			success: function (msg) {
				if(msg['error'] == '1'){
					if(msg['ShowNum'] != null && msg['ShowNum']){
						for(var i in msg['ShowNum']){
							ShowNum_dom.find('a[href="#'+i+'"] .badge').html((msg['ShowNum'][i]>0?msg['ShowNum'][i]:''));
						}
					}
				}
			},
			error: function () {
			}
		});
	},100);
}
/*toastr配置*/
//toastr.options = {positionClass: "toast-top-center"};


/*审批状态小图标*/
function status_html(status){
	var html = '';
	if(status!=''){
		var status = JSON.parse(status);
		if(status['key']!=null && status['key']!=''){
			if(status['key'] == 'spz'){
				html = 'warning';
			}else if(status['key'] == 'sptg'){
				html = 'success';
			}else if(status['key'] == 'spbtg'){
				html = 'danger';
			}else if(status['key'] == 'cxsp'){
				html = 'primary';
			}else if(status['key'] == 'wcl'){
				html = 'info';
			}

			html = '<button type="button" class="btn btn-'+html+' btn-xs">'+status['value']+'</button>';
		}
	}
	return html;
}

/*获得当前显示的tabID*/
function active(){
	var id = $(".tab-pane.active").attr("id");
	if(id == 'undefined'){
		return false;
	}
	return id;
}

/*处理返回的data数据*/
function show_msg(data){
	if(data.error=='0'){
		/*红色*/
		toastr.error(data.msg);
	}else if(data.error=='1'){
		/*绿色*/
		toastr.success(data.msg);
	}else if(data.error=='2'){
		/*橘黄色*/
		toastr.warning(data.msg);
	}else if(data.error=='4'){
		/*浅蓝色*/
		toastr.info(data.msg);
	}
}


/*计算tab-content距离高度*/
function js_height(){
	var height = $(".tab .tab-header-wrap").height();
	$(".tab .tab-content-wrap").css("padding-top",height+"px");
}
js_height();


/*首行冻结*/
$('.tab-content-main').scroll(function() {

	var header_height = $(".tab .tab-header-wrap").height();
	var thead_top = $('.tab-pane.active .tab-table tbody').prev().offset().top;
	var scrollTop = $(this).scrollTop() || $(this).get(0).scrollTop;
	
	var fixTable = $(".tab-pane.active .tab-pane-content .fixTable");
	if(fixTable.length){
		if(thead_top <= header_height){
			fixTable.css('top',(scrollTop-(scrollTop+thead_top)+header_height)+'px');
		}else{
			fixTable.css('top','0px');
		}
	}else{
		var table_html = $(".tab-pane.active .tab-table").attr("class");
		var thead_html = $(".tab-pane.active .tab-table thead").html();
		html = '<table class="'+table_html+' fixTable"><thead>'+thead_html+'</thead></table>';
		$(".tab-pane.active .tab-pane-content .tab-table").before(html);
		fixTable = $(".tab-pane.active .tab-pane-content .fixTable");
	}
});