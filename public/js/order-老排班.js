var Action = 'Order';
$(document).ready(function(){
	if(web == 'OrderPost'){
		$('#OrderList-myModal').modal('show');
	}

	/*打开modal窗口*/
	$(".modal_sub").click(function(){
		var now_id = active();
		$("#"+now_id+"-myModal").modal('show');
	});

    /*提交订单点击确定按钮*/
	$("#PostOrder").click(function(){
		var id = $(this).parents(".modal").attr("id");
		Enter(id);
	});

	/*修改订单点击确定修改按钮*/
	$("#UpdateOrder").click(function(){
		UpdateOrder();

		var id = $(this).parents(".modal").find('input[type="hidden"][name="OrderID"]').val();
		if(id!=''){
			setTimeout(function(){
				tr_update(id);
			},500);
		}
	});

	/*增加上传图片*/
	$(".addfile").click(function(){
		var count = $(this).parent().prev().children("p").length;
		if(count<5){
			$(this).parent().prev().append('<p><input type="file" class="form-control" accept="image/jpg,image/png,image/gif,image/jpeg"><button type="button" class="close" onClick="close_file(this)">×</button></p>');
		}else{
			toastr.warning("图片上传不得超过5张");
		}
		
	});

	/*留言显示*/
	$('[data-toggle="popover"]').popover({html : true});
	// $('.tab-content').scroll(function() {
	// 	$('[data-toggle="popover"]').popover('hide');
	// });

	/*货到付款，隐藏付款状态*/
	$('#OrderList-UpdateModal input[name="PayType"]').change(function(){
		var val = $('#OrderList-UpdateModal input[name="PayType"]:checked').val();
		if(val == 'hdfk'){
			$(this).parents(".form-horizontal").children(".PayStatus").hide();
		}else{
			$(this).parents(".form-horizontal").children(".PayStatus").show();
		}
	});

	/*点击搜索*/
	$("#search-sub").click(function(){
		var searchs = search_data($('.tab-search'))
		if(searchs){
			post_data("/"+Action+"/"+active()+"Page/",searchs,'','');
		}
	});

	/*展开其他搜索，更新首行缩进*/
	$(".tab-search a.collapsed").click(function(){
		setTimeout(function(){
			scroll($('.tab-content-main'));
		},400);
	});
});
/*展现表格样式*/
var now_table = TableStyle(1);
function TableStyle(type){
	var style = getCookie('TableStyle');
	style = (style == null || style=='') ? 'order-table' : style;
	if(type==1){
		now_table = style;
		$('.order-table,.orders-table').hide();
		$('.'+now_table).show();
		return style;
	}else{
		style = style == 'order-table' ? 'orders-table' : 'order-table';
		now_table = style;
		$('.order-table,.orders-table').hide();
		$('.'+now_table).show();

		setCookie('TableStyle',style,'');

		var searchs = search_data($('.tab-search'));
		if(searchs){
			post_data('/'+Action+'/'+active()+'Page/p/'+window.PageNum+'/',searchs,'','');
			$(".tab-content-main").animate({scrollTop:0},300);
		}
	}
}

/*搜索*/
function search_data(dom){
	var data = search_val(dom,'input,select');

	if(data.ClientName!=null && data.ClientName!='' && data.ClientName.length<2){
		toastr.warning("搜索时客户姓名最少两个字");
		return false;
	}

	if(data.tel!=null && data.tel!='' && data.tel.length<4){
		toastr.warning("搜索时联系电话最少四位数");
		return false;
	}

	if(data.TimeType != null && (data.StartTime==null || data.OverTime==null)){
		toastr.warning("请选择开始时间或结束时间");
		return false;
	}

	if(data.TimeType==null && (data.StartTime!=null || data.OverTime!=null)){
		toastr.warning("请选择时间类型");
		return false;
	}

	data.operate = 'page';
	return data;
}

/*小祥尽然私聊小红红，说我太拼了，我只想说：人活在世上，一言既出，驷马难追，诚信做人，答应别人的事情，要尽最大努力！！！*/

/*更新订单*/
function UpdateOrder(){
	var dom = $("#OrderList-UpdateModal");
	var data = {};
	data.id = dom.find('input[type="hidden"][name="OrderID"]').val();
	if(data.id==''){
		toastr.error("出错，ID为空，请联系技术");
		return false;
	}

	/*获得订单状态、快递公司、快递单号、运费*/
	var order_info = m_r_val(dom.find("#head-info"),'input[type="text"],select','obj');
	if(order_info.OrderStatus == null){
		toastr.error("订单状态不能为空");
		return false;
	}

	if(order_info.Express != 'zwkd' && order_info.ExpressNum == ''){
		toastr.error("请选择该单号的快递公司");
		return false;
	}

	/*获得客户信息*/
	var client_info = m_r_val(dom.find("#client-info"),'input[type="text"],input[type="radio"],select,textarea','obj');


	if(client_info.ClientName == null || client_info.ClientName == ''){
		toastr.warning("客户姓名不能为空");
		return false;
	}
	
	if(client_info.tel == null || client_info.tel == ''){
		toastr.warning("客户联系电话不能为空");
		return false;
	}else{
		client_info.tel = client_info.tel.replace(/(^\s+|\s+$)/g,"");
		if(!/^1[3|4|5|7|8]\d(\d{4}|\*{4})\d{4}$/.test(client_info.tel)){
			toastr.warning("手机格式不正确");
			return false;
		}
	}
	
	if(client_info.provice == null || client_info.provice == ''){
		toastr.warning("收件省份不能为空");
		return false;
	}
	
	if(client_info.city == null || client_info.city == ''){
		toastr.warning("收件城市不能为空");
		return false;
	}

	if(client_info.address == null || client_info.address == ''){
		toastr.warning("详细地址不能为空");
		return false;
	}

	if(client_info.sex == null || client_info.sex == ''){
		toastr.warning("请选择性别");
		return false;
	}

	if(client_info.age == null || client_info.age == ''){
		toastr.warning("请选择年龄");
		return false;
	}

	if(client_info.FriendType == null || client_info.FriendType == ''){
		toastr.warning("请选择好友类型");
		return false;
	}
	
	/*获得订单支付信息*/
	var pay_info = m_r_val(dom.find("#pay-info"),'input[type="radio"]','obj');

	if(pay_info.PayType == null || pay_info.PayType == ''){
		toastr.warning("请选择付款方式");
		return false;
	}
	
	if(pay_info.PayType == null || pay_info.PayType == ''){
		toastr.warning("请选择付款方式");
		return false;
	}

	for(var i in order_info){
		data[i] = order_info[i];
	}

	for(var i in client_info){
		data[i] = client_info[i];
	}

	for(var i in pay_info){
		data[i] = pay_info[i];
	}

	data['operate'] = 'UpdateOrder';
	post_data("/"+Action+"/"+active()+"Data/",data,'','');

}


