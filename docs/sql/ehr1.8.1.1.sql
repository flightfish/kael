alter table `dingtalk_department`
add alias_name varchar(255) not null default '' comment 'ehr名称' AFTER `name`;