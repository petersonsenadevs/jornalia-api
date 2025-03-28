# Etapa base
FROM php:8.2-fpm AS base

# Configuración de entorno
ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    PHP_DATE_TIMEZONE=Europe/Madrid \
    PHP_MEMORY_LIMIT=256M \
    PHP_MAX_EXECUTION_TIME=90 \
    PHP_POST_MAX_SIZE=100M \
    PHP_UPLOAD_MAX_FILE_SIZE=100M

# Instalar dependencias del sistema, PHP y Composer
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl zip unzip git nano vim rsync sqlite3 supervisor nginx cron \
    && docker-php-ext-install pdo pdo_mysql bcmath opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuración inicial
WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --optimize-autoloader --no-dev \
    && mkdir -p storage/logs \
    && chown -R www-data:www-data /var/www/html



# Copiar configuraciones personalizadas
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/conf.d/* /etc/supervisor/conf.d/
COPY entrypoint.sh /entrypoint.sh
COPY docker/scripts/ /scripts/



# Copiar script de entrada

# Permisos para los scripts
RUN chmod +x /entrypoint.sh /scripts/*.sh

# Exponer puertos
EXPOSE 9000

# Definir entrypoint
ENTRYPOINT ["/entrypoint.sh"]
