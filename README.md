# Приложение Обработчик задач [![Build Status](https://travis-ci.com/vovancho/slim-vue-demo.svg?token=73g6SyN3hhjhz6WZX1ws&branch=master)](https://travis-ci.com/vovancho/slim-vue-demo) [![Coverage Status](https://coveralls.io/repos/github/vovancho/slim-vue-demo/badge.svg?branch=master)](https://coveralls.io/github/vovancho/slim-vue-demo?branch=master)

Демонстрационное приложение представляет из себя динамичный многопользовательский обработчик задач. Каждый пользователь может завести свою задачу *(как приватную, так и видимую для всех)*. При этом он может наблюдать за процессом обработки, номером в очереди своих и чужих задач в динамическом списке.

Приложение имеет свою систему регистрации/аутентификации пользователей.

![demo_animated.gif](doc/resources/demo_animated.gif)
<p align="center"><sup><i>(Анимация работы приложения)</i></sup></p>

* [Установка](#установка)
* [Описание сервисов приложения](#описание-сервисов-приложения)
* [Переменные окружения](#переменные-окружения)
* [Стек и схема работы](#стек-и-схема-работы)
  * [Стек Backend](#стек-backend)
  * [Стек Frontend](#стек-frontend)
* [Запуск тестов](#запуск-тестов)

## Установка

* Склонируйте репозиторий и выполните в папке проекта команду `make init`
* Дождитесь окончания процесса поднятия проекта
* Перейдите по ссылке http://127.0.0.1:8080

> Для пользователей Windows необходимо установить утилиту `make`  
> <sup>[https://stackoverflow.com/questions/544362/is-there-an-equivalent-of-make-on-windows](https://stackoverflow.com/questions/544362/is-there-an-equivalent-of-make-on-windows)</sup>
>
> или выполнить команды вручную, описанные в файле `Makefile`.

```bash
> git clone https://github.com/vovancho/slim-vue-demo.git
> cd slim-vue-demo
> make init
```

## Описание сервисов приложения

[docker-compose.yml](docker-compose.yml)

Сервис             | Открытые порты                                             |  Описание
------------------ | ---------------------------------------------------------- |  -----------------------------------------------------------------------------------
gateway            |                                                            |  Шлюз проекта
frontend-nginx     | <sup>8080 (шлюз)</sup>                                     |  <p>Веб-сервер Фронтенда </p><sup><p> Имеющееся учетная запись: `user@app.dev:secret`</p></sup>
frontend-node      |                                                            |  Веб-сервер Фронтенда для разработки
frontend-node-cli  |                                                            |  Сервисный клиент NodeJs *(Установка пакетов)*
api-nginx          | <sup>8081 (шлюз)</sup>                                     |  Веб-сервер API
api-php-cli        |                                                            |  Сервисный клиент PHP *(Миграции/Тесты/Фикстуры/OAuth ключи/Генерация документации)*
api-php-fpm        |                                                            |  PHP клиент для API
api-queue-consumer |                                                            |  Точка входа для выполнения задач из очереди *(Консольное приложение)*
api-db             | <sup>54321</sup>                                           |  <p>БД PostgreSQL для API </p><sup><p>Подключение: `pgsql://api:secret@api-db:54321/api` </p></sup>
mailer             | <sup>8082 (шлюз)</sup>                                     |  Почтовый сервер *(Получение писем с токеном при регистрации нового пользователя)*
ws                 | <sup>8084 (шлюз)</sup>                                     |  WebSocket-сервер *(Для отслеживания состояния выполнения задач)*
amqp               | <sup><p>8083 (шлюз) <i>(админка)</i></p><p>5672 </p></sup> |  <p>RabbitMQ сервер *(Очередь для взаимодействия сервисов)* </p><sup><p>Логин/Пароль `rabbit:rabbit`</p></sup>
swagger-ui         | <sup>8085 (шлюз)</sup>                                     |  Документация API
elk                | <sup>5601 (шлюз)</sup>                                     |  <p>Стек ELK для сбора логов </p><sup><p>Сервисов `api-nginx`, `api-php-fpm`, `api-queue-consumer`</p></sup><p>Логин/Пароль `elastic:secret`</p>
maintenance        |                                                            |  Сервисный контейнер для инициализации проекта

## Переменные окружения

Путь к файлу переменных окружения: `./.env`

> Переменные окружения для разработки определены в `./.env.example`  
> Переменные окружения для продакшена определены в `./.env.production`  

#### Общие

Имя                          | Описание
---------------------------- | ----------------------------------------------------
`SWAGGER_CONFIG_URL`         | Путь к конфигурации Swagger
`SWAGGER_URL`                | URL Swagger UI для CORS защиты веб-сервера API
`API_ENV`                    | Окружение проекта
`API_DEBUG`                  | Включить DEBUG режим
`API_DB_HOST`                | Хост подключения к БД PostgreSQL
`API_DB_USER`                | Пользователь подключения к БД PostgreSQL
`API_DB_PASSWORD`            | Пароль подключения к БД PostgreSQL
`API_DB_NAME`                | Имя базы данных подключения к PostgreSQL
`API_MAILER_HOST`            | Хост почтового сервера
`API_MAILER_PORT`            | Порт почтового сервера
`API_MAILER_USERNAME`        | Пользователь почтового сервера
`API_MAILER_PASSWORD`        | Пароль почтового сервера
`API_MAILER_ENCRYPTION`      | Шифрование почтового сервера
`API_MAILER_FROM_EMAIL`      | Email отправителя для почтового сервера
`API_OAUTH_PUBLIC_KEY_PATH`  | Путь к публичному ключу
`API_OAUTH_PRIVATE_KEY_PATH` | Путь к приватному ключу
`API_OAUTH_ENCRYPTION_KEY`   | Ключ шифрования OAuth
`API_AMQP_HOST`              | Хост AMQP сервера
`API_AMQP_PORT`              | Порт AMQP сервера
`API_AMQP_USERNAME`          | Пользователь AMQP сервера
`API_AMQP_PASSWORD`          | Пароль AMQP сервера
`API_AMQP_VHOST`             | Виртуальный хост AMQP сервера
`API_AMQP_COOKIE`            | Cookie AMQP сервера
`VUE_APP_API_URL`            | URL API сервера для фронтенда
`VUE_APP_WS_URL`             | URL WebSocket сервера для фронтенда
`WS_JWT_PUBLIC_KEY`          | Путь к публичному ключу для WebSocket сервера
`WS_AMQP_URI`                | URI подключения к AMQP серверу для WebSocket сервера
`ELK_KIBANA_USERNAME`        | Логин для Kibana сервиса ELK
`ELK_KIBANA_PASSWORD`        | Пароль для Kibana сервиса ELK
`ELK_ES_JAVA_OPTS`           | Размер кучи ElasticSearch сервиса ELK
`SWAGGER_UI_USERNAME`        | Логин для документации API сервиса Swagger
`SWAGGER_UI_PASSWORD`        | Пароль для документации API сервиса Swagger

#### Для разработки

Имя                          | Описание
---------------------------- | ----------------------------------------------------
`FRONTEND_URL`               | URL фронтенда (для формирования URL, например в электронных письмах)

#### Для продакшена

Имя                          | Описание
---------------------------- | ----------------------------------------------------
`DOMAIN_FRONTEND`            | Имя домена фронтенда
`DOMAIN_API`                 | Имя домена API
`DOMAIN_WEBSOCKET`           | Имя домена WebSocket сервера
`DOMAIN_SWAGGER_UI`          | Имя домена документации API (Swagger)
`DOMAIN_AMQP_MANAGER`        | Имя домена админки RabbitMQ
`DOMAIN_ELK`                 | Имя домена Kibana сервиса ELK

## Стек и схема работы

![scheme_of_work.svg](doc/resources/scheme_of_work.svg)
<p align="center"><sup><i>(Схема работы приложения)</i></sup></p>

### Стек Backend

 - Slim 4
   - Doctrine
   - OAuth2 Server <sup><i>* league/oauth2-server</i></sup>
   - Symfony Console
   - Monolog
   - PHPUnit
 - PostgreSQL
 - RabbitMQ
 - Swagger UI <sup><i>* документация API</i></sup>
 - ELK <sup><i>* сбор логов</i></sup>

### Стек Frontend

 - VueJS
   - Vue CLI 3
   - Vuetify
 - RabbitMQ
 - WebSocket
 - JWT

## Запуск тестов

Запуск тестов:

```bash
> make test
```

Запуск только Unit тестов

```bash
> make test-unit
```

Запуск только Functional тестов

```bash
> make test-functional
```
