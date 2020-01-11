up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear api-clear docker-pull docker-build docker-up project-init
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

project-init: api-composer-install frontend-assets-install api-oauth-keys api-wait-db api-migrations api-fixtures api-ready

api-clear:
	docker run --rm -v ${PWD}/api:/tmp --workdir=/tmp alpine rm -f .ready

api-composer-install:
	docker-compose run --rm api-php-cli composer install

frontend-assets-install:
	docker-compose run --rm frontend-node yarn install
#	docker-compose run --rm frontend-node yarn build

api-oauth-keys:
	docker-compose run --rm api-php-cli mkdir -p var/oauth
	docker-compose run --rm api-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm api-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm api-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

api-wait-db:
	docker-compose exec -T project-db pg_isready --timeout=0 --dbname=app

api-migrations:
	docker-compose run --rm api-php-cli php bin/console doctrine:migrations:migrate --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli php bin/app.php fixtures:load

api-process-consumer:
	docker-compose run --rm api-php-cli php bin/app.php tasks:process

api-ready:
	docker run --rm -v ${PWD}/api:/tmp --workdir=/tmp alpine touch .ready

# frontend-assets-dev:
# 	docker-compose run --rm frontend-node yarn dev

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
