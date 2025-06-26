FROM rpungello/laravel-franken:8.4

ARG VERSION=1.0.0
ENV APP_VERSION=${VERSION}
COPY . /app
RUN composer install && npm install \
 && chown -R www-data:www-data /app

HEALTHCHECK --interval=5s --timeout=3s --retries=3 CMD php artisan status
