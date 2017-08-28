
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) NOT NULL DEFAULT '',
  `is_outer` int(11) NOT NULL DEFAULT '-1' COMMENT '1外包 0公司内部分组',
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


INSERT INTO `platform` VALUES ('1', '系统人员管理', '/admin/index/user', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/人员管理.png', '10.10.131.245,106.75.2.33', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:20:07', '2017-08-28 15:00:10');
INSERT INTO `platform` VALUES ('101', '题库MIS系统【作业盒子】', 'http://kbqmis.knowbox.cn/nav.html', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/题库-盒子.png', '10.10.131.245,106.75.2.33', '', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:00:10');
INSERT INTO `platform` VALUES ('102', '题库MIS系统【速算】', 'http://ssqmis.knowbox.cn/nav.html', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/题库-速算.png', '10.10.131.245,106.75.2.33', '', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:00:19');
INSERT INTO `platform` VALUES ('103', '题库MIS系统【单词部落】', 'http://wtqmis.knowbox.cn/nav.html', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/题库-单词.png', '10.10.131.245,106.75.2.33', '', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:00:28');
INSERT INTO `platform` VALUES ('104', '中学英语内容管理平台【单词部落】', 'http://wordcow.platform.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/中学英语.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:00:52');
INSERT INTO `platform` VALUES ('105', '小学语文内容管理平台【速算】', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/小学语文.png', '', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '1', '2017-07-27 20:19:55', '2017-08-28 14:58:15');
INSERT INTO `platform` VALUES ('501', 'KBManage运营活动管理平台', 'http://kbmanage.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/KBManage 运营平台.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:01');
INSERT INTO `platform` VALUES ('502', '学校地区管理平台【速算】', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/学校管理-速.png', '', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '1', '2017-07-27 20:19:55', '2017-08-28 14:58:23');
INSERT INTO `platform` VALUES ('503', '学校地区管理平台【单词部落】', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/学校管理-单.png', '', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '1', '2017-07-27 20:19:55', '2017-08-28 14:58:23');
INSERT INTO `platform` VALUES ('1001', '深蓝数据', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/深蓝.png', '', '', '0', '0', '2017-07-27 20:19:55', '2017-08-28 14:58:07');
INSERT INTO `platform` VALUES ('1002', '深蓝数据管理后台', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/深蓝-后.png', '', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 14:58:07');
INSERT INTO `platform` VALUES ('1003', 'Rubick数据需求导出平台', 'http://rubick.platform.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/rubick-前.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:11');
INSERT INTO `platform` VALUES ('1004', 'Rubick数据需求导出平台管理后台', 'http://rubickmanage.platform.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/rubick-后.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:15');
INSERT INTO `platform` VALUES ('1005', '商业化变现分析平台', 'http://businesslike.test.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/商业化变现分析平台.png', '10.10.213.219,123.59.66.74', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 14:59:53');
INSERT INTO `platform` VALUES ('1006', 'Medivh题库使用分析平台', 'http://medivh.knowbox.cn', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/medivh.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:21');
INSERT INTO `platform` VALUES ('1011', '运营数据监控平台【作业盒子】', 'http://datamonitor.platform.knowbox.cn', 'http://datamonitor.platform.knowbox.cn', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/运营监管-盒子.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:39');
INSERT INTO `platform` VALUES ('1012', '运营数据监控平台【速算】', 'http://datamonitor.platform.knowbox.cn/susuan.html', 'http://datamonitor.platform.knowbox.cn', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/运营监管-速算.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:39');
INSERT INTO `platform` VALUES ('1013', '运营数据监控平台【单词部落】', 'http://worddata.knowbox.cn/app', 'http://octopus_wordtribe.knowbox.cn', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/运营监管-单词.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:39');
INSERT INTO `platform` VALUES ('1100', 'Hera数据分析平台', 'http://hera.platform.knowbox.cn', 'http://zeus.platform.knowbox.cn', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/HeraNew.png', '10.10.66.161,123.59.44.22', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '0', '2017-07-27 20:19:55', '2017-08-28 15:01:39');
INSERT INTO `platform` VALUES ('2000', 'Furion用研中心', '', '', 'http://ov2iw51h2.bkt.clouddn.com/iconkael/fuiron.png', '', '36.110.92.194,36.110.92.195,36.110.92.196,36.110.92.197,36.110.92.198', '1', '1', '2017-07-27 20:19:55', '2017-08-28 15:01:49');


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
  `create_user` int(11) NOT NULL DEFAULT '0',
  `delete_user` int(11) NOT NULL DEFAULT '0',
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

INSERT INTO `relate_user_platform` (`user_id`,`platform_id`)
VALUES (11090,1);


CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `role` VALUES ('0', '普通用户', '0');
INSERT INTO `role` VALUES ('1', '超级管理员', '0');
INSERT INTO `role` VALUES ('2', '部门管理员', '0');


ALTER TABLE `user`
ADD `email` varchar(20) NOT NULL DEFAULT '',
ADD `department_id` int(11) NOT NULL DEFAULT '0',
ADD  `login_ip` varchar(100) NOT NULL COMMENT '上次登录ip',
ADD INDEX (email);

ALTER TABLE `user`
MODIFY COLUMN `update_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;



update `user` set `admin` = 1 where mobile in ('13683602952');
update `user` set `department_id` = 100;
update `user` set `user_type` = 0,`department_id` = 101 where mobile in ('13683602952','15010986303','13811762727');
update `user` set `user_type` = 0,`department_id` = 102 where mobile in ('17319304090');





