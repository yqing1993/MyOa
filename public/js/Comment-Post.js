//加载 css 文件
function includeCss(filename) {
	var head = document.getElementsByTagName('head')[0];
	var link = document.createElement('link');
	link.href = filename;
	link.rel = 'stylesheet';
	link.type = 'text/css';
	head.appendChild(link);
}

//加载 js 文件
function includeJs(filename) {
	var head = document.getElementsByTagName('head')[0];
	var script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	head.appendChild(script)
}
includeCss('/public/bootstrap/css/bootstrap.min.css');
includeCss('/public/css/toastr.min.css');
includeJs('/public/bootstrap/js/bootstrap.min.js');
includeJs('/public/js/toastr.min.js');
includeCss('/public/css/Comment-Post.css');//http://fit.nyjie.net

function on(){
	$(".Star-Click i").click(function(){
		$(".Star-Click i").addClass('gray');
		var index = $(this).index();
		for(var i=0;i<=index;i++){
			$(".Star-Click i").eq(i).removeClass('gray');
		}
	});
}
on();

/*ajax*/
function post_data(url,data,dom,time){
	chuli("正在处理，请稍后……");
	time = time!=''?time:500;

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


