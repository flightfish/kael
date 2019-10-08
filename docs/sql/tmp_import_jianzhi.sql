
CREATE TABLE `tmp_import_jianzhi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '电话号码',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `department_name` varchar(255) NOT NULL DEFAULT '' COMMENT '部门名称',
  `department_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '部门id',
  `ding_userid` varchar(100) NOT NULL DEFAULT '',
  `ding_error` varchar(1000) NOT NULL DEFAULT '',
  `work_number` varchar(100) NOT NULL DEFAULT '',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE dingtalk_user add columns corp_type int(11) not NULL DEFAULT 0 comment '1主企业 2兼职企业' after auto_id;
ALTER TABLE dingtalk_department add columns corp_type int(11) not NULL DEFAULT 0 comment '1主企业 2兼职企业' after id;