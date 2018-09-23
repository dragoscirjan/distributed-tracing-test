#! /bin/bash

# [ -d jaeger-symfony ] || composer create-project symfony/website-skeleton jaeger-symfony

composer install
[ jaeger-symfony/vendor/symfony/web-server-bundle ] || composer require symfony/web-server-bundle --dev

[ -f .env ] || cp .env.dist .env

php bin/console make:migration || exit 1
php bin/console doctrine:migrations:migrate -n || exit 1

# php bin/console server:start 0.0.0.0:8000 -n -vvv
php bin/console server:run 0.0.0.0:8000 -n -vvv