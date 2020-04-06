up: docker-up
down: docker-down
restart: docker-down docker-up
init: init-env docker-down-clear docker-pull docker-build docker-up project-init
test: api-test api-fixtures
test-coverage: api-test-coverage
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage
test-functional: api-test-functional api-fixtures
test-functional-coverage: api-test-functional-coverage api-fixtures
update: api-composer-update frontend-assets-upgrade ws-assets-upgrade
check: lint validate-schema test
lint: api-lint
validate-schema: api-validate-schema

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose run --rm maintenance rm -f websocket/.ready frontend/.ready api/.ready api/var/oauth/private.key api/var/oauth/public.key websocket/public.key
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

project-init: api-composer-install api-oauth-keys copy-api-oauth-keys frontend-assets-install frontend-ready ws-assets-install ws-ready openapi-config-generate api-wait-db api-migrations api-fixtures api-ready

init-env:
	docker-compose run --rm maintenance sh -c 'if [ ! -f .env ]; then cp -i .env.example .env; fi'

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-composer-update:
	docker-compose run --rm api-php-cli composer update

frontend-assets-install:
	docker-compose run --rm frontend-node yarn install

frontend-assets-upgrade:
	docker-compose run --rm frontend-node yarn upgrade

ws-assets-install:
	docker-compose run --rm project-ws yarn install

ws-assets-upgrade:
	docker-compose run --rm project-ws yarn upgrade

api-oauth-keys:
	docker-compose run --rm api-php-cli mkdir -p var/oauth
	docker-compose run --rm api-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm api-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm api-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

copy-api-oauth-keys:
	docker-compose run --rm maintenance cp api/var/oauth/public.key websocket/public.key

api-wait-db:
	docker-compose exec -T project-db pg_isready --timeout=0 --dbname=app

api-migrations:
	docker-compose run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli php bin/app.php fixtures:load

api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer cs-check

api-ready:
	docker-compose run --rm maintenance touch api/.ready

frontend-ready:
	docker-compose run --rm maintenance touch frontend/.ready

ws-ready:
	docker-compose run --rm maintenance touch websocket/.ready

api-test:
	docker-compose run --rm api-php-cli vendor/bin/phpunit

api-test-coverage:
	docker-compose run --rm api-php-cli composer test-coverage

api-test-unit:
	docker-compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

ws-start:
	docker-compose exec project-ws npm run start

api-clear-cache:
	docker-compose run --rm api-php-cli php bin/app.php orm:clear-cache:metadata

openapi-config-generate:
	docker-compose run --rm api-php-cli vendor/bin/openapi ./src/Http/Action --output ./public/openapi.yml
