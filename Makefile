up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up project-init
init-windows: init-env-windows init
init-linux: init-env-linux init
test: api-test
test-coverage: api-test-coverage
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

project-init: api-composer-install frontend-assets-install frontend-ready api-oauth-keys openapi-config-generate api-wait-db api-migrations api-fixtures api-ready

api-composer-install:
	docker-compose run --rm api-php-cli composer install

frontend-assets-install:
	docker-compose run --rm frontend-node yarn install

api-oauth-keys:
	docker-compose run --rm api-php-cli mkdir -p var/oauth
	docker-compose run --rm api-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm api-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm api-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

api-wait-db:
	docker-compose exec -T project-db pg_isready --timeout=0 --dbname=app

api-migrations:
	docker-compose run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli php bin/app.php fixtures:load

api-process-consumer:
	docker-compose run --rm api-php-cli php bin/app.php tasks:process

api-ready:
	docker-compose exec api-php-cli touch .ready

frontend-ready:
	docker-compose run --rm frontend-node touch .ready

api-test:
	docker-compose run --rm api-php-cli vendor/bin/phpunit

api-test-coverage:
	docker-compose run --rm api-php-cli vendor/bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

api-test-unit:
	docker-compose run --rm api-php-cli vendor/bin/phpunit --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm api-php-cli vendor/bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage

ws-start:
	docker-compose exec project-ws npm run start

api-clear-cache:
	docker-compose run --rm api-php-cli php bin/app.php orm:clear-cache:metadata

openapi-config-generate:
	docker-compose run --rm api-php-cli vendor/bin/openapi ./src/Http/Action --output ./public/openapi.yml

init-env-windows:
	echo n | copy /-y ".env.example" ".env"
	echo n | copy /-y "api/.env.example" "api/.env"
	echo n | copy /-y "frontend/.env.example" "frontend/.env"
	echo n | copy /-y "websocket/.env.example" "websocket/.env"

init-env-linux:
	cp -n .env.example .env
	cp -n api/.env.example api/.env
	cp -n frontend/.env.example frontend/.env
	cp -n websocket/.env.example websocket/.env
