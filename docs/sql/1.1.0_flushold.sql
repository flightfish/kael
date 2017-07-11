
UPDATE qualitysys_check_log a
left join qualitysys_check_log b  on a.question_id = b.question_id
set b.source_id = a.log_id
where a.check_id > 0 and a.`error_status` = 10 and b.error_status in (11,12,13,14,15);

UPDATE qualitysys_check_log a
left join qualitysys_check_log b  on a.question_id = b.question_id
set b.source_id = a.log_id
where a.check_id > 0 and a.`error_status` = 20 and b.error_status in (21,22,23,24,25);

UPDATE qualitysys_check_log a
left join qualitysys_check_log b  on a.question_id = b.question_id
set b.source_id = a.log_id
where a.check_id > 0 and a.`error_status` = 100 and b.error_status in (101,102,103,104,105);

# only速算
update relate_knowledge_question set main_type = 1;
update base_question set show_type = show_type + 10 where show_type >=10 and show_type < 20;

