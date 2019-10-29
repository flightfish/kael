
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


