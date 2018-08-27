$(document).ready(function(){

});

function organize_on(){
	/*展开和隐藏*/
	$(".organize .select").click(function(){
		$(this).parent().parent().children("ul").toggle(200);
		$(this).children("i.up-down").toggleClass("glyphicon-menu-up").toggleClass("glyphicon-menu-down");
	});

	/*编辑按钮点击事件*/
	$(".organize .edit").click(function(){
		var title = $(this).attr("title");
		if(title != ''){
			if(title == '编辑部门'){
				var data = {};
				data.id = $(this).parents("li").attr("data-DepartmentID");
				if(data.id!=null && data.id!=''){
					data.operate = 'Get';
					post_data('/'+Action+'/Data/',data,$(this),'0');
				}else{
					toastr.error("部门ID获取失败，请联系技术");
				}

			}else if(title == '编辑员工'){
				var data = {};
				data.id = $(this).parents("li").attr("data-userid");
				if(data.id!=null && data.id!=''){
					data.operate = 'GetUser';
					post_data('/'+Action+'/Data/',data,$(this),'0');
				}else{
					toastr.error("员工ID获取失败，请联系技术");
				}
			}
		}else{
			toastr.error("不知道您要干啥");
		}
	});

	/*添加部门按钮*/
	$(".organize .add").click(function(){
		var DepartmentID = $(this).parents("li").attr("data-DepartmentID");
		if(DepartmentID != null && DepartmentID != ''){
			$('#add-myModal').find('input[name="parentID"]').val(DepartmentID);
			$('#add-myModal').modal('show');
		}else{
			toastr.error("部门ID获取失败，请刷新页面重试");
		}
	});

	/*删除部门按钮*/
	$(".organize .del").click(function(){
		var DepartmentID = $(this).parents("li").attr("data-DepartmentID");
		if(DepartmentID != null && DepartmentID != ''){
			if(confirm("确定删除该部门吗？")){
				var data = {};
				data.id = DepartmentID;
				data.operate = 'del';
				post_data('/'+Action+'/Data/',data,$(this),'0');
			}
		}else{
			toastr.error("部门ID获取失败，请刷新页面重试");
		}
	});
}

function organize_chuli(data,msg,dom){
	chuli("");

	if(msg['msg'] != undefined){
		show_msg(msg);
	}

	if(data.operate=='get'){
		if(msg['error'] == '1'){
			organize_html(msg['data'],dom);
		}
	}else if(data.operate=='UpdateAdd'){

	}

}

/*生成组织架构html*/
function organize_html(data,dom){
	var html = ''
	html = organize_bumen_html(data,null);
	dom.html(html);
	organize_on();
}

/*生成部门html*/
function organize_bumen_html(data,udata){
	var html = '';

	if(data != null && data.length>0){
		for(var i=0;i<data.length;i++){

			var zi_organize_html = organize_bumen_html(data[i]['zi'],data[i]['user']);

			html += '<li data-DepartmentID="'+data[i]['id']+'"><div class="rows clearfix"><div class="select"><i class="glyphicon glyphicon-folder-open"></i><span class="title">'+data[i]['DepartmentName']+(data[i]['user']!=null?' ('+data[i]['user'].length+'人)':"")+'</span><i class="up-down glyphicon glyphicon-menu-up"'+(zi_organize_html==''?'style="display:none;"':"")+'></i></div><div class="hides"><i class="edit glyphicon glyphicon-cog" title="编辑部门"></i><i class="add glyphicon glyphicon-plus" title="添加子部门"></i>'+(data[i]['id']!='1'?'<i class="del glyphicon glyphicon-trash" title="删除该部门"></i>':"")+'</div></div>'+zi_organize_html+'</li>';

		}
	}

	if(udata != null && udata.length>0){
		for(var u=0;u<udata.length;u++){
			html += '<li data-userid="'+udata[u]['userid']+'"><div class="rows clearfix user"><div class="select"><i class="glyphicon glyphicon-user"></i><span class="title">'+udata[u]['username']+'</span>'+DutyStyle(udata[u]['Duty'],udata[u]['DutyName'])+'</div><div class="hides"><i class="edit glyphicon glyphicon-cog" title="编辑员工"></i></div></div></li>';
		}
	}

	html = html!=''?'<ul>'+html+'</ul>':"";

	return html;
}

/*组长和主管加样式*/
function DutyStyle(key,value){
	var html = '';
	if(key=='zg' || key=='zz'){
		html = '<button type="button" class="btn btn-xs btn-'+(key=='zg'?'warning':'default')+'">'+value+'</button>';
	}
	return html;
}

function organize_select_html(data,s){
	var html = '';
	if(data != null && data.length>0){
		s = s == ''?'&nbsp':(s + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		for(var i=0;i<data.length;i++){

			var zi_html = organize_select_html(data[i]['zi'],s);
			html += '<option value="'+data[i]['id']+'">'+s+'┣ '+data[i]['DepartmentName']+'</option>'+zi_html;

		}
	}
	return html;
}

/*请求组织架构*/
function post_organize(url,data,dom,time){
	chuli("正在处理，请稍后……");
	time = time!=''?time:500;

	setTimeout(function(){
		$.ajax({
			url: '/order.php'+url,
			type:"post",
			data: data,
			dataType:'json',
			success: function (msg) {
				organize_chuli(data,msg,dom);
			},
			error: function () {
				chuli("");
				toastr.error("提交数据，产生错误");
			}
		})
	},time);
}
































