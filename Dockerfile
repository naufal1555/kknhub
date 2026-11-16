# Multi-stage Dockerfile untuk KKNHub Laravel 11 Production di AWS EC2
FROM php:8.3-fpm-alpine AS base

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin source code
COPY . /var/www/html

# Set permission storage & bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Konfigurasi Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Konfigurasi Supervisor (menjalankan Nginx, PHP-FPM, dan Laravel Queue/Schedule Worker)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
