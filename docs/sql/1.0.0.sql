
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) NOT NULL DEFAULT '',
  `is_outer` int(11) NOT NULL DEFAULT '0' COMMENT '是否外包组',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `department` (`department_id`,`department_name`,`is_outer`) VALUES (100,'外包',1),(101,'研发',0),(102,'教研',0),(103,'运营',0),(104,'BD',0);



CREATE TABLE `platform` (
  `platform_id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `platform_url` varchar(255) NOT NULL DEFAULT '' COMMENT '平台地址',
  `platform_api` varchar(255) NOT NULL DEFAULT '' COMMENT '平台接口地址',
  `platform_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '平台图标',
  `server_ips` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器ip 逗号分割',
  `allow_ips` varchar(200) NOT NULL DEFAULT '' COMMENT '访问ip白名单 逗号分割 空为不限制',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '1展示 0不展示',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`platform_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `platform` VALUES ('1', '系统人员管理', '/admin/index/user', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:20:07', '2017-08-05 13:59:01');
INSERT INTO `platform` VALUES ('101', '题库MIS系统【作业盒子】', 'http://entrystore.test.knowbox.cn/nav.html', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('102', '题库MIS系统【速算】', 'http://entrystore_susuan.test.knowbox.cn/nav.html', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('103', '题库MIS系统【单词部落】', 'http://entrystore_word.test.knowbox.cn/nav.html', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('104', '中学英语内容管理平台【单词部落】', 'http://wordcow.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('105', '小学语文内容管理平台【速算】', 'http://yuwencow.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-17 16:19:25');
INSERT INTO `platform` VALUES ('501', 'KBManage运营活动管理平台', 'http://kbmanage.test.knowbox.cn/#http://mytest3.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-15 17:40:24');
INSERT INTO `platform` VALUES ('502', '学校地区管理平台【速算】', 'http://alliance.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('503', '学校地区管理平台【单词部落】', 'http://horde.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('1001', '深蓝数据', 'http://bd.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '0', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:53');
INSERT INTO `platform` VALUES ('1002', '深蓝数据管理后台', 'http://bdmanage.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-15 14:45:55');
INSERT INTO `platform` VALUES ('1003', 'Rubick数据需求导出平台', 'http://export.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1004', 'Rubick数据需求导出平台管理后台', 'http://exportmanage.test.knowbox.cn', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1005', '商业化变现分析平台', 'http://businesstest.test.knowbox.cn/', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-18 16:27:38');
INSERT INTO `platform` VALUES ('1006', 'Medivh题库使用分析平台', 'http://tkcp.test.knowbox.cn/', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1011', '运营数据监控平台【作业盒子】', 'http://datamonitor.platform.knowbox.cn', 'http://datamonitor.platform.knowbox.cn', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1012', '运营数据监控平台【速算】', 'http://datamonitor.platform.knowbox.cn/susuan.html', 'http://datamonitor.platform.knowbox.cn', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1013', '运营数据监控平台【单词部落】', 'http://wttest.platform.knowbox.cn/app', 'http://octest.platform.knowbox.cn', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('1100', 'Hera数据分析平台', 'http://heratest.platform.knowbox.cn', 'http://zeustest.platform.knowbox.cn', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');
INSERT INTO `platform` VALUES ('2000', 'Furion用研中心（接口测试用）', 'http://callcenter.test.knowbox.cn/test.html', '', 'https://knowboxqiniu.knowbox.cn/Fv8rnmprbUSLpup8qLEqPhbtmcds', '', '', '1', '0', '2017-07-27 20:19:55', '2017-08-14 17:49:54');



CREATE TABLE `relate_admin_department` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `platform_id` int(11) NOT NULL DEFAULT '0',
  `create_user` int(11) NOT NULL DEFAULT 0,
  `delete_user` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`),
  INDEX (department_id,platform_id),
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `relate_department_platform` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL DEFAULT '0',
  `platform_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `relate_department_platform` (`department_id`,`platform_id`)
    VALUES (101,1),(101,101),(101,102),(101,103),(101,104),(101,105),(101,501),(101,502),(101,503),
      (101,1001),(101,1002),(101,1003),(101,1004),(101,1005),(101,1006),(101,1007),(101,1008),(101,1009),
      (101,1010),(101,1011),(101,1012),(101,1013),(101,1100),(101,2000),
      (102,101),(102,102),(102,103),(102,104),(102,105),
      (103,501),(103,502),(103,503),(103,1002),
      (100,101),(100,102),(100,103),
      (104,1001);


CREATE TABLE `relate_user_platform` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `platform_id` int(11) NOT NULL DEFAULT 0,
  `create_user` int(11) NOT NULL DEFAULT 0,
  `delete_user` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `user`
ADD `email` varchar(20) NOT NULL DEFAULT '',
ADD `department_id` int(11) NOT NULL DEFAULT '0',
ADD  `login_ip` varchar(100) NOT NULL COMMENT '上次登录ip',
ADD INDEX (email);

update `user` set `admin` = 1 where mobile in ('13683602952');
update `user` set `department_id` = 100;
update `user` set `user_type` = 0,`department_id` = 101 where mobile in ('13683602952','15010986303','13811762727');
update `user` set `user_type` = 0,`department_id` = 102 where mobile in ('17319304090');





