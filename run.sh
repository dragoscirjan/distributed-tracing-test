#! /bin/bash

docker ps -a | grep  jaeger-test_core- | cut -f1 -d' ' | xargs docker rm -f

MD5=md5
which md5sum > /dev/null && MD5=md5sum

[ "$(cat Dockerfile | $MD5)" != "$(cat .md5)" ] && docker-compose -f docker-compose.yml build
cat Dockerfile | $MD5 > .md5

docker-compose -f docker-compose.yml up