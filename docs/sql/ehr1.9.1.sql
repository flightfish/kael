
CREATE TABLE `dingcan_order_exception` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier` int(11)  NOT NULL DEFAULT 0 COMMENT '1美餐 2竹蒸笼',
  `meal_date` DATE NOT NULL DEFAULT '0000-00-00' COMMENT '日期',
  `meal_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '饭点',
  `order_id` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '订餐ID',
  `kael_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'kael用户ID',
  `dingtalk_department_id` bigint(20)  NOT NULL DEFAULT 0 COMMENT '钉钉部门ID',
  `dingtalk_department_name` VARCHAR(100)  NOT NULL DEFAULT 0 COMMENT '钉钉部门名称',
  `dingtalk_subroot_id` bigint(20)  NOT NULL DEFAULT 0 COMMENT '钉钉一级部门ID',
  `dingtalk_subroot_name` VARCHAR(100)  NOT NULL DEFAULT 0 COMMENT '钉钉一级部门名称',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT '订餐金额',
  `order_ext` TEXT,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX idx_supplier_orderid(`supplier`,`order_id`),
  INDEX idx_kaelid_mealdate(`kael_id`,`meal_date`),
  INDEX idx_mealtime(`meal_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dingtalk_attendance_process_instance` (
   `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
   `proc_inst_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '审批实例id',
   `title` varchar(255)  NOT NULL DEFAULT '' COMMENT '审批实例标题',
   `start_date` DATE NOT NULL DEFAULT '0000-00-00' COMMENT '发起日期',
   `start_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
   `finish_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
   `user_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '发起人员ID',
   `dingtalk_department_id` bigint(20)  NOT NULL DEFAULT 0 COMMENT '钉钉部门ID',
   `dingtalk_department_name` VARCHAR(100)  NOT NULL DEFAULT 0 COMMENT '钉钉部门名称',
   `process_status` varchar(100) NOT NULL DEFAULT '' COMMENT '审批状态，分为NEW（新创建）RUNNING（运行中）TERMINATED（被终止）COMPLETED（完成)',
   `cc_user_id` varchar(255) NOT NULL DEFAULT '' COMMENT '抄送人。审批附带抄送人时才返回该字段。',
   `result` varchar(100) NOT NULL DEFAULT '' COMMENT '审批结果，分为 agree 和 refuse',
   `business_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '审批实例业务编号',
   `form_component_values` varchar(5000) NOT NULL DEFAULT '' COMMENT '表单详情列表',
   `operation_records` varchar(5000) NOT NULL DEFAULT '' COMMENT '操作记录列表',
   `tasks` varchar(5000) NOT NULL DEFAULT '' COMMENT '已审批任务列表，可以通过此列表获取已审批人',
   `attached_process_instance_ids` varchar(5000) NOT NULL DEFAULT '' COMMENT '审批附属实例列表，当已经通过的审批实例被修改或撤销，会生成一个新的实例，作为原有审批实例的附属。如果想知道当前已经通过的审批实例的状态，可以依次遍历它的附属列表，查询里面每个实例的biz_action',
   `biz_action` varchar(100) NOT NULL DEFAULT '' COMMENT '审批实例业务动作，MODIFY表示该审批实例是基于原来的实例修改而来，REVOKE表示该审批实例对原来的实例进行撤销，NONE表示正常发起',
   `status` int(11) NOT NULL DEFAULT '0',
   `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE INDEX idx_proc_inst_id(`proc_inst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


