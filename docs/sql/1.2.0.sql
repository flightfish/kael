alter table `user`
add work_type int not null default 0 comment '工种' AFTER department_id,
add work_level int not null default 0 comment '级别' AFTER work_type;

create table work_type (
	id int not null auto_increment,
	name varchar(50) not null default '',
	status int not null default 0,
	create_time timestamp not null default CURRENT_TIMESTAMP,
	update_time timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  primary key(id)
)ENGINE=innodb default charset=utf8;

create table work_level (
	id int not null auto_increment,
	name varchar(50) not null default '',
	status int not null default 0,
	create_time timestamp not null default CURRENT_TIMESTAMP,
	update_time timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  primary key(id)
)ENGINE=innodb default charset=utf8;

INSERT INTO work_level (id,name) VALUES (1,'初级'),(2,'中级'),(3,'高级');

INSERT INTO work_type (id,name) VALUES (1,'BD'),(2,'运营'),(3,'市场'),(4,'研发'),(5,'产品'),(6,'设计'),(7,'职能'),(8,'教学'),(9,'教研');