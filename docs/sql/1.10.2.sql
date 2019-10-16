alter table platform add column env_type int(11) not null default 0 comment '应用类型 1线上 2预览' after `is_show`;
