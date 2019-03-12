alter table user add email_created tinyint not null default '0';

CREATE TABLE `dingtalk_department` (
  `id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' comment '部门ID',
  `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `parentid` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级部门ID',
  `level` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `subroot_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '主部门id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parentid` (`parentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dingtalk_user` (
  `auto_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '人员ID',
  `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '名称',
  `email` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像',
  `job_number` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '工号',
  `union_id` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'UNIONID',
  `open_id` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'OPENID',
  `departments` varchar(1000) NOT NULL DEFAULT '' COMMENT '部门list',
  `department_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '主部门ID',
  `department_subroot` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '一级部门ID',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`auto_id`),
  UNIQUE KEY `idx_userid` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_unionid` (`union_id`),
  KEY `idx_openid` (`open_id`),
  KEY `idx_jobnumber` (`job_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

