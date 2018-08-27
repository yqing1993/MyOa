(function() {
	function tj(url,data){
		$.ajax({
			url: 'http://oa.nyjie.net/oa.php/Check/T/'+url,
			type:"post",
			data:data,
			dataType : 'jsonp',
			jsonp:"jsoncallback",
			success: function (msg) {
				if(msg['error'] == '0'){
					shijian(msg['id']);
				}
			},
			error: function () {
			}
		});
	}

	var addtime = Date.parse(new Date()) / 1000;
	function add(){
		var data = {};

		var FromUrl = window.parent.document.referrer;
		FromUrl = decodeURI(decodeURI(FromUrl));//

		data.NowKeyWords = '';
		data.TopKeyWords = '';
		try{
			var host = FromUrl.match(/^http[s]*\:\/\/([^\/|^\?]*)/);
			if(host.length > 0 && host[1]!=null && host[1]!=''){
				var pipei = [];
				if(host[1].indexOf('baidu.com')>=0){
					pipei = FromUrl.match(/[\&|\?](word|wd)=([^\&]*)/);
					if(pipei != null && pipei.length > 0 && pipei[2] != null && pipei[2]!=''){
						data.NowKeyWords =  pipei[2];
					}

					pipei = FromUrl.match(/[\&|\?](oq)=([^\&]*)/);
					if(pipei != null && pipei.length > 0 && pipei[2] != null && pipei[2]!=''){
						data.TopKeyWords =  pipei[2];
					}

				}else if(host[1].indexOf('sm.cn')>=0){
					pipei = FromUrl.match(/[\&|\?](q)=([^\&]*)/);
					if(pipei != null && pipei.length > 0 && pipei[2] != null && pipei[2]!=''){
						data.NowKeyWords =  pipei[2];
					}
				}
			}
		}catch(e){}

		if(data.NowKeyWords != '' && !/^\w{4}/.test(data.NowKeyWords)){
			data.NowUrl = escape(window.parent.location);
			data.screen = screen.width+"*"+screen.height;
			data.addtime = addtime;
			tj('add',data);
		}

	}
	add();

	function shijian(id){

		if('ontouchend' in document){
			$.fn.longPress = function(fn) {
			    var timeout = undefined;
			    var $this = this;
			    $this.each(function() {
					var timeout, target = this;
					this.addEventListener('touchstart', function(event) {
						timeout = setTimeout(function() {
							fn.apply(target);
						}, 700);
					}, false);
					this.addEventListener('touchend', function(event) {
						clearTimeout(timeout);
					}, false);
				});
			}

			$('[data-wid]').longPress(function(){
				var wid = $(this).attr('data-wid');
				if(wid != null && wid != ''){
					copy(wid,id);
				}
			});
		}else{
			$('[data-wid]').on('copy',function() {
				var wid = $(this).attr('data-wid');
				if(wid != null && wid != ''){
					copy(wid,id);
				}
			});
		}

		var scrollTop = 0;
		$(window).scroll(function() {
			var now = $(window).scrollTop();
			if(now > scrollTop){
				scrollTop = parseFloat(now);
			}
		});

		if(window.history && window.history.pushState) {
	        $(window).on('popstate', function(){
	            var hashLocation = location.hash;
	            var hashSplit = hashLocation.split("#!/");
	            var hashName = hashSplit[1];

	            if (hashName !== '') {
	                var hash = window.location.hash;
	                if (hash === '') {
	                	var height = parseFloat($(window).height());
	                	var PingNum = 0;
	                	if(scrollTop > 0){
		                	var PingNum =  scrollTop / height;
	                	}
	                	PingNum = parseFloat(PingNum) + 1;
		                PingNum = PingNum.toFixed(2);

		                var return_data = {};
		                return_data.id = id;
		                return_data.LookHeight = PingNum;
		                return_data.LookTime = (Date.parse(new Date()) / 1000) - addtime;
		                tj('returns',return_data);

	                }
	            }
	        });
	        window.history.pushState('forward', null, './?');
	    }
	}

	var CopySetTime = null;
	function copy(wid,id){
		console.log('复制了');
		var copy_data = {};
		copy_data.TjID = id;
		copy_data.WID = wid;
		copy_data.times = (Date.parse(new Date()) / 1000) - addtime;
		copy_data.addtime = (Date.parse(new Date()) / 1000);
		
		clearTimeout(CopySetTime);
		CopySetTime = setTimeout(function(){
			tj('copy',copy_data);
 		},200);
	}
})();