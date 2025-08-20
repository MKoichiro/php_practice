FROM php:8.3-apache

# 必要拡張とmod_rewrite
RUN apt-get update && apt-get install -y --no-install-recommends \
      libicu-dev libpng-dev libjpeg-dev libzip-dev unzip git libonig-dev \
  && docker-php-ext-configure gd --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo_mysql intl mbstring zip gd opcache \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# タイムゾーン
RUN ln -snf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && echo Asia/Tokyo > /etc/timezone

# 開発用 php.ini を読み込む
COPY php/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini

# vhost: DocumentRoot を public/ に、.htaccess を有効化
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Composer（任意／必要ならどうぞ）
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
