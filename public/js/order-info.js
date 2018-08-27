$(document).ready(function(){
	$("#order-info button").click(function(){
		var OrderID = $('#order-info input[name="OrderID"]').val();
		if(OrderID == ''){
			toastr.error("出错，订单编号获取失败");
			return false;
		}
		var button = $(this).text();
		if(button == '申请'){
			post_approve($(this),'');
		}else if(button == '发表留言'){
			var panel = $(this).parents(".panel");
			var data = m_r_val(panel,'textarea[name="content"]','obj');

			if(data.content==''){
				toastr.error("留言不能为空");
				return false;
			}

			data.id = OrderID;
			data.operate = 'AddMsg';

			post_data('/'+Action+'/'+active()+'Data/',data,panel,'0');

			/*更新单个tr*/
			setTimeout(function(){
				tr_update(data.id);
			},600);
		}
	});

});

function post_approve(dom,two){

	var OrderID = $('#order-info input[name="OrderID"]').val();
	if(OrderID == ''){
		toastr.error("出错，订单编号获取失败");
		return false;
	}

	var panel = dom.parents(".panel-collapse");

	var data = m_r_val(panel.find(".form-horizontal"),'input[type="text"],textarea,select','obj');
	data.id = OrderID;

	data.ApproveType = panel.attr("id").replace("-collapse","");
	if(data.ApproveType == ''){
		toastr.error("出错，审批类型不能为空");
		return false;
	}else if(data.ApproveType == 'Cheap'){
		/*订单优惠申请*/
		/*获得填入数字的赠品ID*/
		var error = 0;
		var gift_arr = new Array();
		var g_i = 0;
		panel.find('input[name="GiftNum"]').each(function(i){
			var GiftNum = $(this).val();
			if(GiftNum != ''){
				if(isNaN(GiftNum)){
					error = 1;
					toastr.warning("第 "+(i+1)+" 行 赠品申请数量必须为数字");
					return false;
				}else if(parseInt(GiftNum) != GiftNum){
					error = 1;
					toastr.warning("第 "+(i+1)+" 行 赠品申请数量必须是整数");
					return false;
				}else if(parseInt(GiftNum) > 0){

					var limit = $(this).attr("data-limit");
					if(parseInt(GiftNum) > parseInt(limit)){
						error = 1;
						toastr.warning("第 "+(i+1)+" 行 赠品申请数量超出单次上限");
						return false;
					}else{
						var GiftID = $(this).parents("tr").attr("data-id");
						if(GiftID != null && GiftID != ''){
							gift_arr[g_i] = [GiftID,GiftNum];
							g_i++;
						}
					}
				}
			}
		});

		if(error != 0){
			return false;
		}

		if(gift_arr.length>0){
			data.Gift = JSON.stringify(gift_arr);
		}

		if(data.CheapMoney != '' && isNaN(data.CheapMoney)){
			toastr.warning("优惠金额必须是数字");
			return false;
		}

		if(gift_arr.length<=0 && data.CheapMoney=='' && data.PreferentialCode==''){
			toastr.warning("请填写优惠");
			return false;
		}
	}else if(data.ApproveType == 'OrderTo'){
		/*订单归属申请*/
		if(data.SellUser == ''){
			toastr.warning("销售人员不能为空");
			return false;
		}
		if(data.SellID == ''){
			toastr.warning("销售号不能为空");
			return false;
		}

	}else if(data.ApproveType == 'Transfer'){
		/*转账支付申请*/
		if(data.PaysType == ''){
			toastr.warning("支付方式不能为空");
			return false;
		}
		if(data.PayMoney == ''){
			toastr.warning("付款金额不能为空");
			return false;
		}else if(isNaN(data.PayMoney)){
			toastr.warning("付款金额必须是数字");
			return false;
		}else if(parseInt(data.PayMoney) <= 0){
			toastr.warning("付款金额必须大于0");
			return false;
		}
	}else if(data.ApproveType == 'Deposit'){
		/*定金申请*/
		if(data.PaysType == ''){
			toastr.warning("支付方式不能为空");
			return false;
		}
		if(data.DepositMoney == ''){
			toastr.warning("订金金额不能为空");
			return false;
		}else if(isNaN(data.DepositMoney)){
			toastr.warning("订金金额必须是数字");
			return false;
		}else if(parseInt(data.DepositMoney) <= 0){
			toastr.warning("付款金额必须大于0");
			return false;
		}
	}else if(data.ApproveType == 'Service'){
		/*售后申请*/
		data.ServiceWay = panel.find('.form-horizontal').find('input[name="ServiceWay"]:checked').val();
		if(data.ServiceWay == null){
			toastr.warning("请选择售后方式");
			return false;
		}
		if(data.ReturnNum != ''){
			if(isNaN(data.ReturnNum)){
				toastr.warning("数量必须是数字");
				return false;
			}else if(parseInt(data.ReturnNum) != data.ReturnNum){
				toastr.warning("数量必须是整数");
				return false;
			}else if(parseInt(data.ReturnNum) <= 0){
				toastr.warning("数量必须大于0");
				return false;
			}
		}
		if(data.ReturnMoney != ''){
			if(isNaN(data.ReturnMoney)){
				toastr.warning("金额必须是数字");
				return false;
			}else if(parseInt(data.ReturnMoney) <= 0){
				toastr.warning("金额必须大于0");
				return false;
			}
		}

		if(data.ReturnNum=='' && data.ReturnMoney==''){
			toastr.warning("数量或金额至少要填写一个");
			return false;
		}
	}

	//var formData = new FormData();
	var file_count = 0;
	var file_dom = panel.find('input[type="file"]');
	file_dom.each(function(i){
		if($(this).val() != ''){
			//formData.append('myfile'+i, $(this)[0].files[0]);
			$(this).attr('name','myfile'+i);
			file_count++;
		}
	});

	if(file_count <= 0){
		if(data.ApproveType != 'Cheap'){
			toastr.warning("必须带截图");
			return false;
		}
	}else if(file_count > 5){
		toastr.warning("截图上传不得超过5张");
		return false;
	}

	data.file = file_count;
	data.operate = 'ApproveAdd';
	data.two = two;

	// for(var i in data){
	// 	formData.append(i,data[i]);
	// }

	chuli("正在处理，请稍后……");

	setTimeout(function(){
		$.ajaxFileUpload({
			url:'/order.php/Approve/Data/',
			secureuri: false,
			fileElementIds:file_dom,
			data:data,
			dataType: 'json',
			type: 'post',
			success: function (msg){
				if(msg.error=='2'){
					if(confirm(msg.msg)){
						post_approve(dom,'yes');
					}
					chuli("");
				}else{
					msg_chuli(data,msg,panel);
				}
			},
			error: function (){
					chuli("");
					toastr.error("提交数据，产生错误");
			}
		});
	},500);
	return false;

	setTimeout(function(){
		$.ajax({
			url: '/order.php/Approve/Data/',
			type:"post",
			data: formData,
            contentType: false,
            processData: false,
			dataType:'json',
			success: function (msg) {
				if(msg.error=='2'){
					if(confirm(msg.msg)){
						post_approve(dom,'yes');
					}
					chuli("");
				}else{
					msg_chuli(data,msg,panel);
				}
			},
			error: function () {
				chuli("");
				toastr.error("提交数据，产生错误");
			}

		});
	},500);
}

/*优惠点击，获取赠品*/
function gift_on(){
	$("#Cheap-heading a").click(function(){
		if(!$("#Cheap-collapse").hasClass("in")){

			$("#Cheap-collapse .panel-gift-table tbody").empty();

			var now_active = active();
			var data = {};
			data.id = $("#"+now_active+"-UpdateModal").find('input[name="OrderID"]').val();;

			if(data.id != ''){
				data.operate = 'GetGift';
				post_data('/'+Action+'/'+active()+'Data/',data,'','0');
			}else{
				toastr.error("订单编号获取失败，请刷新页面重试");
			}
		}
	})
}