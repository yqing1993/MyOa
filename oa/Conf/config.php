<?php
return array(
	//'配置项'=>'配置值
    //'URL_CASE_INSENSITIVE'=> true,
	'LOG_RECORD' => false,
	'DB_TYPE'      =>  'mysql',     // 数据库类型

	 'DB_HOST'      =>  'localhost',     // 服务器地址
	 'DB_USER'      =>  'root',     // 用户名
	 'DB_PWD'       =>  'root',     // 密码
	 'DB_NAME'      =>  'wpoa',   // 数据库名

//	'DB_HOST'      =>  'hdm144528164.my3w.com',     // 服务器地址
//	'DB_USER'      =>  'hdm144528164',     // 用户名
//	'DB_PWD'       =>  'wpkjmysql',     // 密码
//	'DB_NAME'      =>  'hdm144528164_db',    // 数据库名

    //测试主机
//    'DB_HOST'      =>  'localhost',     // 服务器地址
//    'DB_USER'      =>  's1022247db0',     // 用户名
//    'DB_PWD'       =>  'e05dc22a',     // 密码
//    'DB_NAME'      =>  's1022247db0',    // 数据库名

	'DB_PORT'      =>  3306,     // 端口
	'DB_PREFIX'    =>  'wp_',     // 数据库表前缀
	'DB_CHARSET'   =>  'utf8', // 数据库的编码 默认为utf8

	'APP_GROUP_LIST' => 'OA,Check,Bidding,Keywords,Love', //项目分组设定
	'DEFAULT_GROUP'  => 'OA', //默认分组

	'TMPL_FILE_DEPR'=>'_',

	'OrderUrl'=>'http://oady.spbartcenter.net/',


	'module'=>array(
		'后台管理'=>array(
			'UserManage'=>'用户管理',
			'RoleManage'=>'角色管理',
			'Department'=>'部门管理',
			'LIDLookcode'=>'查看码',
            'PublishMessage'=>'发布企业消息'
		),

		'排班管理'=>array(
			'ClassUser'=>'人员设置',
			'ClassCi'=>'班次设置',
			'ClassPai'=>'排班管理',
			'Myclass'=>'我的排班'
		), 


		'竞价模块'=>array(
			'ProjectManage'=>'项目管理',
			'WechatManage'=>'微信号管理',
			'PlatformManage'=>'平台管理',
			'HuManage'=>'户管理',
			'ZhaoWeb'=>'着陆页管理',
			'TuiWeb'=>'推广管理',

			'RecordXiao'=>'消费录入',
			'RecordYuhu'=>'预算录入',
			'RecordChong'=>'充值返点录入',

			'LIDProject'=>'项目数据',
			'LIDHu'=>'户数据',
			'LIDTui'=>'推广数据',
			'LIDWechat'=>'微信数据',
			'LIDZhao'=>'着陆页数据',

			'LXiangXi'=>'详细数据',
			'LXiaoGuo'=>'效果分析',
			'LHeXin'=>'核心分析',

			// 'TongjiSet'=>'统计代码设置',
			// 'TjVisitLook'=>'访问统计',
			// 'TjCopyLook'=>'复制统计'
		),

		'关键词模板'=>array(
		    'KeBs'=>'是否属于关键词模块',
            'KeCategory'=>'类型管理',
            'KeProject'=>'项目词管理',
            'KeType'=>'类别词管理',
            'KeKeyword'=>'关键词管理',
            'KeSearch'=>'搜索级别管理',
            'InsertSearch'=>'搜索级别录入',

            'KeSp'=>'审批管理',

            'KeDistribution'=>'关键词分配管理',

            'InsertBotox'=>'瘦脸录入',
            'InsertJianf'=>'减肥录入',
            'InsertYangs'=>'养生录入',
            'InsertBrand'=>'品牌词录入',

            'KeGet'=>'品牌词获取',

//            'LookKeywords'=>'关键词查看',

            'LogKeyword'=>'是否能被分配关键词'
        ),
		'爱心系统'=>array(
            'SpLove'=>'爱心审批',
        ),

		'数据预览'=>array(
			'Log'=>'操作日志'
		)
	),

	'function'=>array('DelLog'=>'清除日志'),

	'WechatType'=>array('sq'=>'售前号','sh'=>'售后号'),

	'citys'=>array(
		array(),
		array("直辖市",array("","北京市","上海市","天津市","重庆市")),
		array("浙江省",array("","杭州市","宁波市","温州市","嘉兴市","湖州市","绍兴市","金华市","衢州市","舟山市","台州市","丽水市")),
		array("江苏省",array("","南京市","无锡市","徐州市","常州市","苏州市","南通市","连云港市","淮安市","盐城市","扬州市","镇江市","泰州市","宿迁市")),
		array("广东省",array("","广州市","韶关市","深圳市","珠海市","汕头市","佛山市","江门市","湛江市","茂名市","肇庆市","惠州市","梅州市","汕尾市","河源市","阳江市","清远市","东莞市","中山市","潮州市","揭阳市","云浮市")),
		array("福建省",array("","福州市","厦门市","莆田市","三明市","泉州市","漳州市","南平市","龙岩市","宁德市")),
		array("湖南省",array("","长沙市","株洲市","湘潭市","衡阳市","邵阳市","岳阳市","常德市","张家界","益阳市","郴州市","永州市","怀化市","娄底市","湘西市","吉首市")),
		array("湖北省",array("","武汉市","黄石市","十堰市","宜昌市","襄阳市","鄂州市","荆门市","孝感市","荆州市","黄冈市","咸宁市","随州市","恩施市","仙桃市","潜江市","江汉市","神农架")),
		array("辽宁省",array("","沈阳市","大连市","鞍山市","抚顺市","本溪市","丹东市","锦州市","营口市","阜新市","辽阳市","盘锦市","铁岭市","朝阳市","葫芦岛市")),
		array("吉林省",array("","长春市","吉林市","四平市","辽源市","通化市","白山市","松原市","白城市","延边市","珲春市","梅河口市","延吉市")),
		array("黑龙江",array("","哈尔滨市","齐齐哈尔市","鸡西市","鹤岗市","双鸭山市","大庆市","伊春市","佳木斯市","七台河市","牡丹江市","黑河市","绥化市","大兴安岭市")),
		array("河北省",array("","石家庄市","唐山市","秦皇岛市","邯郸市","邢台市","保定市","张家口市","承德市","沧州市","廊坊市","衡水市")),
		array("河南省",array("","郑州市","开封市","洛阳市","平顶山市","安阳市","鹤壁市","新乡市","焦作市","濮阳市","许昌市","漯河市","三门峡市","南阳市","商丘市","信阳市","周口市","驻马店市","济源市")),
		array("山东省",array("","济南市","青岛市","淄博市","枣庄市","东营市","烟台市","潍坊市","济宁市","泰安市","威海市","日照市","莱芜市","临沂市","德州市","聊城市","滨州市","菏泽市")),
		array("陕西省",array("","西安市","铜川市","宝鸡市","咸阳市","渭南市","延安市","汉中市","榆林市","安康市","商洛市")),
		array("甘肃省",array("","兰州市","嘉峪关","金昌市","白银市","天水市","武威市","张掖市","平凉市","酒泉市","庆阳市","定西市","陇南市","临夏市","甘南市","西峰市")),
		array("青海省",array("","西宁市","海东市","海北市","黄南市","海南市","果洛市","玉树市","海西市","德令哈市","格尔木市","共和市","玛沁市","同仁市")),
		array("新疆",array("","乌鲁木齐","克拉玛依","吐鲁番","哈密","昌吉","博尔塔拉","巴音郭楞","阿克苏","克孜勒苏","喀什","和田","伊犁","塔城","阿勒泰","石河子","奎屯","库尔勒","阿图什","博乐")),
		array("山西省",array("","太原市","大同市","阳泉市","长治市","晋城市","朔州市","晋中市","运城市","忻州市","临汾市","吕梁市")),
		array("四川省",array("","成都市","自贡市","攀枝花","泸州市","德阳市","绵阳市","广元市","遂宁市","内江市","乐山市","南充市","眉山市","宜宾市","广安市","达州市","雅安市","巴中市","资阳市","阿坝市","甘孜市","凉山市","西昌市")),
		array("贵州省",array("","贵阳市","六盘水市","遵义市","安顺市","铜仁市","黔东南市","黔南市","黔西南市","毕节市","都匀市","凯里市","兴义市")),
		array("安徽省",array("","合肥市","芜湖市","蚌埠市","淮南市","马鞍山市","淮北市","铜陵市","安庆市","黄山市","巢湖市","滁州市","阜阳市","宿州市","六安市","亳州市","池州市","宣城市")),
		array("江西省",array("","南昌市","景德镇","萍乡市","九江市","新余市","鹰潭市","赣州市","吉安市","宜春市","抚州市","上饶市")),
		array("云南省",array("","昆明市","曲靖市","玉溪市","保山市","昭通市","丽江市","普洱市","临沧市","楚雄市","红河市","文山市","西双版纳","大理市","德宏市","怒江市","迪庆市")),
		array("内蒙古",array("","呼和浩特","包头市","乌海市","赤峰市","通辽市","鄂尔多斯","呼伦贝尔","巴彦淖尔","乌兰察布","兴安盟","锡林郭勒","阿拉善")),
		array("广西",array("","南宁市","柳州市","桂林市","梧州市","北海市","防城港","钦州市","贵港市","玉林市","百色市","贺州市","河池市","来宾市","崇左市")),
		array("西藏",array("","拉萨","昌都","山南","日喀则","那曲","阿里","林芝")),
		array("宁夏",array("","银川市","石嘴山市","吴忠市","固原市","中卫市")),
		array("海南省",array("","海口市","三亚市")),
		array("国外",array("","自定义"))
	 ),

	/*职位*/
	'Duty'=>array('zg'=>'主管','zz'=>'组长','yg'=>'员工'),

	/*时间点*/
	'TimeDian'=>array(
		array('00:00','01:00'),
		array('01:00','02:00'),
		array('02:00','03:00'),
		array('03:00','04:00'),
		array('04:00','05:00'),
		array('05:00','06:00'),
		array('06:00','07:00'),
		array('07:00','08:00'),
		array('08:00','09:00'),
		array('09:00','10:00'),
		array('10:00','11:00'),
		array('11:00','12:00'),
		array('12:00','13:00'),
		array('13:00','14:00'),
		array('14:00','15:00'),
		array('15:00','16:00'),
		array('16:00','17:00'),
		array('17:00','18:00'),
		array('18:00','19:00'),
		array('19:00','20:00'),
		array('20:00','21:00'),
		array('21:00','22:00'),
		array('22:00','23:00'),
		array('23:00','00:00')
	),

	/*班制*/
	'ClassType'=>array(
		array('name'=>'正常','start'=>'08:30:00','end'=>'17:30:00'),
		array('name'=>'早班','start'=>'08:30:00','end'=>'15:45:00'),
		array('name'=>'晚班','start'=>'15:45:00','end'=>'23:00:00')
	)
);
?>