alter table dingtalk_department add column path_id varchar(1000) not null default '' comment '部门链id列表' after path_name;



CREATE TABLE `dingtalk_hrm_user` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID',
  `corp_type` int(11) NOT NULL DEFAULT '0' COMMENT '1主企业 2兼职企业',
  `user_id` varchar(100) NOT NULL DEFAULT '' COMMENT '用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `sexType` varchar(100) NOT NULL DEFAULT '' ,
  `dept` varchar(1000) NOT NULL DEFAULT '' ,
  `mainDept` varchar(255) NOT NULL DEFAULT '' ,
  `position` varchar(50) NOT NULL DEFAULT '' ,
  `mobile` varchar(20) NOT NULL DEFAULT '' ,
  `jobNumber` varchar(100) NOT NULL DEFAULT '' ,
  `tel` varchar(100) NOT NULL DEFAULT '' ,
  `remark` varchar(100) NOT NULL DEFAULT '' ,
  `confirmJoinTime` varchar(100) NOT NULL DEFAULT '' ,
  `employee_type` varchar(100) NOT NULL DEFAULT '' ,
  `employee_status` varchar(100) NOT NULL DEFAULT '' ,
  `birthTime` varchar(100) NOT NULL DEFAULT '' ,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parentid` (`user_id`,`corp_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 comment '钉钉花名册'