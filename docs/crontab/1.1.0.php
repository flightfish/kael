<?php

/**
 * ************************* init once all
 * cd /data/wwwroot/usercenter_knowbox && nohup php yii elastic/init-question >> /data/crontablog/knowbox/init_elastic.log 2>&1 &
 * cd /data/wwwroot/usercenter_knowbox && nohup php yii question-resource/init-image >> /data/crontablog/knowbox/init_resource.log 2>&1 &
 *
 * cd /data/wwwroot/usercenter_susuan && nohup php yii elastic/init-question >> /data/crontablog/susuan/init_elastic.log 2>&1 &
 * cd /data/wwwroot/usercenter_susuan && nohup php yii question-resource/init-image >> /data/crontablog/susuan/init_resource.log 2>&1 &
 *
 *
 * *************************only knowbox
 * cd /data/wwwroot/usercenter_knowbox && nohup php yii qselect-init/init >> /data/crontablog/knowbox/init_qselect.log 2>&1 &
 */


/**
 * **********************crontab
 * 10 1 * * * cd /data/wwwroot/usercenter_knowbox && php yii elastic/update-question >> /data/crontablog/knowbox/crontab_elastic_update-question.log 2>&1
 * /10 * * * * cd /data/wwwroot/usercenter_knowbox && php yii question-resource/update >> /data/crontablog/knowbox/crontab_question-resource_update.log 2>&1
 * 10 0 * * * cd /data/wwwroot/usercenter_knowbox && php yii question-resource/new >> /data/crontablog/knowbox/crontab_question-resource_new.log 2>&1
 * /5 * * * * cd /data/wwwroot/usercenter_knowbox && php yii qselect-syn/syn-tag >> /data/crontablog/knowbox/crontab_qselect-syn_syn-tag.log 2>&1
 *
 * 10 1 * * * cd /data/wwwroot/usercenter_susuan && php yii elastic/update-question >> /data/crontablog/susuan/crontab_elastic_update-question.log 2>&1
 * /10 * * * * cd /data/wwwroot/usercenter_susuan && php yii question-resource/update >> /data/crontablog/susuan/crontab_question-resource_update.log 2>&1
 * 10 0 * * * cd /data/wwwroot/usercenter_susuan && php yii question-resource/new >> /data/crontablog/susuan/crontab_question-resource_new.log 2>&1
 * /5 * * * * cd /data/wwwroot/usercenter_susuan && php yii qselect-syn/syn-tag >> /data/crontablog/susuan/crontab_qselect-syn_syn-tag.log 2>&1
 */
