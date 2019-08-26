alter table `dingtalk_department`
add ehr_name varchar(255) not null default '' comment 'ehr名称' AFTER `name`;