insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,6000
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=6000
where a.`status` = 0 and a.user_type=0 and b.relate_id is null