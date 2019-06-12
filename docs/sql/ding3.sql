CREATE TABLE `user_info` (
  `user_id` int(11) NOT NULL COMMENT '员工序号',
  `company_name` varchar(255) NOT NULL COMMENT '公司名称',
  `business_layer` varchar(255) NOT NULL DEFAULT '' COMMENT '业务层级',
  `business_or_platform` varchar(255) NOT NULL DEFAULT '' COMMENT '业务or平台',
  `business_by_level_one_and_low` varchar(255) NOT NULL DEFAULT '' COMMENT '一级业务&子业务',
  `budget_big_class` varchar(255) NOT NULL DEFAULT '' COMMENT '预算大类',
  `budget_small_class` varchar(255) NOT NULL DEFAULT '' COMMENT '预算子类',
  `cost_type_for_finance` varchar(255) NOT NULL DEFAULT '' COMMENT '成本分类(财务)',
  `cost_light_or_weight` varchar(255) NOT NULL DEFAULT '' COMMENT '成本轻/重',
  `fee_type_for_finance` varchar(255) NOT NULL DEFAULT '' COMMENT '费用分类(财务)',
  `R&D_six_low_item` varchar(255) NOT NULL DEFAULT '' COMMENT '研发六子项',
  `market_two_low_item` varchar(255) NOT NULL DEFAULT '' COMMENT '财务费用(市场两子项)',
  `manage_two_low_item` varchar(255) NOT NULL DEFAULT '' COMMENT '财务费用(管理两子项)',
  `fix_float_label` varchar(255) NOT NULL DEFAULT '' COMMENT '固浮标签',
  `investment_server_label` varchar(255) NOT NULL DEFAULT '' COMMENT '招服标签',
  `leave_time` varchar(255) NOT NULL DEFAULT '' COMMENT '离职时间',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;