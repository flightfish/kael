alter table `dingtalk_user`
  add `email_pinyin` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱拼音' AFTER `email`,
  add `email_suffix` varchar(255) NOT NULL DEFAULT '' COMMENT '后缀，用于邮箱创建' after `email_pinyin`,
  add `email_number` int(11) NOT NULL DEFAULT '0' COMMENT '序号，用于邮箱创建' AFTER `email_suffix`,
  add `email_errno` int(11) NOT NULL DEFAULT '0' COMMENT '错误类型 0正常 1多音字 2姓名中包含非汉字字符 3名字长度过长(大于10位) 4名字长度过短(小于2位) 5其他'  AFTER `email_number`,
  add `email_errmsg` varchar(255) NOT NULL DEFAULT '' COMMENT '错误详情' AFTER `email_errno`,
  add `email_created` tinyint(4) NOT NULL DEFAULT '0' COMMENT '邮箱创建状态 0创建中 1已创建 2创建异常 3注销中 4已注销' AFTER `email_errmsg`,
  add INDEX idx_pinyin_emailsuffix_emailnumber(`email_pinyin`,`email_suffix`,`email_number`);

