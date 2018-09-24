#! /bin/bash

# export TEST_CASE_FOLDER=${1:-laravel-jaeger-opencensus}
# export TEST_CASE_FOLDER=${1:-no-framework-jaeger-opencensus}
export TEST_CASE_FOLDER=${1:-symfony-jaeger-opencensus}
# export TEST_CASE_FOLDER=${1:-symfony-jaeger-opentracing}

pip install -r requirements.txt
python build.py -p $TEST_CASE_FOLDER

docker ps -a | grep distributed-tracing-test- | cut -f1 -d' ' | xargs docker rm -f

MD5=md5
which md5sum > /dev/null && MD5=md5sum

[ "$(cat $TEST_CASE_FOLDER/Dockerfile | $MD5)" != "$(cat .md5)" ] && docker-compose -f docker-compose.yml build
cat $TEST_CASE_FOLDER/Dockerfile | $MD5 > .md5

docker-compose -f docker-compose.yml up