function Enter(id){

	var data = m_r_val($("#"+id+" .form-horizontal"),'input[type="hidden"],input[type="text"],textarea,select','obj');

	if(data.id == ''){
		toastr.error("出错，ID为空，请联系技术");
		return false;
	}

	if(data.SellUser == null || data.SellUser == ''){
		toastr.warning("请选择销售人员");
		return false;
	}
	
	if(data.SellID == null || data.SellID == ''){
		toastr.warning("请选择销售号");
		return false;
	}
	
	if(data.SellProduct == null || data.SellProduct == ''){
		toastr.warning("请选择销售产品");
		return false;
	}
	
	if(data.SellMeal == null || data.SellMeal == ''){
		toastr.warning("请选择产品套餐");
		return false;
	}

	data.PayType = $("#"+id+" .form-horizontal").find('input[name="PayType"]:checked').val();
	if(data.PayType == null || data.PayType == ''){
		toastr.warning("请选择付款方式");
		return false;
	}

	if(data.ClientName == null || data.ClientName == ''){
		toastr.warning("客户姓名不能为空");
		return false;
	}
	
	if(data.tel == null || data.tel == ''){
		toastr.warning("客户联系电话不能为空");
		return false;
	}else{
		data.tel = data.tel.replace(/(^\s+|\s+$)/g,"");
		if(!/^1[3|4|5|7|8][0-9]{9}$/.test(data.tel)){
			toastr.warning("手机格式不正确");
			return false;
		}
	}
	
	if(data.provice == null || data.provice == ''){
		toastr.warning("收件省份不能为空");
		return false;
	}
	
	if(data.city == null || data.city == ''){
		toastr.warning("收件城市不能为空");
		return false;
	}

	if(data.address == null || data.address == ''){
		toastr.warning("详细地址不能为空");
		return false;
	}

	data.sex = $("#"+id+" .form-horizontal").find('input[name="sex"]:checked').val();
	if(data.sex == null || data.sex == ''){
		toastr.warning("请选择性别");
		return false;
	}

	if(data.FriendType == null || data.FriendType == ''){
		toastr.warning("请选择好友类型");
		return false;
	}

	if(data.age == null || data.age == ''){
		toastr.warning("请选择年龄");
		return false;
	}

	data['operate'] = 'UpdateAdd';
	post_data("/"+Action+"/"+active()+"Data/",data,'','');

}

window.PageNum = 1;
function on(){
	/*页数*/
	$(".page a").off("click").click(function(e){
		e.preventDefault();
		var num = $(this).attr("href");
		var arr = num.split("/");
		num = arr.pop().replace(".html","");
		if(num!='' && !isNaN(num)){
			window.PageNum = num;
			var searchs = search_data($('.tab-search'));
			if(searchs){
				post_data('/'+Action+'/'+active()+'Page/p/'+window.PageNum+'/',searchs,'','');
				$(".tab-content-main").animate({scrollTop:0},300);
			}
		}
	});

	/*td按钮*/
	$(".tab-table tbody tr td button").off("click").click(function(){
		var button = $(this).text();
		var data = {};
		data.id = $(this).parents("tr").attr("data-id");
		if(data.id == null || data.id == ''){
			data.id = $(this).parents("tr").parents("tr").attr("data-id");
		}
		if(data.id!=''){
			if(button=='查看详情' || button=='查看'){
				LookInfo(data.id);
				setTimeout(function(){
					$("#order-info").scrollTop(0);
				},500);
			}
		}else{
			toastr.error("获取的ID为空，无法操作，请联系技术人员");
		}
	});

	/*订单状态修改按钮*/
	$('.order-table select[name="OrderStatus"],.orders-table select[name="OrderStatus"]').off("change").change(function(){
		if(confirm('确定修改订单状态吗？')){
			var data = {};
			data.id = $(this).parents("tr").attr("data-id");
			if(data.id == null || data.id == ''){
				data.id = $(this).parents("tr").parents("tr").attr("data-id");
			}
			
			if(data.id == null || data.id==''){
				toastr.error("没有获取到订单ID，请刷新页面重试，或者联系技术");
				return false;
			}

			data.status = $(this).val();
			if(data.status == null || data.status ==''){
				toastr.error("没有获取到当前状态，请刷新页面重试，或者联系技术");
				return false;
			}
			data.operate = 'SetStatus';
			post_data('/'+Action+'/'+active()+'Data/',data,'','0');

			setTimeout(function(){
				tr_update(data.id);
			},50);
		}
		
	});

	/*订单留言点击，加载新的留言*/
	$(".order-table button.msg,.orders-table button.msg").off("mouseover").mouseover(function(){
		$(this).attr("data-content","<p><center>正在加载……</center></p>");
		var OrderID = $(this).parents("tr").attr("data-id");
		post_data("/"+Action+"/OrderMsg/",{"operate":"OrderMsgLists","OrderID":OrderID},$(this),'0');
	});

	$('[data-toggle="popover"]').popover({html : true});

	/*发送短信按钮*/
	$(".msg-button.btn-warning").click(function(){
		var data = {};
		data.Top = $(this).find("span").text();
		$(this).find("span").text("发送中…");
		$(this).attr("disabled",true);

		data.id = $(this).parents("tr").attr("data-id");
		if(data.id == null){
			toastr.error("订单编号获取失败，请联系技术");
			return false;
		}

		data.MsgID = $(this).attr("data-id");
		if(data.MsgID == null){
			toastr.error("短信ID获取失败，请联系技术");
		}

		data.operate = 'PostMsg';
		post_data('/'+Action+'/'+active()+'Data/',data,$(this),'0');
	});
}

