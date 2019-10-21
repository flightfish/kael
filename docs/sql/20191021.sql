/**
update dingtalk_department set path_name=alias_name where level =1 and status=0;
update  dingtalk_department s
left join dingtalk_department p on s.parentid = p.id
set s.path_name = concat(p.path_name,'/',s.alias_name)
where s.status = 0 and p.status=0 and s.level=2;
**/
alter table dingtalk_department add path_name varchar(1000)  not null default '' after alias_name;