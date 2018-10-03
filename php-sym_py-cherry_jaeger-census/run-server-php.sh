#! /bin/bash

# [ -d jaeger-symfony ] || composer create-project symfony/website-skeleton jaeger-symfony

composer install
[ jaeger-symfony/vendor/symfony/web-server-bundle ] || composer require symfony/web-server-bundle --dev

[ -f .env ] || cp .env.dist .env

# sed -i "s/DATABASE_URL=.*/DATABASE_URL=mysql:\/\/test:weltest@distributed-tracing-test-mysql:3306\/test/g" .env
# php bin/console doctrine:schema:update --force || php bin/console doctrine:schema:create || exit 1

php bin/console server:run 0.0.0.0:8000 -n -vvv