#! /bin/bash

docker ps -a | grep  jaeger-test_core- | cut -f1 -d' ' | xargs docker rm -f

[ "$(cat Dockerfile | md5)" != "$(cat .md5)" ] && docker-compose -f docker-compose.yml build
cat Dockerfile | md5 > .md5

docker-compose -f docker-compose.yml up