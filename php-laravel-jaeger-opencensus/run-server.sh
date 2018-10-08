#! /bin/bash

# [ -d jaeger-symfony ] || composer create-project symfony/website-skeleton jaeger-symfony

composer install

[ -f .env ] || cp .env.example .env
grep "APP_KEY=base64" .env > /dev/null || php artisan key:generate

php artisan serve --host 0.0.0.0 --port 8000

