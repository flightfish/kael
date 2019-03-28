ALTER TABLE `dingtalk_user`
ADD COLUMN `kael_id` int(11) NOT NULL DEFAULT '0' COMMENT 'kael账号' after `user_id`;