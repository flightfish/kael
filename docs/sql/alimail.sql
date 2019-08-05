alter table `dingtalk_user`
  add `email_created_ali` tinyint(4) NOT NULL DEFAULT '0' COMMENT '邮箱创建状态 0创建中 1已创建 2创建异常 3注销中 4已注销' AFTER `email_created`;


create table alimail_status (
	id int not null auto_increment,
	email varchar(100) not null default '',
	create_time timestamp not null default CURRENT_TIMESTAMP,
	update_time timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 	status int not null default 0,
 primary key(id),
 UNIQUE KEY (email)
)ENGINE=innodb default charset=utf8;