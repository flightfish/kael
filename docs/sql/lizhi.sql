/**
  "dept_list": [{
		"dept_id": 121327403,
		"dept_path": "业务发展中心-业务发展中心5组-业务发展中心5.2组-业务发展中心5.2.3组-业务发展中心5.2.3.1组"
	}],
	"last_work_day": 1571241600000,
	"main_dept_id": 121327403,
	"main_dept_name": "业务发展中心5.2.3.1组",
	"pre_status": 3,
	"reason_memo": "",
	"status": 2,
	"userid": "174662400639955471"
 */
CREATE TABLE `dingtalk_hrm_user_leave` (
 `id` bigint(20) unsigned NOT NULL COMMENT '部门ID' auto_increment ,
 `corp_type` int(11) NOT NULL DEFAULT '0' COMMENT '1主企业 2兼职企业',
 `user_id` varchar(100) NOT NULL DEFAULT '' COMMENT '用户ID',
 `last_work_day` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后工作时间',
 `reason_memo` varchar(2000) not null default '' comment '离职原因备注',
 `reason_type` int(11) not null default '0' comment '离职原因类型：1，家庭原因；2，个人原因；3，发展原因；4，合同到期不续签；5，协议解除；6，无法胜任工作；7，经济性裁员；8，严重违法违纪；9，其他',
 `pre_status` int(11) NOT NULL DEFAULT '0' COMMENT '离职前工作状态：1，待入职；2，试用期；3，正式',
 `handover_userid` varchar(100) NOT NULL DEFAULT '' COMMENT '离职交接人',
 `ding_status` int(11) NOT NULL DEFAULT '0' COMMENT '离职状态：1，待离职；2，已离职',
 `main_dept_name` varchar(255) NOT NULL DEFAULT '' COMMENT '离职前主部门名称',
 `main_dept_id` varchar(255) NOT NULL DEFAULT '' COMMENT '离职前主部门id',
 `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `status` tinyint(4) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `idx_userid_corytype` (`user_id`,`corp_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 comment '钉钉花名册-离职';

CREATE TABLE `dingtalk_hrm_user_leave_dept` (
   `id` bigint(20) unsigned NOT NULL COMMENT '部门ID' auto_increment ,
   `corp_type` int(11) NOT NULL DEFAULT '0' COMMENT '1主企业 2兼职企业',
   `user_id` varchar(100) NOT NULL DEFAULT '' COMMENT '用户ID',
   `dept_id` bigint(20) not null default '0' comment '离职前部门id',
   `dept_path` varchar(500) not null default '' comment '离职前部门路径',
   `is_main` tinyint(4) not null default '0' comment '是否主部门',
   `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `status` tinyint(4) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `idx_userid_corytype` (`user_id`,`corp_type`),
   KEY `idx_deptid` (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 comment '钉钉花名册-离职-离职部门';