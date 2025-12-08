FROM dunglas/frankenphp:1-php8.4-bookworm

# Installer les outils système nécessaires pour le script d'entrypoint
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires
RUN install-php-extensions pdo_mysql intl zip opcache xdebug

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier uniquement les fichiers composer d'abord (pour le cache Docker)
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances Composer (sera refait dans l'entrypoint si nécessaire)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Copier le reste de l'application
COPY ./ /app

# Créer les répertoires nécessaires
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var/ && \
    chmod -R 775 var/

# Copier et configurer l'entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Healthcheck adapté (vérifie juste que le serveur répond)
HEALTHCHECK --interval=30s --timeout=5s --start-period=90s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
