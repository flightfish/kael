INSERT INTO common_modules (name,module,role,url) VALUES
  ('人员管理',	'qselect',	1,	'/admin/index/user'),
  ('查看题目',	'bookstore',	2,	'/look_over_question/lookover.html'),
  ('选精',	'qselect',	2,	'/look_over_question/taskmanage.html?type=select'),
  ('审查',	'qselect',	'3',	'/look_over_question/taskmanage.html?type=check'),
  ('任务管理',	'qselect',	'1',	'/look_over_question/taskmanage.html?type=manage'),
  ('知识点管理',	'bookstore',	'4',	'/look_over_question/treemanage.html');

INSERT INTO common_modules_user (name,module,role,url) VALUES
  ('tkmis人员管理',	'qselect',	1,	'/admin_qselect/user'),
  ('题盒子管理新',	'entrystore',	4,	'/admin/user?type=entrystore'),
  ('教辅管理新',	'qselect',	1,	'/admin/user?type=bookstore'),
  ('检错管理新',	'qselect',	1,	'/admin/user?type=qualitysys'),
  ('选精管理新',	'qselect',	1,	'/admin/user?type=qselect');