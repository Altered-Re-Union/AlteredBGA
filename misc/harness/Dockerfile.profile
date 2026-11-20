# Harness image with Xdebug's profiler: docker compose -f docker-compose.yml -f docker-compose.profile.yml run --rm php ...
# Writes cachegrind files to results/profile/, summarised by tools/profile_summary.py
FROM php:8.2-cli
RUN docker-php-ext-install mysqli >/dev/null \
 && pecl install xdebug >/dev/null \
 && docker-php-ext-enable xdebug \
 && curl -sSL https://phar.phpunit.de/phpunit-11.phar -o /usr/local/bin/phpunit \
 && chmod +x /usr/local/bin/phpunit \
 && printf "memory_limit=2G\nzend.exception_ignore_args=On\nerror_reporting=E_ALL & ~E_DEPRECATED\nxdebug.mode=profile\nxdebug.start_with_request=trigger\nxdebug.output_dir=/harness/results/profile\nxdebug.profiler_output_name=cachegrind.out.%%R.%%u\n" > /usr/local/etc/php/conf.d/harness.ini
WORKDIR /harness
