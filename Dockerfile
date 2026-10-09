# syntax=docker/dockerfile:1
FROM php:8.3-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev libcurl4-openssl-dev \
    && for extension in pdo_sqlite sqlite3 curl; do \
        if ! php -r 'exit(extension_loaded($argv[1]) ? 0 : 1);' "$extension"; then docker-php-ext-install "$extension"; fi; \
       done \
    && a2enmod rewrite headers setenvif \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

RUN <<'SHELL'
set -eu
cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
cat > /etc/apache2/sites-available/000-default.conf <<'APACHE'
<VirtualHost *:80>
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    SetEnvIf X-Forwarded-Proto "^https$" HTTPS=on
    ErrorLog /proc/self/fd/2
    CustomLog /proc/self/fd/1 combined
</VirtualHost>
APACHE
printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf
a2enconf servername
cat > /usr/local/etc/php/conf.d/bryxa.ini <<'PHPINI'
upload_max_filesize=10M
post_max_size=12M
expose_php=Off
display_errors=Off
log_errors=On
session.use_strict_mode=1
date.timezone=America/Sao_Paulo
PHPINI
cat > /var/www/html/config.local.php <<'PHP'
<?php
return [
    'demo_mode' => false,
    'setup_key' => getenv('BRYXA_SETUP_KEY') ?: '',
    'storage_path' => __DIR__ . '/storage',
];
PHP
cat > /usr/local/bin/bryxa-start <<'START'
#!/bin/sh
set -eu
mkdir -p /var/www/html/storage /var/www/html/public/uploads
chown www-data:www-data /var/www/html/storage /var/www/html/public/uploads
chmod 750 /var/www/html/storage /var/www/html/public/uploads
exec apache2-foreground
START
chmod 755 /usr/local/bin/bryxa-start
find /var/www/html -name '*.php' -print0 | xargs -0 -n1 php -l
php -r 'foreach (["pdo_sqlite","sqlite3","curl","fileinfo","openssl"] as $extension) { if (!extension_loaded($extension)) { fwrite(STDERR, "Missing extension: ".$extension.PHP_EOL); exit(1); } }'
php /var/www/html/tests/catalog-settings.php
apache2ctl configtest
SHELL

EXPOSE 80
CMD ["bryxa-start"]
