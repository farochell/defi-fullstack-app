#!/bin/bash
set -e

echo "🚀 ENTRYPOINT STARTED"

cd /app

# Composer
if [ -f "composer.json" ]; then
    echo "📦 Installing Composer dependencies..."
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --optimize-autoloader --no-scripts || true
    echo "🔧 Running Symfony auto-scripts..."
    COMPOSER_ALLOW_SUPERUSER=1 composer run-script --no-interaction auto-scripts || true
fi

# Créer répertoires var
mkdir -p var/cache var/log
chown -R www-data:www-data var/ 2>/dev/null || true
chmod -R 775 var/ 2>/dev/null || true

# -----------------------------
# 🔹 Database Setup FIRST
# -----------------------------
echo "🛠 Waiting for MySQL..."

# Parse DATABASE_URL with better error handling
if [ -z "$DATABASE_URL" ]; then
    echo "❌ DATABASE_URL is not set!"
    exit 1
fi

DB_HOST=$(php -r "\$url = parse_url(getenv('DATABASE_URL')); echo \$url['host'] ?? 'db';")
DB_PORT=$(php -r "\$url = parse_url(getenv('DATABASE_URL')); echo \$url['port'] ?? '3306';")
DB_NAME=$(php -r "\$url = parse_url(getenv('DATABASE_URL')); echo ltrim(\$url['path'] ?? '/symfony', '/');")
DB_USER=$(php -r "\$url = parse_url(getenv('DATABASE_URL')); echo \$url['user'] ?? 'symfony';")
DB_PASS=$(php -r "\$url = parse_url(getenv('DATABASE_URL')); echo \$url['pass'] ?? 'password';")

echo "📍 Connecting to: $DB_USER@$DB_HOST:$DB_PORT/$DB_NAME"

# First, wait for the port to be open
echo "🔌 Checking if MySQL port is reachable..."
MAX_RETRIES=30
RETRY_COUNT=0
while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "❌ MySQL port $DB_PORT is not reachable after $MAX_RETRIES attempts"
        exit 1
    fi
    echo "⏳ Waiting for MySQL port at $DB_HOST:$DB_PORT... (attempt $RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done
echo "✅ MySQL port is open!"

# Now wait for MySQL to accept connections
echo "🔐 Testing MySQL authentication..."
RETRY_COUNT=0
until mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" &>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "❌ Failed to authenticate to MySQL after $MAX_RETRIES attempts"
        echo "📋 Debug info:"
        echo "   Host: $DB_HOST"
        echo "   Port: $DB_PORT"
        echo "   User: $DB_USER"
        echo "   DATABASE_URL: ${DATABASE_URL}"
        exit 1
    fi
    echo "⏳ Waiting for MySQL authentication... (attempt $RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done
echo "✅ MySQL is ready and authenticated!"

# Create database if it doesn't exist
echo "🔍 Checking if database exists..."
DB_EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep "$DB_NAME" || true)
if [ -z "$DB_EXISTS" ]; then
    echo "🛠 Creating database $DB_NAME..."
    php bin/console doctrine:database:create --if-not-exists || {
        echo "❌ Failed to create database"
        exit 1
    }
    echo "✅ Database created successfully"
else
    echo "✅ Database $DB_NAME already exists"
fi

# Check if migrations table exists
MIGRATIONS_TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE 'doctrine_migration_versions';" 2>/dev/null | grep "doctrine_migration_versions" || true)

MIGRATIONS_COUNT=$(ls -1 migrations/*.php 2>/dev/null | wc -l)
if [ "$MIGRATIONS_COUNT" -gt 0 ]; then
    echo "🛠 Found $MIGRATIONS_COUNT migration(s)"

    if [ -z "$MIGRATIONS_TABLE_EXISTS" ]; then
        echo "🆕 First migration run, applying all migrations..."
        php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || {
            echo "❌ Failed to apply migrations"
            exit 1
        }
    else
        echo "🔄 Checking migration status..."
        # Sync metadata storage first
        php bin/console doctrine:migrations:sync-metadata-storage --no-interaction 2>/dev/null || true

        # Check if there are pending migrations
        PENDING=$(php bin/console doctrine:migrations:status 2>/dev/null | grep "New Migrations" | awk '{print $4}' || echo "0")

        if [ "$PENDING" != "0" ] && [ -n "$PENDING" ]; then
            echo "📥 Applying $PENDING pending migration(s)..."
            php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || {
                echo "⚠️ Migration failed, trying to update schema instead..."
                php bin/console doctrine:schema:update --force || {
                    echo "❌ Schema update also failed"
                    exit 1
                }
            }
        else
            echo "✅ All migrations already applied"
            echo "🔧 Checking if schema needs update..."
            php bin/console doctrine:schema:update --dump-sql | grep -q "Nothing to update" || {
                echo "⚠️ Schema differences detected, updating..."
                php bin/console doctrine:schema:update --force || true
            }
        fi
    fi
    echo "✅ Migrations/Schema synchronized"
else
    echo "🛠 No migrations found, updating schema directly..."
    php bin/console doctrine:schema:update --force || {
        echo "❌ Failed to update schema"
        exit 1
    }
    echo "✅ Schema updated successfully"
fi

echo "✅ Database setup completed"

# Clear cache after database setup
echo "🧹 Clearing Symfony cache..."
php bin/console cache:clear --no-interaction || true
php bin/console cache:warmup --no-interaction || true

# -----------------------------
# 🔹 Start FrankenPHP
# -----------------------------
echo "🎯 Starting FrankenPHP..."
if [ -f "/app/Caddyfile" ]; then
    exec frankenphp run --config /app/Caddyfile
else
    # FrankenPHP can auto-detect Symfony apps
    exec frankenphp php-server --root /app/public
fi
