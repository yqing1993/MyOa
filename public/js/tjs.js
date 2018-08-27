(function() {
	function tj(url,data){
		$.ajax({
			url: 'http://fit.nyjie.net/oa.php/Check/tj/'+url,
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

	var data = {};
	data.FromUrl = escape(window.parent.document.referrer);
	data.NowUrl = escape(window.parent.location);

	data.screen = screen.width+"*"+screen.height;
	data.addtime = Date.parse(new Date()) / 1000;

	tj('add',data);

	function shijian(id){

		if('ontouchend' in document){
			$.fn.longPress = function(fn) {
			    var timeout = undefined;
			    var $this = this;
			    for(var i = 0;i<$this.length;i++){
			        $this[i].addEventListener('touchstart', function(event) {
			            timeout = setTimeout(fn, 800);
			            }, false);
			        $this[i].addEventListener('touchend', function(event) {
			            clearTimeout(timeout);
			            }, false);
			    }
			}

			var interval;
			$(window).longPress(function(){
				clearInterval(interval);
				interval = setInterval(function() {
					var str = window.getSelection(0).toString();
					if (str != "") {
						clearInterval(interval);
						var selecter = window.getSelection();
						copy(selecter,id);
					}
				}, 10);
			});
		}else{
			document.addEventListener('copy', function(event){
				try {
					if(window.getSelection) {
						var selecter = window.getSelection();
						copy(selecter,id);
						
					}
				} catch (e) {}
			});
		}

		if(window.history && window.history.pushState) {
	        $(window).on('popstate', function() {
	            var hashLocation = location.hash;
	            var hashSplit = hashLocation.split("#!/");
	            var hashName = hashSplit[1];

	            if (hashName !== '') {
	                var hash = window.location.hash;
	                if (hash === '') {
	                	var height = parseFloat($(window).height());
	                	var scrollTop = parseFloat($(document).scrollTop());
	                	var PingNum = 0;
	                	if(scrollTop > 0){
		                	var PingNum =  scrollTop / height;
	                	}
	                	PingNum = parseFloat(PingNum) + 1;
		                PingNum = PingNum.toFixed(2);

		                var return_data = {};
		                return_data.id = id;
		                return_data.LookHeight = PingNum;
		                return_data.LookTime = (Date.parse(new Date()) / 1000) - data.addtime;
		                tj('returns',return_data);

	                }
	            }
	        });
	        window.history.pushState('forward', null, './?');
	    }
	}

	var CopySetTime = null;
	function copy(selecter,id){
		var selectStr = selecter.toString();
		var selectNum = 0;
		try {
			if(selectStr.trim != "") {
				$("#CopyStr").replaceWith($("#CopyStr").text());
				var label = 'span';
			    var rang = selecter.getRangeAt(0);
				rang.insertNode($("<"+label+" id=\"CopyStr\"></"+label+">")[0]);

			    var content = $('body').html();
			    reg = eval("/("+selectStr+"|\<"+label+" id\=\"CopyStr\"\>\<[/]"+label+"\>)/g");
			    var pipei = content.match(reg);
			    for(var i=0;i<pipei.length;i++){
			    	if(pipei[i].indexOf(label)>=0){
			    		selectNum = (i+1);
			    	}
			    }

			    temp.remove();
			}
		} catch (e) {}

		if(selectStr != "" ) {
			var copy_data = {};
			copy_data.id = id;
			copy_data.str = selectStr;
			copy_data.num = selectNum;
			copy_data.times = (Date.parse(new Date()) / 1000) - data.addtime;
			copy_data.copytime = (Date.parse(new Date()) / 1000);
			
			clearTimeout(CopySetTime);
			CopySetTime = setTimeout(function(){
				tj('copy',copy_data);
	 		},200);
		}
	}
})();