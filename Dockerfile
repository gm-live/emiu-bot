# 基於 PHP 8.0 + swoole4.8.6
FROM wright1992/swoole_base:latest

# 設置台北時區
RUN echo "date.timezone = Asia/Taipei" >> /usr/local/etc/php/php.ini

# 設置工作目錄並複製應用程序代碼
WORKDIR /opt/www
COPY . /opt/www

# 安裝應用程序依賴
RUN rm -rf vendor
RUN composer install --no-dev -o && php bin/hyperf.php

# 設置容器啟動命令
CMD ["php", "/opt/www/bin/hyperf.php", "start"]
