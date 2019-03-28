ALTER TABLE `dingtalk_user`
ADD COLUMN `kael_id` int(11) NOT NULL DEFAULT '0' COMMENT 'kael账号' after `user_id`;

CREATE TABLE `department_relate_to_kael` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `kael_department_id` int(11) NOT NULL DEFAULT '0' COMMENT 'kael部门编号',
  `kael_department_name` varchar(255) NOT NULL COMMENT 'kael部门名称',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '实际部门编号',
  `department_name` varchar(255) NOT NULL COMMENT '实际部门名称',
  `depart_no` varchar(60) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX kael_id_index(`kael_department_id`),
  INDEX in_index(`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;