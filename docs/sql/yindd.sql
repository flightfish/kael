alter table `dingtalk_user`
add ydd_account bigint(20) not null default 0 comment '印点点account' AFTER birthday;
