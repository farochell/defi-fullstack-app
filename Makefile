# Nom du container PHP
PHP_CONTAINER=backend

# Commande docker-compose (depuis backend/)
DC=docker compose -f ./docker-compose.yml --env-file ./.env

.PHONY: db-create
db-create:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:database:create --if-not-exists

.PHONY: db-create-test
db-create-test:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:database:create --if-not-exists --env=test

.PHONY: db-migrations
db-migrations:
	-$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:diff --no-interaction || true

.PHONY: db-migrate
db-migrate:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: db-update-test
db-update-test:
	$(DC) exec $(PHP_CONTAINER) php bin/console doctrine:schema:update --force --no-interaction --env=test

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
db-init-test: db-create-test db-update-test
	@echo "✅ Base de données test prête"

.PHONY: fixtures
fixtures:
	$(DC) exec $(PHP_CONTAINER) php bin/console --env=test doctrine:fixtures:load --no-interaction
