create table dingtalk_relate_department_user (
	relate_id bigint(20) not null auto_increment,
	department_id bigint(20) not null default '0',
	user_id varchar(100) not null default '',
	create_time timestamp not null default CURRENT_TIMESTAMP,
	update_time timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 	status int not null default 0,
 primary key(relate_id),
 INDEX idx_departmentid_userid (department_id,user_id),
 INDEX idx_userid (user_id)
)ENGINE=innodb default charset=utf8mb4;


alter table dingtalk_relate_department_user add column corp_tpye int(11) not null default 0 comment '企业信息' after relate_id;


create table dingtalk_department_user (
 `relate_id` bigint(20) not null auto_increment,
 `corp_type` int(11) not null default '0',
 `department_id` bigint(20) not null default '0',
 `user_id` varchar(100) not null default '',
 `union_id` varchar(100) not null default '',
 `open_id` varchar(100) not null default '',
 `mobile` varchar(20) not null default '',
 `tel` varchar(50) not null default '',
 `work_place` varchar(255) not null default '',
 `remark` varchar(255) not null default '',
 `order` bigint(20) not null default '0',
 `is_admin` tinyint(4) not null default '0',
 `is_boss` tinyint(4) not null default '0',
 `is_hide` tinyint(4) not null default '0',
 `is_leader` tinyint(4) not null default '0',
 `name` varchar(100) not null default '',
 `active` tinyint(4) not null default '0',
 `department` varchar(500) not null default '',
 `position` varchar(100) not null default '',
 `email` varchar(100) not null default '',
 `org_email` varchar(255) not null default '',
 `avatar` varchar(255) not null default '',
 `job_number` varchar(100) not null default '',
 `ext_attr` varchar(500) not null default '',
 `create_time` timestamp not null default CURRENT_TIMESTAMP,
 `update_time` timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `status` int not null default 0,
 primary key(relate_id),
 INDEX idx_departmentid_userid (department_id,user_id),
 INDEX idx_userid (user_id)
)ENGINE=innodb default charset=utf8mb4;
