# 基於官方 PHP 8.0 鏡像
FROM php:8.0

# 安裝常用 PHP 擴展
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    libxml2-dev \
    zlib1g-dev \
    libpq-dev \
    libmcrypt-dev \
    libmagickwand-dev \
    && docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    opcache \
    pdo_mysql \
    mysqli \
    pdo_pgsql \
    pcntl \
    && pecl install redis \
    && pecl install swoole-4.8.6 \
    && docker-php-ext-enable redis swoole pcntl  # 移除 imagick 和 mongodb

# 修改 swoole.use_shortname
RUN echo "swoole.use_shortname = 'Off'" >> /usr/local/etc/php/php.ini

# 安裝 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 安裝時區設置
RUN ln -snf /usr/share/zoneinfo/Asia/Taipei /etc/localtime && echo Asia/Taipei > /etc/timezone

WORKDIR /opt/www

# 複製應用程序代碼並安裝依賴項
COPY . /opt/www
RUN composer install --no-dev -o && php bin/hyperf.php

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
