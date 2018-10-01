FROM php:7.2-cli-alpine


RUN mkdir /home/code \
    && addgroup code \
    && adduser -D -h /home/code -G code code \
    && chown code:code /home/code

RUN apk add --no-cache --virtual zlib-dev \
    && docker-php-ext-install -j$(nproc) mysqli pcntl pdo_mysql zip sockets

RUN cd /usr/local/php \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && ln -s /usr/local/php/composer.phar /usr/local/bin/composer \
    && chmod 555 /usr/local/php/composer.phar


RUN apk add autoconf alpine-sdk \
    && pecl install opencensus-0.2.2 \
    && echo "extension=opencensus.so" > /usr/local/etc/php/conf.d/docker-php-opencensus.ini \
    && apk del alpine-sdk


RUN apk add autoconf alpine-sdk \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && apk del alpine-sdk


USER code

WORKDIR /home/code

EXPOSE 8000

CMD [ "sh", "/home/code/run-server.sh" ]