alter table user add meican_update_time timestamp not null default '0000-00-00 00:00:00';
alter table user add email_created tinyint not null default '0';