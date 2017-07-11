ALTER TABLE qselect_select_entity add choice int not null default 0 comment '1课后 2扩展 99坏题';

alter table qselect_task_entity
add question_count_select int not null default 0,
add question_count_checkpass int not null default 0,
add question_count_checkrework int not null default 0,
add question_count_checkmodify int not null default 0,
add question_count_bad int not null default 0
add show_recommend int not null default 1;

alter table global_setting modify `value` LONGTEXT;


CREATE TABLE `qselect_task_tree` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT '0',
  `tree_id` int(11) NOT NULL DEFAULT '0',
  `tree_name` varchar(11) NOT NULL DEFAULT '0' COMMENT '树节点名称',
  `tree_order` bigint(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `question_count` int(11) NOT NULL DEFAULT '0' COMMENT '总题目数量',
  `question_count_select` int(11) NOT NULL DEFAULT '0' COMMENT '已选',
  `question_count_checkpass` int(11) NOT NULL DEFAULT '0' COMMENT '通过',
  `question_count_checkrework` int(11) NOT NULL DEFAULT '0' COMMENT '返工',
  `question_count_checkmodify` int(11) NOT NULL DEFAULT '0' COMMENT '修改后通过',
  `question_count_bad` int(11) NOT NULL DEFAULT '0' COMMENT '坏题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `idx_taskid_treeid` (`task_id`,`tree_id`) USING BTREE,
  KEY `idx_treeid` (`tree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qselect_task_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT '0',
  `tree_id` int(11) NOT NULL DEFAULT '0',
  `question_id` int(11) NOT NULL DEFAULT '0' COMMENT '任务名',
  `select_id` int(11) NOT NULL DEFAULT '0',
  `select_stage` int(11) NOT NULL DEFAULT '-1' COMMENT '-1未选精 0已选精 1通过 2打回 3修改 4修改后结果',
  `select_choice` int(11) NOT NULL DEFAULT '0' COMMENT '精题类型 1课后 2扩展题 99坏题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `idx_taskid_treeid_questionid` (`task_id`,`tree_id`,`question_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;