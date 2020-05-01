init: init-env \
	docker-down-clear \
	project-clear \
	docker-pull docker-build docker-up \
	project-init
up: docker-up
down: docker-down
restart: down up
check: lint validate-schema test
lint: api-lint frontend-lint ws-lint
validate-schema: api-validate-schema
test: api-test api-fixtures
test-coverage: api-test-coverage
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage
test-functional: api-test-functional api-fixtures
test-functional-coverage: api-test-functional-coverage api-fixtures
packages-update: api-composer-update frontend-assets-upgrade ws-assets-upgrade

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

project-init: api-composer-install api-oauth-keys copy-api-oauth-keys frontend-assets-install frontend-ready ws-assets-install ws-ready openapi-config-generate api-wait-db api-migrations api-fixtures api-ready

init-env:
	docker-compose run --rm maintenance sh -c 'if [ ! -f .env ]; then cp -i .env.example .env; fi'

project-clear:
	docker-compose run --rm maintenance rm -rf websocket/.ready frontend/.ready api/.ready api/var/oauth/private.key api/var/oauth/public.key websocket/public.key var/cache/* var/log/*

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-composer-update:
	docker-compose run --rm api-php-cli composer update

frontend-assets-install:
	docker-compose run --rm frontend-node-cli yarn install

frontend-assets-upgrade:
	docker-compose run --rm frontend-node-cli yarn upgrade

frontend-ready:
	docker-compose run --rm maintenance touch frontend/.ready

frontend-lint:
	docker-compose run --rm frontend-node-cli yarn eslint
	docker-compose run --rm frontend-node-cli yarn stylelint --custom-syntax stylelint-plugin-stylus/custom-syntax

frontend-eslint-fix:
	docker-compose run --rm frontend-node-cli yarn eslint-fix

frontend-pretty:
	docker-compose run --rm frontend-node-cli yarn prettier

ws-ready:
	docker-compose run --rm maintenance touch websocket/.ready

ws-assets-install:
	docker-compose run --rm ws-node-cli yarn install

ws-assets-upgrade:
	docker-compose run --rm ws-node-cli yarn upgrade

ws-lint:
	docker-compose run --rm ws-node-cli yarn eslint

ws-eslint-fix:
	docker-compose run --rm ws-node-cli yarn eslint-fix

ws-pretty:
	docker-compose run --rm ws-node-cli yarn prettier

ws-start:
	docker-compose exec ws npm run start

api-oauth-keys:
	docker-compose run --rm api-php-cli mkdir -p var/oauth
	docker-compose run --rm api-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm api-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm api-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

copy-api-oauth-keys:
	docker-compose run --rm maintenance cp api/var/oauth/public.key websocket/public.key

api-wait-db:
	docker-compose exec -T api-db pg_isready --timeout=0 --dbname=app

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

api-clear-cache:
	docker-compose run --rm api-php-cli php bin/app.php orm:clear-cache:metadata

openapi-config-generate:
	docker-compose run --rm api-php-cli vendor/bin/openapi ./src/Http/Action --output ./public/openapi.yml

build: build-gateway build-frontend build-api build-amqp build-elk build-websocket

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/slim-vue-demo-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/slim-vue-demo-frontend-nginx:${IMAGE_TAG} frontend-nginx

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/slim-vue-demo-api-nginx:${IMAGE_TAG} api-nginx
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/slim-vue-demo-api-php-fpm:${IMAGE_TAG} api-nginx
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/slim-vue-demo-api-php-cli:${IMAGE_TAG} api-nginx

build-amqp:
	docker --log-level=debug build --pull --file=amqp/docker/Dockerfile --tag=${REGISTRY}/slim-vue-demo-amqp:${IMAGE_TAG} amqp

build-elk:
	docker --log-level=debug build --pull --file=elk/docker/Dockerfile --tag=${REGISTRY}/slim-vue-demo-elk:${IMAGE_TAG} elk

build-websocket:
	docker --log-level=debug build --pull --file=websocket/docker/Dockerfile --tag=${REGISTRY}/slim-vue-demo-ws:${IMAGE_TAG} ws

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api push-amqp push-elk push-websocket

push-gateway:
	docker push ${REGISTRY}/slim-vue-demo-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/slim-vue-demo-frontend-nginx:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/slim-vue-demo-api-nginx:${IMAGE_TAG}
	docker push ${REGISTRY}/slim-vue-demo-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/slim-vue-demo-api-php-cli:${IMAGE_TAG}

push-amqp:
	docker push ${REGISTRY}/slim-vue-demo-amqp:${IMAGE_TAG}

push-elk:
	docker push ${REGISTRY}/slim-vue-demo-elk:${IMAGE_TAG}

push-websocket:
	docker push ${REGISTRY}/slim-vue-demo-ws:${IMAGE_TAG}

deploy:
	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	scp -P ${PORT} .env.production ${HOST}:site_${BUILD_NUMBER}/.env
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=slim-vue-demo" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build -d api-db api-php-cli'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run api-php-cli wait-for-it api-db:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

rollback:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
