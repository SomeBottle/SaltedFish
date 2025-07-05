#!/bin/sh

# 启动服务

# HTTP 服务后台运行，并重定向日志
php-fpm -D >/app/php-fpm.log 2>&1
lighttpd -f /app/lighttpd.conf

# Workerman 服务前台运行
php /app/server/fish.php start