/*获取订单详情*/
function LookInfo(OrderID){
	if(OrderID!=null && OrderID!=''){
		gift_on();//赠品按钮点击，加载赠品
		var data = {};
		data.id = OrderID;
		data.operate = 'LookInfo';
		post_data('/'+Action+'/'+active()+'Data/',data,'','0');

		$("#OrderMsgList").html('正在加载……');
		$("#OrderLog").html('正在加载……');
	}else{
		toastr.error("订单ID为空，无法加载订单详情");
	}
}

/*处理返回的msg数据*/
function msg_chuli(data,msg,dom){
	chuli("");

	if(msg['msg'] != undefined){
		show_msg(msg);
	}

	if(data.operate=='page' || data.operate == 'OneUpdate'){
		var html = list_html(msg);

		if(data.operate=='page'){
			append($("."+now_table),html);
			$(".tab-pane.active").find('.page-wrap .page').html(msg.page);
		}else if(data.operate == 'OneUpdate'){
			html = html.replace(/^<tr data-id="[^>]*/,"");
			html = html.replace(/<\/tr>$/,"");
			$("."+now_table).find('tr[data-id="'+data.id+'"]').html(html);
		}
		on();
	}else if(data.operate=='UpdateAdd'){
		if(msg['error']=='1'){
			$('.modal').modal('hide');
			setTimeout(function(){
				var searchs = search_data($('.tab-search'))
				if(searchs){
					post_data("/"+Action+"/"+active()+"Page/",searchs,'','');
				}
				
			},1000);
		}
	}else if(data.operate=='LookInfo'){
		if(msg['error']=='1'){
			var now_active = active();
			var dom = $("#"+now_active+"-UpdateModal");

			var weizhi = new Array('SellUserInfo.username');

			dom.find('[data-type="value"],input[type="text"],input[type="hidden"],textarea').each(function(){
				var name = $(this).attr("name");
				if(name != undefined){
					var val = get_obj_val(msg,name);

					if(name == 'addtime' || name == 'cztime' || name == 'fhtime'){
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

			/*订单状态*/
			var status_html = OrderStatus_html(msg['OrderStatus'],msg['OrderStatusNaxt']);
			dom.find('select[name="OrderStatus"]').html(status_html);

			/*赠品*/
			var GiftHtml = '';
			if(msg['Gift'] != null && msg['Gift'].length>0){
				for(var g=0;g<msg['Gift'].length;g++){
					GiftHtml += '<tr><td>'+msg['Gift'][g]['GiftName']+'</td><td>'+msg['Gift'][g]['money']+'</td><td>'+msg['Gift'][g]['num']+msg['Gift'][g]['unit']+'</td><td>'+msg['Gift'][g]['ps']+'</td><td>'+msg['Gift'][g]['limit']+'</td><td>'+msg['Gift'][g]['inventory']+'</td></tr>';
				}
				GiftHtml = GiftHtml!=''?'<thead><tr><td>赠品名称</td><td>市场价值</td><td>数量</td><td>简介</td><td>单次上限</td><td>库存</td></tr></thead><tbody>'+GiftHtml+'</tbody>':"";
			}
			dom.find('[name="Gift-table"]').html(GiftHtml);

			/*写入省份城市*/
			provice_html(dom.find('select[name="provice"]'),get_obj_val(msg,'provice'),get_obj_val(msg,'city'));


			/*性别勾选*/
			dom.find('input[name="sex"]').prop('checked',false);
			dom.find('input[name="sex"][value="'+msg['sex']+'"]').prop('checked',true);

			/*年龄选定*/
			dom.find('select[name="age"]').val(msg['age']);
			/*好友类型选定*/
			dom.find('select[name="FriendType"]').val(msg['FriendType']);
			/*选定快递公司*/
			dom.find('select[name="Express"]').val(msg['Express']);
			/*付款方式勾选*/
			dom.find('input[name="PayType"]').prop('checked',false);
			dom.find('input[name="PayType"][value="'+msg['PayType']+'"]').prop('checked',true);
			/*付款状态勾选*/
			dom.find('input[name="PayStatus"]').prop('checked',false);
			dom.find('input[name="PayStatus"][value="'+msg['PayStatus']+'"]').prop('checked',true);

			/*结算信息*/
			var PriceInfo = '￥'+msg['price']+'（套餐价格）';
			PriceInfo += parseFloat(msg['CheapMoney']) > 0?' - ￥'+msg['CheapMoney']+'（审批优惠）':"";
			PriceInfo += parseFloat(msg['RecommendMoney']) > 0?' - ￥'+msg['RecommendMoney']+'（推荐码优惠）':"";
			PriceInfo += parseFloat(msg['PreferentialMoney']) > 0?' - ￥'+msg['PreferentialMoney']+'（优惠券优惠）':"";

			var money = parseFloat(msg['price']) - parseFloat(msg['CheapMoney']) - parseFloat(msg['RecommendMoney']) - parseFloat(msg['PreferentialMoney']);

			PriceInfo += ' = ￥'+money.toFixed(2);
			dom.find('[name="PriceInfo"] .checkbox').html(PriceInfo);

			/*已付金额*/
			var yfMoney = '';
			yfMoney += parseFloat(msg['DepositMoney']) > 0?'￥'+msg['DepositMoney']+'（订金）':"";
			yfMoney += parseFloat(msg['TransferMoney']) > 0?((yfMoney!=''?' + ':'')+'￥'+msg['TransferMoney']+'（转账）'):"";
			yfMoney = yfMoney==''?'￥0.00':yfMoney;
			dom.find('[name="yfMoney"] .checkbox').html(yfMoney);
			

			/*还需支付*/
			var hxMoney = '￥'+msg['money'];
			dom.find('[name="hxMoney"] .checkbox').html(hxMoney);


			/*加载审批状态*/
			$('#approve-panel').find('.panel-heading button').remove();
			if(msg['Approve']!=null){
				for(var a in msg['Approve']){
					if(msg['Approve'][a]!=null && msg['Approve'][a].length>0){
						var html = '';
						for(var i=0;i<msg['Approve'][a].length;i++){
							html += status_htmls(msg['Approve'][a][i]['ApproveStatus']);
						}
						$('#approve-panel').find('#'+a+'-heading h4').append(html);
					}
				}
			}

			/*审批panel里面value清空，收缩起来*/
			$("#approve-panel").find('input[type="text"],input[type="file"],textarea').val("");
			$("#approve-panel").find(".collapse").collapse('hide');



			$('#'+active()+'-UpdateModal').modal('show');
			on();

			/*开始加载留言*/
			post_data("/"+Action+"/OrderMsg/",{"operate":"OrderMsgList","OrderID":data.id},'','0');

			/*开始加载操作记录*/
			post_data("/"+Action+"/OrderLog/",{"operate":"OrderLogList","OrderID":data.id},'','0');

		}
	}else if(data.operate=='AddMsg'){
		/*开始加载留言*/
		post_data("/"+Action+"/OrderMsg/",{"operate":"OrderMsgList","OrderID":data.id},'','0');
	}else if(data.operate=='GetGift'){
		var html = '';
		if(msg['error']=='1'){
			if(msg['list'] != null && msg['list'].length>0){
				for(var i=0;i<msg['list'].length;i++){
					html += '<tr data-id="'+msg['list'][i]['id']+'"><td>'+get_obj_val(msg['list'][i],'GiftName')+'</td><td>'+get_obj_val(msg['list'][i],'money')+'</td><td>'+get_obj_val(msg['list'][i],'inventory')+get_obj_val(msg['list'][i],'unit')+'</td><td>'+get_obj_val(msg['list'][i],'limit')+get_obj_val(msg['list'][i],'unit')+'</td><td>'+get_obj_val(msg['list'][i],'ps')+'</td><td><input type="text" class="td-input" name="GiftNum" data-limit="'+get_obj_val(msg['list'][i],'limit')+'" placeholder="数量"></td></tr>';
				}

				/*解绑点击事件，避免多次加载*/
				$("#Cheap-heading a").off("click");
			}
		}

		append($("#Cheap-collapse .panel-gift-table"),html);
	}else if(data.operate == 'OrderMsgList'){
		/*订单留言*/
		var html = '';
		if(msg.error == '1'){
			if(msg['list'] != null && msg['list'].length>0){
				for(var i=0;i<msg['list'].length;i++){
					html += '<dl><dt>'+msg['list'][i]['username']+' '+getDates(msg['list'][i]['addtime'],1)+'</dt><dd>'+msg['list'][i]['content']+'</dd></dl>';
				}
			}

			$('#order-info textarea[name="content"]').val('');
		}
		$("#OrderMsgList").html((html==''?'<p>暂无留言</p>':html));
	}else if(data.operate == 'OrderMsgLists'){
		/*订单标题里面的操作日志*/
		var html = '';
		if(msg.error == '1'){
			html = OrderMsg_html(msg['list'],'2');
		}
		dom.attr("data-content",(html==''?'<p>暂无留言</p>':html));
	}else if(data.operate == 'OrderLogList'){
		/*订单详情里面的操作日志*/
		var html = '';
		if(msg.error == '1'){
			html = OrderMsg_html(msg['list'],'1');
		}
		$("#OrderLogList").html((html==''?'<p>暂无日志</p>':html));
	}else if(data.operate == 'ApproveAdd'){
	

		/*重新加载操作日志*/
		post_data("/"+Action+"/OrderLog/",{"operate":"OrderLogList","OrderID":data.id},'','0');

		/*更新订单状态*/
		tr_update(data.id);

	}else if(data.operate == 'UpdateOrder'){
		/*更新了订单信息，重新加载*/
		LookInfo(data.id);
	}else if(data.operate == 'PostMsg'){
		/*发送短信*/
		if(msg.error == '2'){
			if(confirm(msg.confirm)){
				data.chongfu = 'YES';
				post_data('/'+Action+'/'+active()+'Data/',data,dom,'0');
			}else{
				dom.find("span").text(data.Top);
				dom.attr("disabled",false);
			}
		}else if(msg.error == '1'){
			dom.find("span").text("发送成功");
			dom.attr("disabled",false).removeClass("btn-warning").addClass("btn-success");
		}else{
			dom.find("span").text(data.Top);
			dom.attr("disabled",false);
		}
	}
}

/*订单留言生成代码*/
function OrderMsg_html(data,type){

	var html = '';
	if(data != null && data.length>0){
		if(type=='1'){
			for(var i=0;i<data.length;i++){
				html += '<dl><dt>'+data[i]['username']+' '+getDates(data[i]['addtime'],1)+'</dt><dd>'+data[i]['content']+'</dd></dl>';
			}
		}else if(type=='2'){
			for(var i=0;i<data.length;i++){
				html += '<p><strong>'+data[i]['username']+'：</strong>'+data[i]['content']+'</p>';
			}
		}
	}
	return html;
}

/*今天晚上搁公司里面加班，小刀给我订餐了，很感谢的有没有，恩恩，有*/

/*生成html*/
function list_html(data){
	var html = '';
	if(data['list']!=null && data['list'].length>0){
		if(now_table == 'order-table'){
			for(var i=0;i<data['list'].length;i++){
				var status_html = OrderStatus_html(data['list'][i]['OrderStatus'],data['list'][i]['OrderStatusNaxt']);

				var ExpressInfo = get_obj_val(data['list'][i],'ExpressInfo.value');
				if(ExpressInfo!=''){
					var ExpressNum = get_obj_val(data['list'][i],'ExpressNum')
					ExpressInfo = ExpressInfo + ':' + (ExpressNum!=''?ExpressNum:"未知");
				}

				/*留言*/
				var msg_html = data['list'][i]['OrderMsg']!=0?'<button type="button" class="btn btn-link btn-xs msg" data-trigger="focus" data-toggle="popover" data-placement="bottom" title="订单留言" data-content="<p><center>正在加载……</center><p>"><i class="glyphicon glyphicon-comment"></i>留言</button>':'';

				/*短信模块*/
				var msg_button = '';
				if(data['list'][i]['Message'] != null){
					var MessageCount = (data['list'][i]['MessageCount'] != null && data['list'][i]['MessageCount'] != '' && parseFloat(data['list'][i]['MessageCount']) > 0) ? (" +"+data['list'][i]['MessageCount']) : "";

					msg_button = '<button class="btn btn-warning btn-xs msg-button" type="button" data-id="'+get_obj_val(data['list'][i],'Message.id')+'"><i class="glyphicon glyphicon-envelope"></i> <span>'+get_obj_val(data['list'][i],'Message.MessageName')+MessageCount+'</span></button>';
				}

				/*结算信息*/
				var jsxx = '';
				jsxx += parseFloat(data['list'][i]['CheapMoney']) > 0?'<tr><td>审批优惠：</td><td>- '+data['list'][i]['CheapMoney']+'元</td></tr>':"";
				jsxx += parseFloat(data['list'][i]['RecommendMoney']) > 0?'<tr><td>推荐码优惠：</td><td>- '+data['list'][i]['RecommendMoney']+'元</td></tr>':"";
				jsxx += parseFloat(data['list'][i]['PreferentialMoney']) > 0?'<tr><td>优惠券优惠：</td><td>- '+data['list'][i]['PreferentialMoney']+'元</td></tr>':"";
				/*已付金额*/
				jsxx += parseFloat(data['list'][i]['DepositMoney']) > 0?'<tr><td>已付金额：</td><td>- '+data['list'][i]['DepositMoney']+'元</td></tr>':"";

				/*审批*/
				var ApproveHtml = '';
				if(data['list'][i]["Approve"] != null){
					for(var t in data['list'][i]["Approve"]){
						if(data['list'][i]["Approve"][t] != null && data['list'][i]["Approve"][t] != '' && data['list'][i]["Approve"][t].length>0){
							for(var n=0;n<data['list'][i]["Approve"][t].length;n++){
								ApproveHtml += '<tr><td>'+t+'</td><td>'+status_htmls(data['list'][i]["Approve"][t][n]['ApproveStatus'])+'</td>';
							}
						}
					}
					ApproveHtml = ApproveHtml!=''?'<table class="dl-table Approve-table">'+ApproveHtml+'</table>':"";
				}

				/*赠品*/
				var GiftHtml = '';
				if(data['list'][i]['Gift'] != null && data['list'][i]['Gift'].length>0){
					for(var g=0;g<data['list'][i]['Gift'].length;g++){
						if(data['list'][i]['Gift'][g]['GiftInfo']!=''){
							var Gift_arr = JSON.parse(data['list'][i]['Gift'][g]['GiftInfo']);
							if(Gift_arr!=null && Gift_arr.length>0){
								for(var gg=0;gg<Gift_arr.length;gg++){
									GiftHtml += '<tr><td>'+Gift_arr[gg]['GiftName']+'：</td><td>'+Gift_arr[gg]['num']+Gift_arr[gg]['unit']+'</td></tr>';
								}
							}
						}
					}
					GiftHtml = GiftHtml!=''?'<table class="dl-table order-gift-table">'+GiftHtml+'</table>':"";
				}


				html += '<tr data-id="'+data['list'][i]['OrderID']+'"><td colspan="9"><div class="panel panel-default"><div class="panel-heading clearfix"'+(data['list'][i]['color']!=''?('style="background-color:'+data['list'][i]['color']+';"'):'')+'><ul class="list-inline order-info"><li><input type="checkbox" /></li><li>订单编号：'+data['list'][i]['OrderID']+'</li><li>提交时间：'+getDates(data['list'][i]['addtime'],1)+'</li><li>销售人：'+get_obj_val(data['list'][i],'SellUserInfo.username','未知')+'</li><li>销售号：'+get_obj_val(data['list'][i],'SellIDInfo.WechatName','未知')+'</li><li>'+ExpressInfo+'</li><li>'+msg_html+'</li><li>'+msg_button+'</li></ul><div class="delete" title="移到回收站"><i class="glyphicon glyphicon-trash"></i></div></div><table class="tab-table order-table"><tbody><tr><td></td><td><table class="dl-table"><tr><td>商品名称：</td><td>'+get_obj_val(data['list'][i],'SellProductInfo.ProductName')+'</td></tr><tr><td>套餐名称：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.MealName')+'</td></tr><tr><td>售价：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.money')+'元</td></tr><tr><td>数量：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.number')+get_obj_val(data['list'][i],'SellMealInfo.unit')+'</td></tr><tr><td>天数：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.DayNum')+'天</td></tr></table></td><td>'+GiftHtml+'</td><td><table class="dl-table"><tr><td>收件人：</td><td>'+data['list'][i]['ClientName']+'</td></tr><tr><td>联系电话：</td><td>'+data['list'][i]['tel']+'</td></tr><tr><td>详细地址：</td><td>'+data['list'][i]['proviceInfo']['value']+' '+data['list'][i]['cityInfo']['value']+' '+data['list'][i]['address']+'</td></tr><tr><td>客户留言：</td><td>'+data['list'][i]['ClientMsg']+'</td></tr></table></td><td>'+ApproveHtml+'</td><td><h3>'+get_obj_val(data['list'][i],'money')+'<small>元</small></h3><p><code>'+get_obj_val(data['list'][i],'PayStatusInfo.value')+'</code></p><table class="dl-table"><tr><td>支付方式：</td><td>'+get_obj_val(data['list'][i],'PayTypeInfo.value')+'</td></tr><tr><td>套餐售价：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.money')+'元</td></tr>'+jsxx+'</table></td><td><select class="form-control" name="OrderStatus">'+status_html+'</select></td><td><button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button></td></tr></tbody></table></div></td></tr>';
				
			}
		}else{
			for(var i=0;i<data['list'].length;i++){
				var status_html = OrderStatus_html(data['list'][i]['OrderStatus'],data['list'][i]['OrderStatusNaxt']);

				/*赠品*/
				var GiftHtml = '';
				if(data['list'][i]['Gift'] != null && data['list'][i]['Gift'].length>0){
					for(var g=0;g<data['list'][i]['Gift'].length;g++){
						if(data['list'][i]['Gift'][g]['GiftInfo']!=''){
							var Gift_arr = JSON.parse(data['list'][i]['Gift'][g]['GiftInfo']);
							if(Gift_arr!=null && Gift_arr.length>0){
								for(var gg=0;gg<Gift_arr.length;gg++){
									GiftHtml += Gift_arr[gg]['GiftName']+Gift_arr[gg]['num']+Gift_arr[gg]['unit']+'，';
								}
							}
						}
					}
				}
					
				/*金额*/
				if(GiftHtml == ''){
					if(data['list'][i]["Approve"] != null){
						if(data['list'][i]["Approve"]['优惠审批']!=null && data['list'][i]["Approve"]['优惠审批']!='' && data['list'][i]["Approve"]['优惠审批'].length>0){
							for(var n=0;n<data['list'][i]["Approve"]['优惠审批'].length;n++){
								var status = JSON.parse(data['list'][i]["Approve"]['优惠审批'][n]['ApproveStatus']);

								GiftHtml += status['value']+"，";
							}
						}
					}

				}

				var MoneyHtml = '';
				if(data['list'][i]['PayType'] == 'hdfk'){
					var spzt = '';
					if(data['list'][i]["Approve"] != null){
						if(data['list'][i]["Approve"]['优惠审批']!=null && data['list'][i]["Approve"]['优惠审批']!='' && data['list'][i]["Approve"]['优惠审批'].length>0){
							for(var n=0;n<data['list'][i]["Approve"]['优惠审批'].length;n++){
								var status = JSON.parse(data['list'][i]["Approve"]['优惠审批'][n]['ApproveStatus']);
								if(status['key'] == 'sptg'){
									spzt = 'sptg';
								}else{
									MoneyHtml += status['value']+"，";
								}
							}
						}
					}

					if(spzt == 'sptg'){
						MoneyHtml = get_obj_val(data['list'][i],'money');
					}else if(MoneyHtml!=''){
						MoneyHtml = MoneyHtml.substring(0, MoneyHtml.lastIndexOf('，'));
					}

					MoneyHtml = MoneyHtml!=''?'<span>到付：'+MoneyHtml+'</span>':'';
				}

				/*优惠*/
				MoneyHtml += parseFloat(data['list'][i]['CheapMoney']) > 0?'<span>优惠:'+data['list'][i]['CheapMoney']+'</span>':"";
				/*定金*/
				MoneyHtml += parseFloat(data['list'][i]['DepositMoney']) > 0?'<span>定金:'+data['list'][i]['DepositMoney']+'</span>':"";
				/*优惠券*/
				MoneyHtml += parseFloat(data['list'][i]['PreferentialMoney']) > 0?'<span>优惠券:'+data['list'][i]['PreferentialMoney']+'</span>':"";
				
				MoneyHtml = MoneyHtml!=''?'<p style="color:#990099">'+MoneyHtml+'</p>':'';

				GiftHtml = GiftHtml!=''?'<p style="color:#ff0000;">赠品：'+GiftHtml+'</p>':"";
				GiftHtml = GiftHtml.substring(0, GiftHtml.lastIndexOf('，'));

				/*单号*/
				var ExpressInfo = '';
				if(data['list'][i]['Express'] != 'zwkd'){
					ExpressInfo = get_obj_val(data['list'][i],'ExpressInfo.value');
					if(ExpressInfo!=''){
						var ExpressNum = get_obj_val(data['list'][i],'ExpressNum');
						ExpressInfo = ExpressInfo + '<br />' + (ExpressNum!=''?ExpressNum:"未知")+'<br />';
					}
				}else{
					ExpressInfo = '无快递单号';
				}

				/*留言*/
				var msg_html = data['list'][i]['OrderMsg']!=0?'<p><button type="button" style="color:red;padding:0;" class="btn btn-link msg" data-trigger="focus" data-toggle="popover" data-placement="bottom" title="订单留言" data-content="<p><center>正在加载……</center><p>">有留言</button></p>':'';

				/*短信模块*/
				var msg_button = '';
				if(data['list'][i]['Message'] != null){
					var MessageCount = (data['list'][i]['MessageCount'] != null && data['list'][i]['MessageCount'] != '' && parseFloat(data['list'][i]['MessageCount']) > 0) ? (" +"+data['list'][i]['MessageCount']) : "";

					msg_button = '<p><button class="btn-warning msg-button" type="button" data-id="'+get_obj_val(data['list'][i],'Message.id')+'"><span>'+get_obj_val(data['list'][i],'Message.MessageName')+MessageCount+'</span></button></p>';
				}

				/*微信、推荐码优惠金额、客服*/
				var other = '';
				other += get_obj_val(data['list'][i],'SellIDInfo.WechatName');
				if(other!=''){
					other += ','+data['list'][i]['RecommendMoney']+','+get_obj_val(data['list'][i],'SellUserInfo.username');
				}
				other = other!='' ? '<p style="color:red;">'+other+'</p>' : '';

				/*付款状态*/
				var PayStatus = get_obj_val(data['list'][i],'PayStatusInfo.key');
				var PayHtml = (PayStatus == 'yfk') ? '<p style="color:red;">已付</p>' : '';

				html += '<tr data-id="'+data['list'][i]['OrderID']+'"'+(data['list'][i]['color']!=''?('style="background-color:'+data['list'][i]['color']+';"'):'')+'><td><input type="checkbox" /></td><td>'+data['list'][i]['OrderID']+'</td><td>'+get_obj_val(data['list'][i],'SellProductInfo.ProductName')+get_obj_val(data['list'][i],'SellMealInfo.MealName')+GiftHtml+MoneyHtml+'</td><td>'+data['list'][i]['ClientName']+'</td><td>'+data['list'][i]['proviceInfo']['value']+' '+data['list'][i]['cityInfo']['value']+' '+data['list'][i]['address']+'</td><td>'+ExpressInfo+msg_html+'</td><td>'+data['list'][i]['tel']+other+'</td><td>'+getDates(data['list'][i]['addtime'],1)+msg_button+'</td><td>'+get_obj_val(data['list'][i],'PayTypeInfo.value')+PayHtml+'</td><td><select name="OrderStatus">'+status_html+'</select></td><td><button class="">查看</button></td></td>';
			}
		}
	}

	return html;

}
//露露今天给带了西瓜汁
//放在冰箱里面
//拿出来挺好喝的
//都不喝，我一个人给喝了
//露露是个好姑娘

