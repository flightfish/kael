alter table platform
    add column env_type int(11) not null default 0 comment '应用类型 1线上 2预览' after `is_show`,
    add column admin_user bigint(20) not null default 0 comment '管理员' after env_type;


create table log_platform (
    log_id bigint(20) not null auto_increment,
    log_op varchar(20) not null default '',
    platform_id bigint(20) not null default 0,
    user_id bigint(20) not null default 0,
    log_content varchar(5000) not null default '',
    status int(11) not null default 0,
    create_time timestamp not null default CURRENT_TIMESTAMP,
    update_time timestamp not null default CURRENT_TIMESTAMP on update current_timestamp,
    primary key (log_id),
    index idx_platformid(platform_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


