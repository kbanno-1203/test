# 公式 PHP-FPM イメージをベースに使う
FROM php:8.2-fpm

# MySQL PDO 拡張をインストール
RUN docker-php-ext-install pdo pdo_mysql
