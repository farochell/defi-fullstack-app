ifeq ($(CI), true)
	DC=docker compose -f ./docker-compose.ci.yml
	PHP_CONTAINER=backend
else
	DC=docker compose -f ./docker-compose.yml --env-file ./.env
	PHP_CONTAINER=backend
endif

# ----------------------------
# Base de données
# ----------------------------
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
# Initialisation BDD complète
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

# ----------------------------
# Phpstan & phpcs
# ----------------------------
.PHONY: phpstan
phpstan:
	$(DC) exec $(PHP_CONTAINER) ./vendor/bin/phpstan analyse --memory-limit=2G

.PHONY: phpcs
phpcs:
	$(DC) exec $(PHP_CONTAINER) ./vendor/bin/php-cs-fixer fix src

# ----------------------------
# Tests PHP
# ----------------------------
.PHONY: test
test:
	$(DC) exec $(PHP_CONTAINER) ./vendor/bin/phpunit --colors=always

.PHONY: test-coverage
test-coverage:
	$(DC) exec -e XDEBUG_MODE=coverage $(PHP_CONTAINER) ./vendor/bin/phpunit --coverage-html reports/coverage

# ----------------------------
# Tests Vue.js avec Vitest
# ----------------------------
FRONTEND_CONTAINER=frontend

.PHONY: vitest
vitest:
	$(DC) exec $(FRONTEND_CONTAINER) npx vitest run

.PHONY: vitest-coverage
vitest-coverage:
	$(DC) exec $(FRONTEND_CONTAINER) npx vitest run --coverage
