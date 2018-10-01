#! /bin/bash

# export TEST_CASE_FOLDER_PHP=${1:-}
# export TEST_CASE_FOLDER_PHP=${1:-php-laravel-jaeger-opencensus}
# export TEST_CASE_FOLDER_PHP=${1:-php-no-framework-jaeger-opencensus}
export TEST_CASE_FOLDER_PHP=${1:-php-symfony-jaeger-opencensus}
# export TEST_CASE_FOLDER_PHP=${1:-php-symfony-jaeger-opentracing}

# export TEST_CASE_FOLDER_PY=${2:-}
export TEST_CASE_FOLDER_PY=${1:-python-cherrypy-opencensus}

pip install -r requirements.txt
python build.py -php "$TEST_CASE_FOLDER_PHP" -py "$TEST_CASE_FOLDER_PY"

docker ps -a | grep distributed-tracing-test- | cut -f1 -d' ' | xargs docker rm -f

MD5=md5
which md5sum > /dev/null && MD5=md5sum

if [ "$TEST_CASE_FOLDER_PHP" != '' ]; then
    [ "$(cat $TEST_CASE_FOLDER_PHP/Dockerfile | $MD5)" != "$(cat .md5.php)" ] \
        && docker-compose -f docker-compose.yml build
    cat $TEST_CASE_FOLDER_PHP/Dockerfile | $MD5 > .md5.php
fi

if [ "$TEST_CASE_FOLDER_PY" != '' ]; then
    [ "$(cat $TEST_CASE_FOLDER_PY/Dockerfile | $MD5)" != "$(cat .md5.py)" ] \
        && docker-compose -f docker-compose.yml build
    cat $TEST_CASE_FOLDER_PY/Dockerfile | $MD5 > .md5.py
fi

docker-compose -f docker-compose.yml up