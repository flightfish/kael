alter table base_course_section
add index idx_summary(summary(10));

alter table base_knowledge_node
add know_type	int not null default 0 COMMENT '知识点0 考点1',
add index idx_subject_gradepart(subject,grade_part),
add index idx_source (source),
add index idx_summary_source(summary(10),source);

alter table base_question
add `render` text,
add index idx_showtype (show_type),
add index idx_questionid_showtype (question_id, show_type),
add index idx_questionid_systemtype(question_id, system_type);


alter table relate_knowledge_question
add main_type int  NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 主';

CREATE TABLE `qa_base_ability_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `grade_part` int(6) NOT NULL DEFAULT '20',
  `subject` int(6) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `qa_base_method_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `subject` int(11) NOT NULL DEFAULT '0',
  `grade_part` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qa_base_showtype_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `grade_part` int(6) NOT NULL DEFAULT '20',
  `subject` int(6) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `qa_base_thought_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `grade_part` int(6) NOT NULL DEFAULT '20',
  `subject` int(6) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `qa_relate_ability_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `well_chosen` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 精',
  `score` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `main_type` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 主',
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `question_id` (`question_id`,`node_id`) USING BTREE,
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `qa_relate_method_knowledge` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `method_id` int(11) NOT NULL,
  `know_id` int(11) NOT NULL,
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `know_id` (`know_id`,`method_id`),
  KEY `method_id` (`method_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `qa_relate_method_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `well_chosen` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 精',
  `score` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `main_type` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 主',
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `question_id` (`question_id`,`node_id`) USING BTREE,
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `qa_relate_showtype_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `well_chosen` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 精',
  `score` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `main_type` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 主',
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `question_id` (`question_id`,`node_id`) USING BTREE,
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qa_relate_thought_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `well_chosen` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 精',
  `score` int(11) NOT NULL DEFAULT '0',
  `status` int(6) NOT NULL DEFAULT '0',
  `online_status` tinyint(6) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `main_type` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 未知  0 非 1 主',
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `question_id` (`question_id`,`node_id`) USING BTREE,
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qiniu_resource` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) NOT NULL DEFAULT '',
  `resource_type` int(11) NOT NULL DEFAULT '0' COMMENT '1 插画',
  `qiniu_etag` varchar(255) NOT NULL DEFAULT '0',
  `resource_url` varchar(255) NOT NULL DEFAULT '',
  `status` int(6) NOT NULL DEFAULT '0',
  `create_user` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`resource_id`),
  UNIQUE KEY `qiniu_etag` (`qiniu_etag`) USING BTREE,
  KEY `resource_name` (`resource_name`),
  KEY `create_user` (`create_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qiniu_resource_question` (
  `relate_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `qiniu_etag` varchar(255) NOT NULL DEFAULT '',
  `qiniu_url` varchar(255) NOT NULL DEFAULT '',
  `qiniu_size` int(11) NOT NULL DEFAULT '0' COMMENT '字节数',
  `bucket` varchar(255) NOT NULL DEFAULT '',
  `status` int(6) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `question_id` (`question_id`,`qiniu_url`) USING BTREE,
  KEY `image_hash_qiniu` (`qiniu_etag`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `relate_knowledge_question` add index idx_knowid(know_id);


