#!/bin/bash
project="kale"
tag="kael"
env=${1}
if [ ${env} ]; then
    if ["${env}" = "dev"]; then
        phpenv="Development"
    elif ["${env}" = "beta"]; then
        phpenv="Beta"
    elif ["${env}" = "prod"]; then
        phpenv="Production"
    else
        echo "环境不存在"
        exit
    fi
    cp /data/wwwroot/${project}/dockerconf/ngconf/nginx.${env}.conf /data/vhost/${tag}.conf
    docker exec ${tag} php /data/wwwroot/${project}/init --overwrite=all --env=${phpenv}
    docker exec ${tag} crontab /data/wwwroot/${project}/dockerconf/crontab.cron
    docker exec ${tag} supervisorctl restart all
    docker exec tengine nginx -s reload
else
    echo "需要输入环境参数"
fi