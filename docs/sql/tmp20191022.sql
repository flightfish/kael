alter table dingtalk_department add column path_id varchar(1000) not null default '' comment '部门链id列表' after path_name;



CREATE TABLE `dingtalk_hrm_user` (
  `id` bigint(20) unsigned NOT NULL COMMENT '部门ID' auto_increment ,
  `corp_type` int(11) NOT NULL DEFAULT '0' COMMENT '1主企业 2兼职企业',
  `user_id` varchar(100) NOT NULL DEFAULT '' COMMENT '用户ID',
  `name` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '' ,
  `email` varchar(255) NOT NULL DEFAULT '',
  `job_number` varchar(100) NOT NULL DEFAULT '' ,
  `sex` varchar(10) NOT NULL DEFAULT '' ,
  `main_dept_id` bigint(20) NOT NULL DEFAULT '0' ,
  `main_dept_name` varchar(255) NOT NULL DEFAULT '' ,
  `employee_type` varchar(100) NOT NULL DEFAULT '' ,
  `employee_status` varchar(100) NOT NULL DEFAULT '' ,
  `birth_time` varchar(20) NOT NULL DEFAULT '' ,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_userid_corytype` (`user_id`,`corp_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 comment '钉钉花名册';

alter table relate_user_platform add index idx_userid_platformid (user_id,platform_id);