FROM php:8.2-cli
RUN docker-php-ext-install mysqli >/dev/null \
 && curl -sSL https://phar.phpunit.de/phpunit-11.phar -o /usr/local/bin/phpunit \
 && chmod +x /usr/local/bin/phpunit \
 && printf "memory_limit=1G\nzend.exception_ignore_args=On\nerror_reporting=E_ALL & ~E_DEPRECATED\n" > /usr/local/etc/php/conf.d/harness.ini
WORKDIR /harness
