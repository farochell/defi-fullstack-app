FROM dunglas/frankenphp:latest

RUN install-php-extensions pdo_mysql intl zip opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY ./ /app

# Installer les dépendances Composer depuis le bon répertoire
RUN composer install --no-interaction --optimize-autoloader
