/*
Navicat MySQL Data Transfer

Source Server         : oa
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : wpoa

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-08-25 15:19:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wp_classci
-- ----------------------------
DROP TABLE IF EXISTS `wp_classci`;
CREATE TABLE `wp_classci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ClassCiName` varchar(50) NOT NULL COMMENT '班次名称',
  `Wechat` varchar(1000) NOT NULL COMMENT '微信号',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1不正常',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_classcicopy
-- ----------------------------
DROP TABLE IF EXISTS `wp_classcicopy`;
CREATE TABLE `wp_classcicopy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '班次表的id',
  `Wechat` varchar(255) NOT NULL,
  `ctime` int(10) NOT NULL,
  `classname` varchar(255) NOT NULL COMMENT '父级的名字',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=362 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_classpai
-- ----------------------------
DROP TABLE IF EXISTS `wp_classpai`;
CREATE TABLE `wp_classpai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '日期时间戳',
  `days` varchar(20) NOT NULL COMMENT '日期',
  `userid` varchar(50) NOT NULL COMMENT '员工ID',
  `DepartmentID` int(3) NOT NULL DEFAULT '0' COMMENT '员工部门ID',
  `UserInfo` varchar(500) NOT NULL COMMENT '人员信息',
  `ClassType` int(1) NOT NULL DEFAULT '0' COMMENT '班制，0默认，1早班，2晚班',
  `StartTime` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `EndTime` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `ClassCiInfo` varchar(1000) NOT NULL COMMENT '班次',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7047 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_classuser
-- ----------------------------
DROP TABLE IF EXISTS `wp_classuser`;
CREATE TABLE `wp_classuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) NOT NULL COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `DepartmentID` varchar(11) NOT NULL COMMENT '所属部门ID',
  `DepartmentName` varchar(20) NOT NULL COMMENT '所属部门名称',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1不正常',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_department
-- ----------------------------
DROP TABLE IF EXISTS `wp_department`;
CREATE TABLE `wp_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DepartmentName` varchar(20) NOT NULL COMMENT '部门名称',
  `parentID` varchar(11) NOT NULL COMMENT '父部门ID',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `LookCode` varchar(20) NOT NULL COMMENT '查看码',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1不正常',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='部门表';

-- ----------------------------
-- Table structure for wp_department_time
-- ----------------------------
DROP TABLE IF EXISTS `wp_department_time`;
CREATE TABLE `wp_department_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) NOT NULL COMMENT '员工ID',
  `username` varchar(50) NOT NULL COMMENT '员工姓名',
  `DepartmentID` varchar(10) NOT NULL COMMENT '部门ID',
  `DepartmentName` varchar(20) NOT NULL COMMENT '部门名称',
  `StartTime` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间戳',
  `StartTimes` varchar(25) NOT NULL COMMENT '开始时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='员工部门变动时间表';

-- ----------------------------
-- Table structure for wp_j_hu
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_hu`;
CREATE TABLE `wp_j_hu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `HuName` varchar(20) NOT NULL COMMENT '户名称',
  `PlatformID` varchar(10) NOT NULL COMMENT '所属平台ID',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_platform
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_platform`;
CREATE TABLE `wp_j_platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PlatformName` varchar(20) NOT NULL COMMENT '平台名称',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_project
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_project`;
CREATE TABLE `wp_j_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectName` varchar(20) NOT NULL COMMENT '项目名称',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_recordchong
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_recordchong`;
CREATE TABLE `wp_j_recordchong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PlatformID` int(11) NOT NULL COMMENT '所属平台ID',
  `PlatformInfo` varchar(500) NOT NULL COMMENT '平台信息',
  `HuID` int(11) NOT NULL COMMENT '所属户ID',
  `HuInfo` varchar(500) NOT NULL COMMENT '户信息',
  `Day` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `Fan` decimal(10,2) NOT NULL COMMENT '返点',
  `CBi` decimal(10,2) NOT NULL COMMENT '充值币',
  `CRMB` decimal(10,2) NOT NULL COMMENT '充值人民币',
  `SBi` decimal(10,2) NOT NULL COMMENT '剩余币',
  `SRMB` decimal(10,2) NOT NULL COMMENT '剩余人民币',
  `ps` varchar(200) NOT NULL COMMENT '备注',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_recordxiao
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_recordxiao`;
CREATE TABLE `wp_j_recordxiao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL COMMENT '所属项目ID',
  `ProjectInfo` varchar(500) NOT NULL COMMENT '项目信息',
  `PlatformID` int(11) NOT NULL COMMENT '所属平台ID',
  `PlatformInfo` varchar(500) NOT NULL COMMENT '平台信息',
  `HuID` int(11) NOT NULL COMMENT '所属户ID',
  `HuInfo` varchar(500) NOT NULL COMMENT '户信息',
  `ZhaoWebID` int(11) NOT NULL COMMENT '着陆页ID',
  `ZhaoWebInfo` varchar(500) NOT NULL COMMENT '着陆页信息',
  `WechatID` varchar(50) NOT NULL COMMENT '微信号ID',
  `WechatInfo` varchar(500) NOT NULL COMMENT '微信信息',
  `TuiWebID` int(11) NOT NULL COMMENT '推广页ID',
  `TuiWebInfo` varchar(500) NOT NULL COMMENT '推广页信息',
  `StartTime` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `EndTime` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `Bi` decimal(10,2) NOT NULL COMMENT '消费币',
  `Click` int(11) NOT NULL COMMENT '点击',
  `ps` varchar(200) NOT NULL COMMENT '备注',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1267 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_recordyu
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_recordyu`;
CREATE TABLE `wp_j_recordyu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL COMMENT '所属项目ID',
  `ProjectInfo` varchar(500) NOT NULL COMMENT '项目信息',
  `PlatformID` int(11) NOT NULL COMMENT '所属平台ID',
  `PlatformInfo` varchar(500) NOT NULL COMMENT '平台信息',
  `HuID` int(11) NOT NULL COMMENT '所属户ID',
  `HuInfo` varchar(500) NOT NULL COMMENT '户信息',
  `ZhaoWebID` int(11) NOT NULL COMMENT '着陆页ID',
  `ZhaoWebInfo` varchar(500) NOT NULL COMMENT '着陆页信息',
  `WechatID` varchar(50) NOT NULL COMMENT '微信号ID',
  `WechatInfo` varchar(500) NOT NULL COMMENT '微信信息',
  `TuiWebID` int(11) NOT NULL COMMENT '推广页ID',
  `TuiWebInfo` varchar(500) NOT NULL COMMENT '推广页信息',
  `Day` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `Money` decimal(10,2) NOT NULL COMMENT '预算人民币',
  `ps` varchar(200) NOT NULL COMMENT '备注',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_recordyuhu
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_recordyuhu`;
CREATE TABLE `wp_j_recordyuhu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PlatformID` int(11) NOT NULL COMMENT '所属平台ID',
  `PlatformInfo` varchar(500) NOT NULL COMMENT '平台信息',
  `HuID` int(11) NOT NULL COMMENT '所属户ID',
  `HuInfo` varchar(500) NOT NULL COMMENT '户信息',
  `Day` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `Money` decimal(10,2) NOT NULL COMMENT '预算人民币',
  `ps` varchar(200) NOT NULL COMMENT '备注',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_tuiweb
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_tuiweb`;
CREATE TABLE `wp_j_tuiweb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TuiWebName` varchar(20) NOT NULL COMMENT '推广页名称',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `PlatformID` varchar(10) NOT NULL COMMENT '所属平台ID',
  `HuID` varchar(10) NOT NULL COMMENT '所属户ID',
  `ZhaoWebID` varchar(10) NOT NULL COMMENT '着陆页ID',
  `WechatID` varchar(10) NOT NULL COMMENT '微信号ID',
  `TuiWebUrl` varchar(500) NOT NULL COMMENT '推广网址',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_wechat
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_wechat`;
CREATE TABLE `wp_j_wechat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `WechatName` varchar(50) NOT NULL COMMENT '微信名称',
  `WechatID` varchar(50) NOT NULL COMMENT '微信ID',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `WechatType` varchar(5) NOT NULL COMMENT '微信类型',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(1) NOT NULL DEFAULT '0',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_wechat_time
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_wechat_time`;
CREATE TABLE `wp_j_wechat_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `WechatName` varchar(50) NOT NULL COMMENT '微信名称',
  `WechatID` varchar(50) NOT NULL COMMENT '微信ID',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `ProjectName` varchar(10) NOT NULL COMMENT '项目名称',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_j_zhaoweb
-- ----------------------------
DROP TABLE IF EXISTS `wp_j_zhaoweb`;
CREATE TABLE `wp_j_zhaoweb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ZhaoWebName` varchar(20) NOT NULL COMMENT '着陆页名称',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_keywords
-- ----------------------------
DROP TABLE IF EXISTS `wp_keywords`;
CREATE TABLE `wp_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(100) NOT NULL COMMENT '关键词名称',
  `TypeID` varchar(20) NOT NULL DEFAULT '0' COMMENT '行业关键词所属的类型词名称//0表示当前这个词是属于品牌词',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `CategoryID` varchar(10) NOT NULL COMMENT '所属分类ID',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `sp_status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未审批，1审批',
  `sp_res` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未通过，1通过',
  `rank_search` varchar(30) NOT NULL COMMENT '搜索词的级别',
  `Dis_cishu` int(11) NOT NULL DEFAULT '0' COMMENT '分配的次数',
  `Acc_cishu` int(11) NOT NULL DEFAULT '0' COMMENT '接受的次数',
  `userid` varchar(50) NOT NULL COMMENT '提交人ID',
  `username` varchar(50) NOT NULL COMMENT '提交人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '提交时间',
  `sp_time` int(11) NOT NULL DEFAULT '0' COMMENT '审批时间',
  `sp_userid` varchar(50) NOT NULL COMMENT '审批人ID',
  `sp_username` varchar(50) NOT NULL COMMENT '审批人姓名',
  `sp_role` int(1) NOT NULL DEFAULT '0' COMMENT '状态0无需审批，1超级管理员审批，2品【品牌专员审批',
  `search_number` varchar(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32540 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_category
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_category`;
CREATE TABLE `wp_k_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(255) NOT NULL COMMENT '类别名称',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_distribution
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_distribution`;
CREATE TABLE `wp_k_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '分配的关键词',
  `ProjectID` varchar(10) NOT NULL COMMENT '分配的关键词所属项目ID',
  `Dis_status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未接受，1接受',
  `username` varchar(50) NOT NULL COMMENT '被分配人的姓名',
  `Dis_time` int(11) NOT NULL DEFAULT '0' COMMENT '分配的时间',
  `is_operate` int(1) NOT NULL DEFAULT '0' COMMENT '分配的关键词是否被操作',
  `d_username` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_project
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_project`;
CREATE TABLE `wp_k_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectName` varchar(255) NOT NULL COMMENT '项目词名称',
  `CategoryID` varchar(10) NOT NULL COMMENT '所属类别ID',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_search
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_search`;
CREATE TABLE `wp_k_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `SearchCategory` varchar(30) NOT NULL COMMENT '搜索词的级别',
  `SearchRange` varchar(55) NOT NULL COMMENT '搜索词的级别范围',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_sp_admin
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_sp_admin`;
CREATE TABLE `wp_k_sp_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(100) NOT NULL COMMENT '提交的关键词',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `TypeID` varchar(80) NOT NULL COMMENT '行业关键词所属的类型词名称',
  `sp_status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未审批，1审批',
  `sp_res` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未通过，1通过',
  `userid` varchar(50) NOT NULL COMMENT '提交人ID',
  `username` varchar(50) NOT NULL COMMENT '提交人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '提交时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_sp_normal
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_sp_normal`;
CREATE TABLE `wp_k_sp_normal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(100) NOT NULL COMMENT '提交的关键词',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `TypeID` varchar(80) NOT NULL COMMENT '行业关键词所属的类型词名称',
  `sp_status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未审批，1审批',
  `sp_res` int(1) NOT NULL DEFAULT '0' COMMENT '状态0未通过，1通过',
  `userid` varchar(50) NOT NULL COMMENT '提交人ID',
  `username` varchar(50) NOT NULL COMMENT '提交人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '提交时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_k_type
-- ----------------------------
DROP TABLE IF EXISTS `wp_k_type`;
CREATE TABLE `wp_k_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TypeName` varchar(255) NOT NULL COMMENT '类型词名称',
  `ProjectID` varchar(10) NOT NULL COMMENT '所属项目ID',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态0正常，1不正常',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `userid` varchar(50) NOT NULL COMMENT '更新人ID',
  `username` varchar(50) NOT NULL COMMENT '更新人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_agent
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_agent`;
CREATE TABLE `wp_list_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Akey` int(11) NOT NULL COMMENT '0系统，1浏览器，2尺寸',
  `Avalue` varchar(50) NOT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_copy
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_copy`;
CREATE TABLE `wp_list_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `VisitID` int(11) NOT NULL DEFAULT '0' COMMENT '访问ID',
  `StrID` int(11) NOT NULL COMMENT '复制文本ID',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '第几个',
  `times` int(11) NOT NULL DEFAULT '0' COMMENT '多长时间复制',
  `copytime` int(11) NOT NULL DEFAULT '0' COMMENT '复制时间',
  PRIMARY KEY (`id`),
  KEY `VisitID` (`VisitID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_copystr
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_copystr`;
CREATE TABLE `wp_list_copystr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str` varchar(500) NOT NULL COMMENT '拷贝内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_fromurl
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_fromurl`;
CREATE TABLE `wp_list_fromurl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_keywords
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_keywords`;
CREATE TABLE `wp_list_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL COMMENT '关键字类型，0当前，1上一个关键字',
  `KeyWords` varchar(100) NOT NULL COMMENT '关键字',
  PRIMARY KEY (`id`),
  KEY `KeyWords` (`KeyWords`)
) ENGINE=InnoDB AUTO_INCREMENT=578 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_nowurl
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_nowurl`;
CREATE TABLE `wp_list_nowurl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_list_visit
-- ----------------------------
DROP TABLE IF EXISTS `wp_list_visit`;
CREATE TABLE `wp_list_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(26) NOT NULL COMMENT '访问者ID',
  `NowUrlID` int(11) NOT NULL COMMENT '页面ID',
  `FromUrlID` int(11) NOT NULL COMMENT '来源网址ID',
  `platformID` int(2) NOT NULL COMMENT '来源平台ID',
  `KeyWordsID` int(11) NOT NULL COMMENT '关键字',
  `TopKeyWordsID` int(11) NOT NULL DEFAULT '0' COMMENT '上一个关键字ID',
  `copy` int(1) NOT NULL DEFAULT '0' COMMENT '是否复制，0没有，1复制了',
  `ip` int(11) NOT NULL DEFAULT '0' COMMENT '访问者IP',
  `provice` int(2) NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(2) NOT NULL DEFAULT '0' COMMENT '城市',
  `systemID` int(2) NOT NULL DEFAULT '0' COMMENT '系统ID',
  `BrowserID` int(11) NOT NULL DEFAULT '0' COMMENT '浏览器ID',
  `screenID` int(11) NOT NULL DEFAULT '0' COMMENT '屏幕尺寸ID',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `LookTime` int(11) NOT NULL DEFAULT '0' COMMENT '停留时长',
  `LookHeight` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '浏览高度',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NowUrlID` (`NowUrlID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_love
-- ----------------------------
DROP TABLE IF EXISTS `wp_love`;
CREATE TABLE `wp_love` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `send_user` varchar(30) NOT NULL COMMENT '赠送者',
  `acc_user` varchar(30) NOT NULL COMMENT '被赠送者',
  `reason` varchar(255) NOT NULL COMMENT '赠送原因',
  `send_time` int(11) NOT NULL COMMENT '赠送时间',
  `sp_status` int(1) NOT NULL DEFAULT '0' COMMENT '0代表未审批，1代表已审批',
  `sp_res` int(1) NOT NULL DEFAULT '0' COMMENT '0代表未通过，1代表通过',
  `fail_reason` varchar(255) DEFAULT NULL COMMENT '审批不通过原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_l_agent
-- ----------------------------
DROP TABLE IF EXISTS `wp_l_agent`;
CREATE TABLE `wp_l_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Akey` int(11) NOT NULL COMMENT '0系统，1浏览器，2尺寸',
  `Avalue` varchar(50) NOT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=635 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_l_copy
-- ----------------------------
DROP TABLE IF EXISTS `wp_l_copy`;
CREATE TABLE `wp_l_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TjID` int(11) NOT NULL DEFAULT '0' COMMENT '访问ID',
  `WID` int(11) NOT NULL COMMENT '位置',
  `times` int(11) NOT NULL DEFAULT '0' COMMENT '多长时间复制',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '复制时间',
  `TimeDian` int(11) DEFAULT NULL COMMENT '复制时间点',
  PRIMARY KEY (`id`),
  KEY `TjID` (`TjID`),
  KEY `times` (`times`)
) ENGINE=InnoDB AUTO_INCREMENT=10301 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_l_keywords
-- ----------------------------
DROP TABLE IF EXISTS `wp_l_keywords`;
CREATE TABLE `wp_l_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL COMMENT '关键字类型，0当前，1上一个关键字',
  `KeyWords` varchar(100) NOT NULL COMMENT '关键字',
  PRIMARY KEY (`id`),
  KEY `KeyWords` (`KeyWords`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_l_nowurl
-- ----------------------------
DROP TABLE IF EXISTS `wp_l_nowurl`;
CREATE TABLE `wp_l_nowurl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_l_tj
-- ----------------------------
DROP TABLE IF EXISTS `wp_l_tj`;
CREATE TABLE `wp_l_tj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL DEFAULT '0' COMMENT '项目ID',
  `PlatformID` int(11) NOT NULL DEFAULT '0' COMMENT '平台ID',
  `HuID` int(11) NOT NULL DEFAULT '0' COMMENT '户ID',
  `ZhaoWebID` int(11) NOT NULL DEFAULT '0' COMMENT '着陆页ID',
  `TuiWebID` int(11) NOT NULL DEFAULT '0' COMMENT '推广页ID',
  `WechatID` varchar(30) NOT NULL COMMENT '微信ID',
  `NowUrlID` int(11) NOT NULL DEFAULT '0' COMMENT '当前页面ID',
  `FromUrlID` int(11) NOT NULL COMMENT '来源网址ID',
  `KeyWordsID` int(11) NOT NULL COMMENT '关键字',
  `TopKeyWordsID` int(11) NOT NULL DEFAULT '0' COMMENT '上一个关键字ID',
  `copy` int(1) NOT NULL DEFAULT '0' COMMENT '是否复制，0没有，1复制了',
  `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '访问者IP',
  `provice` int(2) NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(2) NOT NULL DEFAULT '0' COMMENT '城市',
  `SystemID` int(2) NOT NULL DEFAULT '0' COMMENT '系统ID',
  `BrowserID` int(11) NOT NULL DEFAULT '0' COMMENT '浏览器ID',
  `ScreenID` int(11) NOT NULL DEFAULT '0' COMMENT '屏幕尺寸ID',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `TimeDian` int(11) DEFAULT NULL COMMENT '访问时间点',
  `LookTime` int(11) NOT NULL DEFAULT '0' COMMENT '停留时长',
  `LookHeight` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '浏览高度',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NowUrlID` (`NowUrlID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_message
-- ----------------------------
DROP TABLE IF EXISTS `wp_message`;
CREATE TABLE `wp_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '0' COMMENT '消息名称',
  `content` text COMMENT '消息内容',
  `noread` varchar(255) DEFAULT NULL,
  `readed` varchar(255) DEFAULT NULL,
  `dapts` varchar(255) DEFAULT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `publisher` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_record
-- ----------------------------
DROP TABLE IF EXISTS `wp_record`;
CREATE TABLE `wp_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL COMMENT '日志类型',
  `content` varchar(100) NOT NULL COMMENT '日志内容',
  `userid` varchar(50) NOT NULL COMMENT '操作人ID',
  `username` varchar(50) NOT NULL COMMENT '操作人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2859 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_role
-- ----------------------------
DROP TABLE IF EXISTS `wp_role`;
CREATE TABLE `wp_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(20) NOT NULL COMMENT '角色名称',
  `module` varchar(9999) NOT NULL COMMENT '功能模块',
  `function` varchar(999) NOT NULL COMMENT '功能权限',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1停用',
  `userid` varchar(50) NOT NULL COMMENT '添加人ID',
  `username` varchar(50) NOT NULL COMMENT '添加人姓名',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wp_user
-- ----------------------------
DROP TABLE IF EXISTS `wp_user`;
CREATE TABLE `wp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) NOT NULL COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码MD5加密',
  `mac` varchar(200) NOT NULL,
  `MacSwitch` int(1) NOT NULL DEFAULT '0' COMMENT 'mac验证开关，1不验证，0验证',
  `role` varchar(200) NOT NULL COMMENT '角色',
  `DepartmentID` varchar(11) NOT NULL COMMENT '所属部门ID',
  `DepartmentName` varchar(20) NOT NULL COMMENT '所属部门名称',
  `Duty` varchar(10) NOT NULL COMMENT '职位',
  `DutyName` varchar(20) NOT NULL COMMENT '职位名称',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1不正常',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
