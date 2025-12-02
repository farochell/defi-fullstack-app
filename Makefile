# Nom du container PHP
PHP_CONTAINER=php

# Commande docker-compose (depuis backend/)
DC=docker compose -f ./docker-compose.yml --env-file ./.env

.PHONY: db-create
db-create:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:database:create --if-not-exists

.PHONY: db-create-test
db-create-test:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction --env=test

.PHONY: db-migrations
db-migrations:
	-$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:diff --no-interaction || true

.PHONY: db-migrate
db-migrate:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: db-migrate-test
db-migrate-test:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction --env=test

.PHONY: db-schema-update
db-schema-update:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:schema:update --force

# ----------------------------
# Initialisation complète
# ----------------------------
.PHONY: db-init
db-init: db-create db-migrations db-migrate
	@echo "✅ Base de données dev prête"

.PHONY: db-init-test
db-init-test: db-create-test db-migrate-test
	@echo "✅ Base de données test prête"
