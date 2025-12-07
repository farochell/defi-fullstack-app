FROM dunglas/frankenphp:1-php8.4-bookworm

# Installer les extensions PHP nécessaires
RUN install-php-extensions pdo_mysql intl zip opcache xdebug

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app
COPY ./ /app

# Installer les dépendances Composer
RUN composer install --no-interaction --optimize-autoloader



