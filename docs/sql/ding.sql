ALTER TABLE `dingtalk_department`
ADD COLUMN `main_leader_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门领导人编号(kael)' after `subroot_id`,
ADD COLUMN `main_leader_name` varchar(255) NOT NULL DEFAULT '' COMMENT '部门领导人名称' after `main_leader_id`;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(104,'BD-张文翼',55052601,'BD1组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(104,'BD-张文翼',104422706,'GR部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(105,'BD-唐君',55128736,'BD2组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(115,'BD销售运营组',55088717,'销售运营组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(118,'运营-周翼组',83369200,'用户运营组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(118,'运营-周翼组',99238150,'家校盒子运营组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(118,'运营-周翼组',55073700,'运营2组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(107,'布克学堂',109524743,'销售部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(108,'财务',63848304,'财务部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(114,'采购部',65452086,'采购部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(120,'产品-周翼组',55249465,'产品1组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(120,'产品-周翼组',90776812,'产品3组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(120,'产品-周翼组',92081851,'产品4组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(126,'用户研究',55318452,'产品2组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(126,'用户研究',68889183,'创意部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(109,'行政',63869313,'行政部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(102,'小学教研',63858222,'教研部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(149,'内审部',92556592,'内审部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(106,'人力资源部',55092607,'人事部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(122,'插画动画组',55231401,'设计部1组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(141,'品牌视觉组',55250488,'设计部3组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(123,'UI组',55326428,'设计部4组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(111,'市场',63739873,'市场部');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(142,'小盒课堂',63956959,'小盒课堂');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(137,'衍生品',109530688,'小盒课堂1.1.3组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(124,'研发3组',55231413,'研发部1组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(124,'研发3组',99830174,'小象编程');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(125,'运维',55285316,'研发部2组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(101,'研发-张昊波',55311358,'研发部3组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(113,'研发-周恒磊',55312365,'研发部4组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(113,'研发-周恒磊',104691689,'研发部5组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(128,'测试',69171032,'研发部6组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(146,'研发-李达',72197405,'研发部7组');
INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(119,'运营-汤立',55039598,'运营1组');

INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(145,'战略综合部',66503511,'战略综合部');
-- INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(145,'战略综合部',69852311,'战略综合部1组');
-- INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(145,'战略综合部',110054183,'战略综合部2组');
-- INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(145,'战略综合部',78587108,'战略综合部3组');

INSERT INTO `department_relate_to_kael`(`kael_department_id`,`kael_department_name`,`department_id`,`department_name`) VALUES(134,'总裁办',55440532,'战略综合部4组');

ALTER  TABLE `dingtalk_user`
ADD COLUMN `hired_date` varchar(60) NOT NULL DEFAULT '' COMMENT '入职日期' after `department_subroot`,
ADD COLUMN `birthday` varchar(60) NOT NULL DEFAULT '' COMMENT '出生日期' after `hired_date`;