
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) NOT NULL DEFAULT '',
  `is_outer` int(11) NOT NULL DEFAULT '0' COMMENT '是否外包组',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `platform` (
  `platform_id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `platform_url` varchar(255) NOT NULL DEFAULT '' COMMENT '平台地址',
  `platform_api` varchar(255) NOT NULL DEFAULT '' COMMENT '平台接口地址',
  `platform_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '平台图标',
  `server_ips` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器ip 逗号分割',
  `allow_ips` varchar(200) NOT NULL DEFAULT '' COMMENT '访问ip白名单 逗号分割 空为不限制',
  `is_show` int(11) NOT NULL DEFAULT 1 COMMENT '1展示 0不展示',
  `status` int(11) NOT NULL DEFAULT 0,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`platform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
