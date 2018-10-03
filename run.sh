#! /bin/bash

# export TEST_CASE_FOLDER=${1:-}
export TEST_CASE_FOLDER=${1:-php-sym_jaeger-census}
# export TEST_CASE_FOLDER=${1:-py-cherry_jinja2-census}
# export TEST_CASE_FOLDER=${1:-php-sym_py-cherry_jaeger-census}

pip install -r requirements.txt
python build.py -p "$TEST_CASE_FOLDER"

docker ps -a | grep distributed-tracing-test- | cut -f1 -d' ' | xargs docker rm -f

MD5=md5
which md5sum > /dev/null && MD5=md5sum

if [ -f $TEST_CASE_FOLDER/docker/php/Dockerfile ]; then
    [ "$(cat $TEST_CASE_FOLDER/docker/php/Dockerfile | $MD5)" != "$(cat .md5.php)" ] \
        && docker-compose -f docker-compose.yml build
    cat $TEST_CASE_FOLDE/docker/php/Dockerfile | $MD5 > .md5.php
fi

if [ -f $TEST_CASE_FOLDER/docker/py/Dockerfile ]; then
    [ "$(cat $TEST_CASE_FOLDER/docker/py/Dockerfile | $MD5)" != "$(cat .md5.py)" ] \
        && docker-compose -f docker-compose.yml build;
    cat $TEST_CASE_FOLDER/docker/py/Dockerfile | $MD5 > .md5.py
fi

docker-compose -f docker-compose.yml up