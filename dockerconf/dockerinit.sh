docker pull docker.inner.knowbox.cn:5000/knowbox-platform/php70:1.0.0-kael
docker container stop kael
docker container rm kael
docker run -d --network knowboxplatform --name kael \
-v /data/wwwroot/kael/dockerconf/ngconf/ngvhost:/usr/local/nginx/conf/vhost \
-v /data/wwwroot:/data/wwwroot \
--restart=always \
docker.inner.knowbox.cn:5000/knowbox-platform/php70:1.0.0-kael