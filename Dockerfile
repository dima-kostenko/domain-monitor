# ──────────────────────────────────────────────────────────────────────────────
# Stage 1 — Composer dependencies
# ──────────────────────────────────────────────────────────────────────────────
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Install production dependencies only (no dev — overridden in docker-compose for local)
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ──────────────────────────────────────────────────────────────────────────────
# Stage 2 — Runtime image
# ──────────────────────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS runtime

LABEL maintainer="Domain Monitor"

# ─── System dependencies ─────────────────────────────────────────────────────
RUN apk add --no-cache \
        bash \
        curl \
        git \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        supervisor \
        tzdata \
    && cp /usr/share/zoneinfo/UTC /etc/localtime \
    && echo "UTC" > /etc/timezone

# ─── PHP extensions ──────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip

# ─── PHP configuration ───────────────────────────────────────────────────────
COPY docker/php/php.ini    /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ─── Supervisor (php-fpm + scheduler) ────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisord.conf

# ─── Application ─────────────────────────────────────────────────────────────
WORKDIR /var/www/html

# Copy vendor from stage 1
COPY --from=vendor /app/vendor ./vendor

# Copy application source
COPY --chown=www-data:www-data . .

# ─── Entrypoint ──────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
