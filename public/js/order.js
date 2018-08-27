var Action = 'Order';
$(document).ready(function(){
	if(web == 'OrderPost'){
		$('#OrderList-myModal').modal('show');
	}

	//$("#Checkbox-Preferential").modal("show");

	/*打开modal窗口*/
	$(".modal_sub").click(function(){
		var now_id = active();
		$("#"+now_id+"-myModal").modal('show');

		if(now_id == 'OrderList'){
			$("#"+now_id+"-myModal select").each(function(){
				$(this).find("option:first").prop("selected",'selected').change();
			});

			$('#'+now_id+'-myModal').find('input[type="text"],textarea').each(function(){
				$(this).val('');
			});

			$("#"+now_id+"-myModal").find('input[name="sex"]:first').prop('checked',true);

			$('#'+now_id+'-myModal').find('[name="PayType"]:checked').prop('checked',false);
		}
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

	$("#OrderList-UpdateModal").find('#head-info,#client-info,#pay-info,#product-info').find('input,select').change(function(){
		$("#OrderList-UpdateModal").find(".modal-footer button:last-child").show();
	});
	$("#OrderList-UpdateModal").find('#head-info,#client-info,#pay-info,#product-info').find('input[type="text"],textarea').keydown(function(){
		$("#OrderList-UpdateModal").find(".modal-footer button:last-child").show();
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
	$('[data-toggle="tooltip"]').tooltip();

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
	/*回车键搜索*/
	$(".tab-search-box input").keyup(function (e) {
		if(e.keyCode == 13) {
			var searchs = search_data($('.tab-search'))
			if(searchs){
				post_data("/"+Action+"/"+active()+"Page/",searchs,'','');
			}
		}
	});

	/*展开其他搜索，更新首行缩进*/
	$(".tab-search a.collapsed").click(function(){
		setTimeout(function(){
			scroll($('.tab-content-main'));
		},400);
	});

	/*地址粘贴自动匹配*/
	$('[name="address"]').on("postpaste", function() {
		city_ico($(this));
	}).pasteEvents();

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

	data.uptime = dom.find('input[type="hidden"][name="uptime"]').val();

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

	order_info.SellMeal = dom.find('select[name="SellMeal"]').val();
	if(order_info.SellMeal == null || order_info.SellMeal == ''){
		toastr.error("请选择产品套餐");
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

	var SellID_text = $("#"+id+" .form-horizontal").find('[name="SellID"]').find('option:selected').text();
	var SellMeal_text = $("#"+id+" .form-horizontal").find('[name="SellMeal"]').find('option:selected').text();
	var provice_text = $("#"+id+" .form-horizontal").find('[name="provice"]').find('option:selected').text();
	var city_text = $("#"+id+" .form-horizontal").find('[name="city"]').find('option:selected').text();
	var confirm_str = '销售微信：'+SellID_text+'\r\n产品套餐：'+SellMeal_text+'\r\n客户姓名：'+data.ClientName+'\r\n联系电话：'+data.tel+'\r\n详细地址：'+provice_text+' '+city_text+' '+data.address+(data.ClientMsg!='' ? '\r\n客户留言：'+data.ClientMsg : '')+'\r\n\r\n请核对订单基本信息，确定提交吗？';

	if(confirm(confirm_str)){
		post_data("/"+Action+"/"+active()+"Data/",data,'','');
	}
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
	$(".order-table button.msg,.orders-table button.msg").off("click").click(function(){
		var dom = $(this);
		//dom.attr("data-content","<p><center>正在加载……</center></p>");
		//console.log("加载中……");
		var OrderID = dom.parents("tr").attr("data-id");
		if(OrderID == null){
			var OrderID = dom.parents("tr").parents("tr").attr("data-id");
		}
		if(OrderID != null){
			post_data("/"+Action+"/OrderMsg/",{"operate":"OrderMsgLists","OrderID":OrderID},dom,'0');
		}else{
			toastr.error("加载留言的时候，订单ID获取失败，请联系技术");
		}
	});

	$('[data-toggle="popover"]').popover({html : true});

	/*发送短信按钮*/
	$(".msg-button.btn-warning").click(function(){
		if(confirm("确定发送短信吗？")){
			var data = {};
			data.Top = $(this).find("span").text();
			$(this).find("span").text("发送中…");
			$(this).attr("disabled",true);

			data.id = $(this).parents("tr").attr("data-id");
			data.id = data.id==null ?  $(this).parents("tr").parents("tr").attr("data-id") : data.id;
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
		}
	});

	/*移到回收站按钮点击事件*/
	$(".recycle").off("click").click(function(){
		var val = $(this).attr("data-title");
		if(val!=null && val!=''){
			var content = val == '0' ? "确定把该笔订单移到回收站吗？" : "确定回复该笔订单吗？";
			if(confirm(content)){
				var data = {};
				data.id = $(this).parents('tr').parents('tr').attr('data-id');
				if(data.id!=null && data.id!=''){
					data.recycle = val == '0' ? '1' : '0';
					data.operate = 'moblie';
					post_data('/'+Action+'/'+active()+'Data/',data,$(this),'0');
				}else{
					toastr.error("订单编号获取失败，请联系技术");
					return false;
				}
			}
		}
	});

	/*复制订单信息按钮*/
	$(".CopyOrder").each(function(){
		var dom = $(this);
		var index = dom.parent().children(".zclip").length;
		if(index <= 0){
			dom.show();
			dom.zclip({
				path: "/public/js/ZeroClipboard.swf",
				copy: function(){
					var val = dom.attr("data-title");

					if(val!=null && val!=''){
						return val;
					}else{
						toastr.warning('数据为空');
					}
				},
				afterCopy:function(){
					toastr.success('复制成功');
				}
			});
			dom.removeAttr("style");
		}
	});

}

/*获取订单详情*/
function LookInfo(OrderID){
	if(OrderID!=null && OrderID!=''){
		gift_on();//赠品按钮点击，加载赠品
		var data = {};
		data.id = OrderID;
		data.operate = 'LookInfo';
		post_data('/'+Action+'/'+active()+'Data/',data,'','10');

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

			/*填入售后时间*/
			if(web == 'OrderService'){
				$("#ServiceDay").html("展现食完时间 "+msg['ServiceDay'][0]+" 至 "+msg['ServiceDay'][1]+" 的订单");
			}

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

			/*商品套餐建立select*/
			var SellMeal_html = '';
			SellMeal_html += '<option value="null">'+get_obj_val(msg,'SellMealInfo.MealName')+'</option>';

			if(msg['SellProduct'] != null && msg['SellProduct']!=''){
				for(var i=0;i<AllMeal.length;i++){
					if(msg['SellProduct'] == AllMeal[i]['ProductID']){
						SellMeal_html += '<option style="color:red;" value="'+AllMeal[i]['id']+'">'+AllMeal[i]['MealName']+'</option>';
					}
				}
			}

			dom.find('select[name="SellMeal"]').html(SellMeal_html);

			/*如果有操作时间、已付款，锁定套餐编辑*/
			dom.find('select[name="SellMeal"]').attr("disabled",false).attr('title',"");
			if(msg['cztime'] > 0 || msg['PayStatus'] == 'yfk'){
				dom.find('select[name="SellMeal"]').attr("disabled",true).attr('title',"已操作、已申请的订单不允许修改产品套餐");
			}


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
			PriceInfo += parseFloat(msg['SaleMoney']) > 0?' - ￥'+msg['SaleMoney']+'（'+msg['Sale']+'折优惠）':"";
			PriceInfo += parseFloat(msg['CheapMoney']) > 0?' - ￥'+msg['CheapMoney']+'（审批优惠）':"";
			PriceInfo += parseFloat(msg['RecommendMoney']) > 0?' - ￥'+msg['RecommendMoney']+'（推荐码优惠）':"";

			$("#pay-info > .panel-heading kbd").remove();
			if(parseFloat(msg['PreferentialMoney']) > 0){
				PriceInfo += ' - ￥'+msg['PreferentialMoney']+'（优惠券优惠）';
				$("#pay-info > .panel-heading").append('<kbd class="pull-right" style="background-color: #777;"><i class="glyphicon glyphicon-barcode"></i> 优惠券 '+msg['preferential']+'</kbd>');
			}
			

			//var money = parseFloat(msg['price']) - parseFloat(msg['CheapMoney']) - parseFloat(msg['RecommendMoney']) - parseFloat(msg['PreferentialMoney']);

			PriceInfo += ' = ￥'+msg['money'];
			dom.find('[name="PriceInfo"] .checkbox').html(PriceInfo);

			/*已付金额*/
			var yfMoney = '';
			yfMoney += parseFloat(msg['DepositMoney']) > 0?' + ￥'+msg['DepositMoney']+'（订金）':"";
			yfMoney += parseFloat(msg['TransferMoney']) > 0?' + ￥'+msg['TransferMoney']+'（转账）':"";
			yfMoney = yfMoney.replace(/^\s\+\s/,'');

			yfMoney = yfMoney=='' ? '￥0.00' : (yfMoney + ' = ￥'+(parseFloat(msg['DepositMoney']) + parseFloat(msg['TransferMoney'])));


			dom.find('[name="yfMoney"] .checkbox').html(yfMoney);
			

			/*还需支付*/
			var hxzf = parseFloat(msg['money']) - parseFloat(msg['DepositMoney']) - parseFloat(msg['TransferMoney']);
			var hxMoney = hxzf > 1 ? '￥'+(parseFloat(msg['money']) - parseFloat(msg['DepositMoney']) - parseFloat(msg['TransferMoney'])) : '￥0.00';
			dom.find('[name="hxMoney"] .checkbox').html(hxMoney);

			/*加载审批状态*/
			$('#approve-panel').find('.panel-heading span').remove();
			if(msg['Approve']!=null){
				for(var a in msg['Approve']){
					if(msg['Approve'][a]!=null && msg['Approve'][a].length>0){
						var html = '';
						for(var i=0;i<msg['Approve'][a].length;i++){
							html += status_htmls(msg['Approve'][a][i]['ApproveStatus']);
						}
						$('#approve-panel').find('#'+a+'-heading h4').append(html);

						/*如果有申请，锁定套餐编辑*/
						dom.find('select[name="SellMeal"]').attr("disabled",true).attr('title',"已操作、已申请的订单不允许修改产品套餐");
					}
				}
			}

			$("#approve-accordion > .panel").show();
			/*如果销售归属已确认，隐藏销售归属申请*/
			if(msg['SellUser'] != '' && msg['SellID'] != ''){
				$("#OrderTo-heading").parent().hide();
			}
			/*货到付款隐藏转账支付申请、隐藏付款状态 ，在线支付隐藏定金申请*/
		
			dom.find(".PayStatus").show();
			if(msg['PayType'] == 'hdfk'){
				dom.find(".PayStatus").hide();
				$("#Transfer-heading").parent().hide();
			}else{
				$("#Deposit-heading").parent().hide();
			}


			/*审批panel里面value清空，收缩起来*/
			$("#approve-panel").find('input[type="text"],input[type="file"],textarea').val("");
			$("#approve-panel").find(".collapse").collapse('hide');

			/*如果变动，显示确定修改按钮*/
			dom.find(".modal-footer button:last-child").hide();

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
		html = html=='' ? '<p>暂无留言</p>' : html;
		dom.attr("data-content",html);
		//$('[data-toggle="popover"]').popover('hide');
		dom.popover('show');

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
		}else if(msg.error == '3'){
			var html = '';
			if(msg['AllPreferentialCode'] != null && msg['AllPreferentialCode'].length > 0){
				for(var i=0;i<msg['AllPreferentialCode'].length;i++){
					html += '<div class="radio"><label><input type="radio" name="Checkbox-Preferential" value="'+msg['AllPreferentialCode'][i]['preferentialcode']+'">【'+msg['AllPreferential'][msg['AllPreferentialCode'][i]['PreferentialID']]+'】'+msg['AllPreferentialCode'][i]['preferentialcode']+'（次数'+msg['AllPreferentialCode'][i]['Num']+'次）（已使用'+msg['AllPreferentialCode'][i]['Number']+'次）</label></div>';
				}
			}
			
			$("#Checkbox-Preferential").find(".modal-body-content").html(html);
			$("#Checkbox-Preferential").modal('show');

			$("#Checkbox-Preferential .btn-success").off("click").click(function(){
				data.PreferentialCode = $("#Checkbox-Preferential").find('input[name="Checkbox-Preferential"]:checked').val();
				if(data.PreferentialCode == null || data.PreferentialCode == ''){
					toastr.warning("请选取优惠券");
					return false;
				}
				$("#Checkbox-Preferential").modal('hide');
				post_data('/'+Action+'/'+active()+'Data/',data,dom,'0');
			});

			$("#Checkbox-Preferential .btn-default").off("click").click(function(){
				dom.find("span").text(data.Top);
				dom.attr("disabled",false);
			});

		}else{
			dom.find("span").text(data.Top);
			dom.attr("disabled",false);
		}
	}else if(data.operate == 'moblie'){
		if(msg.error == '1'){
			dom.parents("tr").parents("tr").hide(300);
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
			var date = new Date();
			var NowTime = Date.parse(new Date(date.getFullYear()+"/"+_time(date.getMonth()+1)+"/"+_time(date.getDate())+" 00:00:00")) / 1000;

			for(var i=0;i<data['list'].length;i++){
				var status_html = OrderStatus_html(data['list'][i]['OrderStatus'],data['list'][i]['OrderStatusNaxt']);

				var ExpressInfo = get_obj_val(data['list'][i],'ExpressInfo.value');
				if(ExpressInfo!=''){
					var ExpressNum = get_obj_val(data['list'][i],'ExpressNum');
					ExpressInfo = '<p>'+ExpressInfo + '：' + (ExpressNum!=''?ExpressNum:"未知") + '</p>';
				}
				ExpressInfo = ExpressInfo != '' ? ExpressInfo : '<p>暂无快递单号</p>';

				/*留言*/
				var msg_html = data['list'][i]['OrderMsg']!=0?'<button type="button" class="btn btn-link btn-xs msg" data-trigger="focus" data-toggle="popover" data-placement="bottom" title="订单留言" data-content="<p><center>正在加载……</center><p>"><i class="glyphicon glyphicon-comment"></i>留言</button>':'';

				/*短信模块*/
				var msg_button = '';
				if(data['list'][i]['Message'] != null){
					for(var m=0;m<data['list'][i]['Message'].length;m++){

						var MessageCount = (data['list'][i]['Message'][m]['count'] != null && data['list'][i]['Message'][m]['count'] != '' && parseFloat(data['list'][i]['Message'][m]['count']) > 0) ? (" +"+data['list'][i]['Message'][m]['count']) : "";

						msg_button += '<p><button class="btn btn-warning btn-xs msg-button" type="button" data-id="'+data['list'][i]['Message'][m]['id']+'"><i class="glyphicon glyphicon-envelope"></i> <span>'+data['list'][i]['Message'][m]['MessageName']+MessageCount+'</span></button></p>';
					}
				}

				/*审批*/
				var ApproveHtml = '';
				var preferential_html = '';
				// if(data['list'][i]["Approve"] != null){
				// 	for(var t in data['list'][i]["Approve"]){
				// 		if(data['list'][i]["Approve"][t] != null && data['list'][i]["Approve"][t] != '' && data['list'][i]["Approve"][t].length>0){
				// 			for(var n=0;n<data['list'][i]["Approve"][t].length;n++){
				// 				ApproveHtml += '<tr><td>'+t+'</td><td>'+status_htmls(data['list'][i]["Approve"][t][n]['ApproveStatus'])+'</td>';

				// 				if(t=='优惠审批' && data['list'][i]["Approve"][t][n]['PreferentialCode']!=''){
				// 					preferential_html += '<kbd style="background-color: #5cb85c;"><i class="glyphicon glyphicon-barcode"></i> 优惠券 '+data['list'][i]["Approve"][t][n]['PreferentialCode']+' ￥'+data['list'][i]["Approve"][t][n]['PreferentialMoney']+'</kbd>';
				// 				}
				// 			}
				// 		}
				// 	}
				// 	ApproveHtml = ApproveHtml!=''?'<table class="dl-table Approve-table">'+ApproveHtml+'</table>':"";
				// }


				var arr;
				var val = '';
				var Show = true;
				/*赠品 优惠金额*/
				var GiftHtml = '';
				var CheapMoneyHtml = '';
				arr = data['list'][i]["Approve"]['Cheap'];
				if(arr != null && arr.length>0){
					for(var g=0;g<arr.length;g++){

						/*赠品*/
						if(arr[g]['GiftInfo'] != null && arr[g]['GiftInfo']!=''){
							var Gift_arr = JSON.parse(arr[g]['GiftInfo']);
							if(Gift_arr!=null && Gift_arr.length>0){
								for(var gg=0;gg<Gift_arr.length;gg++){
									GiftHtml += '<p><span class="text-danger">'+Gift_arr[gg]['GiftName']+'：<b>'+Gift_arr[gg]['num']+Gift_arr[gg]['unit']+'</b></span>'+status_htmls(arr[g]['ApproveStatus'])+'</p>';
								}
							}
						}

						/*优惠金额*/
						if(arr[g]['CheapMoney'] != null && parseFloat(arr[g]['CheapMoney']) > 0){
							val = sptg(arr[g]['CheapMoney'],arr[g]['ApproveStatus']);
							Show = hdfk_show(Show,arr[g]['ApproveStatus']);

							CheapMoneyHtml += '<tr><td>券外优惠：</td><td><code>'+val+' 元</code>'+status_htmls(arr[g]['ApproveStatus'])+'</td></tr>';
						}

						/*优惠券*/
						// if(arr[g]['PreferentialMoney'] != null && parseFloat(arr[g]['PreferentialMoney']) > 0){
						// 	val = sptg(arr[g]['PreferentialMoney'],arr[g]['ApproveStatus']);
						// 	Show = hdfk_show(Show,arr[g]['ApproveStatus']);

						// 	PreferentialHtml += '<tr><td>优惠券：</td><td><code>'+val+'元</code>'+(arr[g]['PreferentialCode'] != '' ? ('<div class="clearfix"></div><kbd style="background-color: #777;"><i class="glyphicon glyphicon-barcode"></i> 优惠券 '+arr[g]['PreferentialCode']+'</kbd>') : '')+status_htmls(arr[g]['ApproveStatus'])+'</td></tr>';
						// }
					}

					GiftHtml = GiftHtml.replace(/\<br\>$/,'');
					GiftHtml = GiftHtml!=''?'<tr><td>赠品：</td><td>'+GiftHtml+'</td></tr>':"";

				}

				/*优惠券*/
				var PreferentialHtml = '';
				if(data['list'][i]['preferential']!=''){
					PreferentialHtml += '<tr><td>优惠券：</td><td><code>'+data['list'][i]['PreferentialMoney']+' 元</code><kbd class="preferential">优惠券 '+data['list'][i]['preferential']+'</kbd></td></tr>';
				}

				/*定金转账*/
				var DepositHtml = '';
				var TransferHtml = '';
				arr = data['list'][i]["Approve"]['Deposit_Transfer'];
				if(arr != null && arr.length>0){
					for(var g=0;g<arr.length;g++){

						/*定金*/
						if(arr[g]['type'] != null && arr[g]['type'] == 'Deposit'){
							val = sptg(arr[g]['money'],arr[g]['ApproveStatus']);
							Show = hdfk_show(Show,arr[g]['ApproveStatus']);

							DepositHtml += '<tr><td>订金：</td><td><code>'+val+' 元</code>'+status_htmls(arr[g]['ApproveStatus'])+'</td></tr>';
						}

						/*转账*/
						if(arr[g]['type'] != null && arr[g]['type'] == 'Transfer'){
							val = sptg(arr[g]['money'],arr[g]['ApproveStatus']);
							Show = hdfk_show(Show,arr[g]['ApproveStatus']);
							
							TransferHtml += '<tr><td>转账支付：</td><td><code>'+val+' 元</code>'+status_htmls(arr[g]['ApproveStatus'])+'</td></tr>';
						}
					}

					// DepositHtml = DepositHtml.replace(/\<br\>$/,'');
					// TransferHtml = TransferHtml.replace(/\<br\>$/,'');
				}

				/*结算信息*/
				var PayInfo_html = '';
				if(data['list'][i]['PayType'] == 'hdfk'){
					PayInfo_html += '<tr><td>支付方式：</td><td><code><b>到付:'+(Show ? (data['list'][i]['money'] - data['list'][i]['DepositMoney'])+" 元" : ' 审批中…')+'</b></code></td></tr>';
				}else{
					if(data['list'][i]['PayStatus'] == 'yfk'){
						PayInfo_html += '<tr><td>支付方式：</td><td><code>在线 <b>已付</b></code></td></tr>';
					}else{
						PayInfo_html += '<tr><td>支付方式：</td><td><code>在线 未付</code></td></tr>';
					}
				}

				PayInfo_html += parseFloat(data['list'][i]['SaleMoney']) > 0?'<tr><td>折扣金额：</td><td><code>'+data['list'][i]['SaleMoney']+' 元（'+data['list'][i]['Sale']+'折）</code></td></tr>':"";

				PayInfo_html += parseFloat(data['list'][i]['RecommendMoney']) > 0?'<tr><td>推荐码：</td><td><code>'+data['list'][i]['RecommendMoney']+' 元</code></td></tr>':"";


				PayInfo_html += PreferentialHtml + CheapMoneyHtml + DepositHtml + TransferHtml;

				var fhtime_html = data['list'][i]['fhtime'] > 0 ? '<p>发货时间：'+getDates(data['list'][i]['fhtime'],1)+'</p>' : '';
				var cztime_html = data['list'][i]['cztime'] > 0 ? '<p>操作时间：'+getDates(data['list'][i]['cztime'],1)+'</p>' : '';
				var swtime_htmls = (web == 'OrderService' && data['list'][i]['swtime'] > 0) ? '<p>食完时间：'+getDates(data['list'][i]['swtime'],1)+'</p>' : '';

				var swtime_html = '';
				if(web == 'OrderService' && data['list'][i]['swtime'] > 0){
					var SY_Day = (NowTime - parseFloat(data['list'][i]['swtime'])) / 86400;
					swtime_html = '<span class="text-'+(SY_Day > 0 ? 'danger' : 'success')+'"><i class="glyphicon glyphicon-time"></i> <b>'+(SY_Day > 0 ? '已吃完'+(parseInt(Math.abs(SY_Day))+1)+'天' : (parseInt(SY_Day) == 0 ? '今天食完' : '还有'+parseInt(Math.abs(SY_Day))+'天吃完'))+'</b></span>';
				}





				/*订单状态颜色*/
				var StatusColor = '';
				var opacity = '';
				if(data['list'][i]['OrderStatus']=='wcl'){
					StatusColor = 'btn-dangers';
				}else if(data['list'][i]['OrderStatus']=='yqs'){
					StatusColor = 'btn-successs';
				}else if(data['list'][i]['OrderStatus']=='yqr'){
					StatusColor = 'btn-warnings';
				}else if(data['list'][i]['OrderStatus']=='yfh'){
					StatusColor = 'btn-infos';
				}else if(data['list'][i]['OrderStatus']=='yyfh'){
					StatusColor = 'btn-primarys';
				}else if(data['list'][i]['OrderStatus']=='cfdd' || data['list'][i]['OrderStatus']=='ljdd'){
					/*垃圾订单和重复订单浅灰*/
					opacity = ' style="opacity:0.4;"';
				}

				/*订单回收站 恢复按钮*/
				var moblie = '<div class="recycle" data-title="'+data['list'][i]['recycle']+'" title="'+(data['list'][i]['recycle'] == '0' ? '移到回收站' : '恢复订单')+'"><i class="glyphicon '+(data['list'][i]['recycle'] == '0' ? 'glyphicon-trash' : 'glyphicon-ok')+'"></i></div>';

				/*垃圾订单和重复订单浅灰*/


				html += '<tr data-id="'+data['list'][i]['OrderID']+'"'+opacity+'><td colspan="7"><div class="panel panel-default"><div class="panel-heading clearfix"'+(data['list'][i]['color']!=''?('style="background-color:'+data['list'][i]['color']+';"'):'')+'><table style="width:100%;margin-bottom:0;"><tr><td width="2%"><input type="checkbox" /></td><td width="20%">订单编号：'+data['list'][i]['OrderID']+'</td><td width="20%">提交时间：'+getDates(data['list'][i]['addtime'],1)+'</td><td width="10%">销售人：'+get_obj_val(data['list'][i],'SellUserInfo.username','未知')+'</td><td width="11%">销售号：'+get_obj_val(data['list'][i],'SellIDInfo.WechatName','未知')+'</td><td width="8%">'+msg_html+'</td><td width="10%">'+swtime_html+'</td><td width="">'+moblie+'</td></tr></table></div><table class="tab-table order-table"><tbody><tr><td></td><td><table class="dl-table"><tr><td>商品名称：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.MealName')+'</td></tr>'+GiftHtml+'</table></td><td><table class="dl-table">'+PayInfo_html+'</table></td><td><table class="dl-table"><tr><td>收件人：</td><td>'+data['list'][i]['ClientName']+'<i class="glyphicon glyphicon-duplicate CopyOrder" title="复制订单信息" data-title="姓名：'+data['list'][i]['ClientName']+'\r\n电话：'+data['list'][i]['tel']+'\r\n地址：'+data['list'][i]['proviceInfo']['value']+' '+data['list'][i]['cityInfo']['value']+' '+data['list'][i]['address']+'\r\n套餐：'+get_obj_val(data['list'][i],'SellMealInfo.MealName')+'"></i></td></tr><tr><td>联系电话：</td><td>'+data['list'][i]['tel']+'</td></tr><tr><td>详细地址：</td><td>'+data['list'][i]['proviceInfo']['value']+' '+data['list'][i]['cityInfo']['value']+' '+data['list'][i]['address']+'</td></tr><tr><td>客户留言：</td><td>'+data['list'][i]['ClientMsg']+'</td></tr></table></td><td><h4>'+get_obj_val(data['list'][i],'PayTypeInfo.value')+(data['list'][i]['PayType'] != 'hdfk' && data['list'][i]['PayStatus'] == 'yfk' ? '<code>已支付</code>' : '')+'</h4>'+msg_button+ExpressInfo+fhtime_html+cztime_html+swtime_htmls+'</td><td><select class="form-control '+StatusColor+'"  name="OrderStatus">'+status_html+'</select></td><td><button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button></td></tr></tbody></table></div></td></tr>';


				//html += '<tr data-id="'+data['list'][i]['OrderID']+'"><td colspan="7"><div class="panel panel-default"><div class="panel-heading clearfix"'+(data['list'][i]['color']!=''?('style="background-color:'+data['list'][i]['color']+';"'):'')+'><ul class="list-inline order-info"><li><input type="checkbox" /></li><li>订单编号：'+data['list'][i]['OrderID']+'</li><li>提交时间：'+getDates(data['list'][i]['addtime'],1)+'</li><li>销售人：'+get_obj_val(data['list'][i],'SellUserInfo.username','未知')+'</li><li>销售号：'+get_obj_val(data['list'][i],'SellIDInfo.WechatName','未知')+'</li><li>'+ExpressInfo+'</li><li>'+msg_html+'</li><li>'+msg_button+'</li></ul><div class="delete" title="移到回收站"><i class="glyphicon glyphicon-trash"></i></div></div><table class="tab-table order-table"><tbody><tr><td></td><td><table class="dl-table"><tr><td>商品名称：</td><td>'+get_obj_val(data['list'][i],'SellProductInfo.ProductName')+'</td></tr><tr><td>套餐名称：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.MealName')+'</td></tr><tr><td>售价：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.money')+'元</td></tr><tr><td>数量：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.number')+get_obj_val(data['list'][i],'SellMealInfo.unit')+'</td></tr><tr><td>天数：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.DayNum')+'天</td></tr></table></td><td>'+GiftHtml+'</td><td><table class="dl-table"><tr><td>收件人：</td><td>'+data['list'][i]['ClientName']+'</td></tr><tr><td>联系电话：</td><td>'+data['list'][i]['tel']+'</td></tr><tr><td>详细地址：</td><td>'+data['list'][i]['proviceInfo']['value']+' '+data['list'][i]['cityInfo']['value']+' '+data['list'][i]['address']+'</td></tr><tr><td>客户留言：</td><td>'+data['list'][i]['ClientMsg']+'</td></tr></table></td><td>'+ApproveHtml+'</td><td><h3>'+get_obj_val(data['list'][i],'money')+'<small>元</small></h3><p><code>'+get_obj_val(data['list'][i],'PayStatusInfo.value')+'</code></p><table class="dl-table"><tr><td>支付方式：</td><td>'+get_obj_val(data['list'][i],'PayTypeInfo.value')+'</td></tr><tr><td>套餐售价：</td><td>'+get_obj_val(data['list'][i],'SellMealInfo.money')+'元</td></tr>'+jsxx+'</table></td><td><select class="form-control" name="OrderStatus">'+status_html+'</select></td><td><button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-search"></i><span>查看详情</span></button></td></tr></tbody></table></div></td></tr>';
				
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

			html = '<span class="Approve-i label label-'+html+'">'+status['value']+'</span>';
		}
	}
	return html;
}

/*审批是否通过*/
function sptg(val,status){
	if(status!=''){
		var status = JSON.parse(status);
		if(status['key']!=null && status['key']!=''){
			if(status['key'] == 'sptg'){
				return val;
			}
		}
	}
	return " * ";
}

function hdfk_show(val,status){
	if(val){
		if(status!=''){
			var status = JSON.parse(status);
			if(status['key']!=null && status['key']!=''){
				if(status['key'] == 'sptg' || status['key'] == 'cxsp' || status['key'] == 'spbtg'){//审批不通过显示，是考虑到申请多次，出现多个审批，第一个同意，第二个可能没有同意
					return true;
				}
			}
		}
	}
	return false;
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

	has(dom.find('select[name="SellID"]').parents('.form-group'));
}

function has(dom){
	setTimeout(function(){
		dom.toggleClass("has-error");
		setTimeout(function(){
			dom.toggleClass("has-error");
			setTimeout(function(){
				dom.toggleClass("has-error");
				setTimeout(function(){
					dom.toggleClass("has-error");
				},250);
			},250);
		},250);
	},250);
}

/*销售号联动事件*/
function SellID_change(dom){
	var SellProduct_html = '';
	var recommend_html = '<option value="">不使用</option>';
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

		for(var i=0;i<AllRecommend.length;i++){
			if(SellID == AllRecommend[i]['SellID']){
				recommend_html += '<option value="'+AllRecommend[i]['Head']+AllRecommend[i]['Code']+'">'+AllRecommend[i]['Head']+AllRecommend[i]['Code']+"（"+AllRecommend[i]['Name']+'）</option>';
			}
		}
	}
	dom.find('select[name="SellProduct"]').html(SellProduct_html);
	dom.find('select[name="recommend"]').html(recommend_html);
	SellProduct_change(dom);

	has(dom.find('select[name="SellProduct"]').parents('.form-group'));
	has(dom.find('select[name="recommend"]').parents('.form-group'));
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

	has(dom.find('select[name="SellMeal"]').parents('.form-group'));
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
			if(arr.indexOf(data[i][id])>=0 || arr.indexOf('all')>=0){
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

	if(provice != '未选择'){
		for(var i=1;i<citys[provice][1].length;i++){
			var selected = (y_city != '' && i == y_city)?' selected="selected"':"";

			html += '<option value="'+i+'"'+selected+'>'+citys[provice][1][i]+'</option>';
		}
	}
	dom.parent().parent().find('select[name="city"]').html(html);
}

$('select[name="provice"]').change(function(){
	provict_change($(this),'');
});


/*自动计算地址*/
function city_ico(dom){
	var doms = dom.parents('.modal');
	// var top_provice = doms.find('[name="provice"]').val();
	// var top_city = doms.find('[name="city"]').val();
	// if(top_provice == '1' && top_city == '1'){
	// }

		var val = dom.val();
		var dizhi = val.replace(/(^\s*|$\s*)/,"");
		var provice = '';
		var provice_str = '';
		var city = '';
		var city_str = '';
		var reg = '';

		for(var p=1;p<citys.length;p++){
			var now_provice_str = citys[p][0].replace(/省$/,'');
			reg = eval('/^'+now_provice_str+'省*/');
			var pipei = dizhi.match(reg);
			if(pipei != null && pipei.length > 0){
				provice = p;
				provice_str = pipei[0];
				break;
			}
		}

		if(provice != ''){
			reg = eval('/^'+provice_str+'\s*[。|，]*/');
			dizhi = dizhi.replace(reg,"");
			dizhi = dizhi.replace(/^\s*/,"");

			for(var c=1;c<citys[provice][1].length;c++){
				var now_city_str = citys[provice][1][c].replace(/市$/,'');
				reg = eval("/"+now_city_str+"市*/");
				var pipei = dizhi.match(reg);
				if(pipei != null && pipei.length > 0){
					city = c;
					city_str = pipei[0];

					reg = eval('/^'+city_str+'\s*[。|，]*/');
					dizhi = dizhi.replace(reg,"");
					dizhi = dizhi.replace(/^\s*/,"");
					break;
				}
			}
		}else{
			for(var p=1;p<citys.length;p++){
				for(var c=1;c<citys[p][1].length;c++){
					var now_city_str = citys[p][1][c].replace(/市$/,'');
					reg = eval("/"+now_city_str+"市*/");
					var pipei = dizhi.match(reg);
					if(pipei != null && pipei.length > 0){
						provice = p;
						provice_str = citys[p][0];

						city = c;
						city_str = pipei[0];

						reg = eval('/^'+city_str+'\s*[。|，]*/');
						dizhi = dizhi.replace(reg,"");
						dizhi = dizhi.replace(/^\s*/,"");
						break;
					}

				}
			}
		}

		if(provice != ''){
			doms.find('[name="provice"]').val(provice).change();
			//doms.find('[name="provice"]').parents('.form-group').toggleClass("has-error");
			has(doms.find('[name="provice"]').parent());
			//provict_change(doms.find('[name="provice"]'),city);
		}else if(city != ''){
			doms.find('[name="provice"]').prepend('<option></option>');
			has(doms.find('[name="city"]').parent());
		}

		if(city != ''){
			doms.find('[name="city"]').val(city);
			has(doms.find('[name="city"]').parent());
		}else if(provice !== ''){
			doms.find('[name="city"]').prepend('<option></option>');
			has(doms.find('[name="city"]').parent());
		}

		if(provice != '' || city != ''){

			dom.val(dizhi);
			has(dom.parent());

			doms.find('[name="city"]').tooltip('show');
			setTimeout(function(){
				doms.find('[name="city"]').tooltip('hide');
			},2000);

		}

}
//city_ico($('#OrderList-myModal [name="address"]'));

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

/*粘贴事件*/
$.fn.pasteEvents = function( delay ) {
    if (delay == undefined) delay = 20;
    return $(this).each(function() {
        var $el = $(this);
        $el.on("paste", function() {
            $el.trigger("prepaste");
            setTimeout(function() { $el.trigger("postpaste"); }, delay);
        });
    });
};