/*加载数据*/
var searchs = search_data($('.tab-search'))
if(searchs){
	post_data("/"+Action+"/"+active()+"Page/",searchs,'','0');
}

/*ajax*/
function post_data(url,data,dom,time){
	time = time!=''?time:500;
	if(time>0){
		chuli("正在处理，请稍后……");
	}

	setTimeout(function(){
		$.ajax({
			url: '/order.php'+url+'?web='+web,
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


/*审批状态小图标*/
function status_htmls(status){
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

/*toastr配置*/
//toastr.options = {positionClass: "toast-top-center"};

/*tr 单独更新*/
function tr_update(OrderID){
	var data = {};
	data.id = OrderID;
	data.operate = 'OneUpdate';
	post_data('/'+Action+'/'+active()+'Data/',data,'','0');
}

/*生成订单状态select代码*/
function OrderStatus_html(key,Naxt){
	var html = '';
	if(Naxt != null && Naxt.length>0){
		for(var n=0;n<Naxt.length;n++){
			for(var ii in UserOrderStatus[Naxt[n]]){
				var selected = (key!='' && ii==key)?' selected="selected"':"";
				html += '<option value="'+ii+'"'+selected+'>'+UserOrderStatus[Naxt[n]][ii]+'</option>'
			}
		}
	}else{
		for(var i in UserOrderStatus){
			for(var ii in UserOrderStatus[i]){
				var selected = (key!='' && ii==key)?' selected="selected"':"";
				html += '<option value="'+ii+'"'+selected+'>'+UserOrderStatus[i][ii]+'</option>'
			}
		}
	}

	if(!/selected/.test(html)){
		html = '<option value="'+key+'" selected="selected">'+get_OrderStatus(key)+'</option>'+html;
	}

	return html;
}

/*获取订单状态值*/
function get_OrderStatus(key){
	var val = '';
	for(var i in OrderStatus){
		for(var ii in OrderStatus[i]){
			if(ii==key){
				return OrderStatus[i][ii];
			}
		}
	}
	return val;
}

/*上传文件删除*/
function close_file(dom){
	$(dom).parent().remove();
}

/*搜索*/
function search_val(dom){
	var data = {};
	dom.find('input[type="text"],select').each(function(){
		var name = $(this).attr("name");
		if(name != null && name != ''){
			var tagName = $(this)[0].tagName;
			var val = $(this).val();
			val = (tagName == 'SELECT' && val == '未选择')?"":val;
			if(val != ''){
				data[name] = val;
			}
		}
	});

	return data;
}

/*返回modal里面的值*/
function m_r_val(id,type,fs){
	if(fs=='obj'){
		var data = {};
		id.find(type).each(function(){
			var name = $(this).attr("name");
			if(name!=undefined && name!=''){
				var now_type = $(this).attr("type");
				if(now_type == 'radio'){
					val = id.find('input[name="'+name+'"]:checked').val();
				}else{
					val = $(this).val();
				}
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


/*返回modal里面的值*/
function serach_val(id,type){
	var data = {};
	id.find(type).each(function(){
		var name = $(this).attr("name");
		if(name!=undefined && name!=''){
			var now_type = $(this).attr("type");
			if(now_type == 'radio'){
				val = id.find('input[name="'+name+'"]:checked').val();
			}else{
				val = $(this).val();
			}

			var tagName = $(this)[0].tagName;
			val = (tagName == 'SELECT' && val == '未选择')?"":val;
			if(val!=''){
				data[name] = val;
			}
			
		}
	});

	return data;
}

/*获取已选*/
function checkbox(dom){
	var che_arr = new Array();
	var che_i = 0;
	dom.find('input[type="checkbox"]').each(function(){
		if($(this).prop('checked')){
			che_arr[che_i] = $(this).val();
			che_i++;
		}
	});
	return che_arr;
}


/*编辑model框获取数据*/
function updata_get_data(name,data){
	var val = '';

	arr = name.split(".");
	if(arr.length == 1){
		val = data[arr[0]] != null?data[arr[0]]:"";
	}else if(arr.length == 2){
		val = data[arr[0]][arr[1]] != null?data[arr[0]][arr[1]]:"";
	}else if(arr.length == 3){
		val = data[arr[0]][arr[1]][arr[2]] != null?data[arr[0]][arr[1]][arr[2]]:"";
	}

	return val;
}

function nulls(obj){
	if(obj != null){
		return false;
	}else{
		return true;
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

/*添加内容到table里面*/
function append(dom,html){
	if(html==''){
		var td_num = dom.find("thead tr td").length;
		html = '<tr><td class="table-null" colspan="'+td_num+'"><i class="glyphicon glyphicon-question-sign"></i><span>暂无数据</span></td></tr>';
	}
	dom.children("tbody").html(html);
}

/*建立人员、销售号、产品、套餐 联动select*/
function sell_select(dom){
	var SellUser_html = '';
	if(AllSalesman != '' && AllSalesman != null && AllSalesman.length>0){
		for(var u=0;u<AllSalesman.length;u++){
			var selected = AllSalesman[u]['userid'] == user_info['userid']?' selected="selected"':"";
			SellUser_html += '<option value="'+AllSalesman[u]['userid']+'"'+selected+'>'+AllSalesman[u]['username']+'</option>';
		}
	}
	dom.find('select[name="SellUser"]').html(SellUser_html);

	SellUser_change(dom);
}

/*销售人员联动事件*/
function SellUser_change(dom){
	var SellID_html = '';
	var user_id = dom.find('select[name="SellUser"]').val();
	if(user_id != null){
		for(var u=0;u<AllSalesman.length;u++){
			if(user_id == AllSalesman[u]['userid']){
				if(AllSalesman[u]['SellID']!=''){
					var SellID_arr = JSON.parse(AllSalesman[u]['SellID']);
					SellID_html = option_html(SellID_arr,AllSellID,'WechatID','WechatID','WechatName');
				}
				break;
			}
		}
	}
	dom.find('select[name="SellID"]').html(SellID_html);
	SellID_change(dom);
}

/*销售号联动事件*/
function SellID_change(dom){
	var SellProduct_html = '';
	var SellID = dom.find('select[name="SellID"]').val();
	if(SellID != null){
		for(var i=0;i<AllSellID.length;i++){
			if(SellID == AllSellID[i]['WechatID']){
				if(AllSellID[i]['ProductID']!=''){
					var ProductID_arr = JSON.parse(AllSellID[i]['ProductID']);
					SellProduct_html = option_html(ProductID_arr,AllProduct,'id','id','ProductName');
				}
				break;
			}
		}
	}
	dom.find('select[name="SellProduct"]').html(SellProduct_html);
	SellProduct_change(dom);
}

/*销售产品联动事件*/
function SellProduct_change(dom){
	var SellMeal_html = '';
	var ProductID = dom.find('select[name="SellProduct"]').val();
	if(ProductID != null){
		for(var i=0;i<AllMeal.length;i++){
			if(ProductID == AllMeal[i]['ProductID']){
				SellMeal_html += '<option value="'+AllMeal[i]['id']+'">'+AllMeal[i]['MealName']+'</option>';
			}
		}
	}
	dom.find('select[name="SellMeal"]').html(SellMeal_html);
}

function OnChange(dom){
	/*改变事件*/
	dom.find('select[name="SellUser"]').change(function(){
		SellUser_change(dom);
	});
	dom.find('select[name="SellID"]').change(function(){
		SellID_change(dom);
	});
	dom.find('select[name="SellProduct"]').change(function(){
		SellProduct_change(dom);
	});
}

/*提交订单联动*/
sell_select($("#OrderList-myModal"));
OnChange($("#OrderList-myModal"));


/*审批联动*/
SellUser_change($("#OrderTo-collapse"));
OnChange($("#OrderTo-collapse"));

/*select计算值*/
function option_html(arr,data,id,key,val){
	var html = '';
	if(arr.length>0 && data.length>0){
		for(var i=0;i<data.length;i++){
			if(arr.indexOf(data[i][id])>=0){
				html += '<option value="'+data[i][key]+'">'+data[i][val]+'</option>';
			}
		}
	}
	return html;
}

/*省份城市select*/
function provice_html(dom,y_pro,y_city){
	var html = '';
	for(var i=1;i<citys.length;i++){

		var selected = (y_pro != '' && i == y_pro)?' selected="selected"':"";

		html += '<option value="'+i+'"'+selected+'>'+citys[i][0]+'</option>';
	}
	dom.html(html);
	provict_change(dom,y_city);
}

function provict_change(dom,y_city){
	var html = '';
	var provice = dom.val();

	var top_html = dom.parent().parent().find('select[name="city"]').html();
	if(top_html.indexOf("未选择")>=0){
		html += '<option>未选择</option>';
	}

	if(provice!='未选择'){
		for(var i=1;i<citys[provice][1].length;i++){
			var selected = (y_city != '' && i == y_city)?' selected="selected"':"";

			html += '<option value="'+i+'"'+selected+'>'+citys[provice][1][i]+'市</option>';
		}
	}
	dom.parent().parent().find('select[name="city"]').html(html);
}

$('select[name="provice"]').change(function(){
	provict_change($(this),'');
});

/*订单提交里面的省份城市联动*/
provice_html($('#OrderList-myModal select[name="provice"]'),'','');

/*订单详情里面的省份，城市*/
provice_html($('.panel-ClientInfo-table select[name="provice"]'),'','');

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

/*获得当前显示的tabID*/
function active(){
	var id = $(".tab-pane.active").attr("id");
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
	scroll($(this));
});

function scroll(dom){
	var header_height = $(".tab .tab-header-wrap").height();
	var thead_top = $('.tab-pane.active .'+now_table+'.tab-table tbody').prev().offset().top;
	var scrollTop = dom.scrollTop() || dom.get(0).scrollTop;
	
	var fixTable = $('.tab-pane.active .tab-pane-content .'+now_table+'.fixTable');
	if(fixTable.length){
		if(thead_top <= header_height){
			fixTable.css('top',(scrollTop-(scrollTop+thead_top)+header_height)+'px');
		}else{
			fixTable.css('top','0px');
		}
	}else{
		var table_html = $('.tab-pane.active .'+now_table+'.tab-table').attr("class");
		var thead_html = $('.tab-pane.active .'+now_table+'.tab-table thead').html();
		html = '<table class="'+table_html+' fixTable"><thead>'+thead_html+'</thead></table>';
		$('.tab-pane.active .tab-pane-content .'+now_table+'.tab-table').before(html);
		fixTable = $('.tab-pane.active .tab-pane-content .'+now_table+'.fixTable');
	}
}