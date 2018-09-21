FROM php:7.2-cli-alpine

RUN mkdir /home/code \
    && addgroup code \
    && adduser -D -h /home/code -G code code \
    && chown code:code /home/code

RUN apk add --no-cache --virtual zlib-dev \
    && docker-php-ext-install -j$(nproc) mysqli pcntl pdo_mysql zip

RUN cd /usr/local/php \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && ln -s /usr/local/php/composer.phar /usr/local/bin/composer \
    && chmod 555 /usr/local/php/composer.phar

USER code

WORKDIR /home/code

EXPOSE 8000

CMD [ "sh", "/home/code/run-php.sh" ]