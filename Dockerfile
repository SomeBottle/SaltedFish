# 构建服务端 non-root 镜像
# STAGE 1: 拉取 Workerman
FROM composer AS builder

WORKDIR /app

RUN composer require workerman/workerman:5.1.3

# STAGE 2: 构建非 root 镜像
FROM php:8.1.33-fpm-alpine

WORKDIR /app

COPY backend/ /app/server/
COPY --from=builder /app/vendor /app/server/vendor
COPY dockerfiles/* /app/

# 配置 http 服务日志目录
RUN mkdir -p /app/lighttpd && \
    # 使用 PHP 的生产环境配置
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    # 配置用户权限，www-data 是镜像中自带的用户
    chown -R www-data:www-data /app && \
    # 安装必要的 pcntl 扩展
    docker-php-ext-install pcntl && \
    # 安装 lighttpd
    apk add --no-cache lighttpd && \
    # 配置 php-fpm 的 unix sock
    mkdir -p /run/php && \
    chown www-data:www-data /run/php && \
    sed -i 's#listen = 9000#listen = /run/php/php-fpm.sock#' /usr/local/etc/php-fpm.d/zz-docker.conf && \
    chmod +x entrypoint.sh

# 非 root 用户运行
USER www-data

ENTRYPOINT [ "./entrypoint.sh" ]