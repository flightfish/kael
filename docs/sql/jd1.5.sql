alter table `dingtalk_department`
    add base_name varchar(50) not null default '' comment '基地名称' AFTER `main_leader_name`;

alter table `dingtalk_department_user`
    add base_name varchar(50) not null default '' comment '基地名称' AFTER `ext_attr`;

alter table `dingtalk_user`
    add base_name varchar(50) not null default '' comment '基地名称' AFTER `ydd_account`;
