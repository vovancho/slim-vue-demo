# Приложение Обработчик задач [![Build Status](https://travis-ci.com/vovancho/slim-vue-demo.svg?token=73g6SyN3hhjhz6WZX1ws&branch=master)](https://travis-ci.com/vovancho/slim-vue-demo)

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

Сервис             | Открытые порты                                      |  Описание
------------------ | --------------------------------------------------- |  -----------------------------------------------------------------------------------
api-nginx          | <sup>8081</sup>                                     |  Веб-сервер API
api-php-cli        |                                                     |  Сервисный клиент PHP *(Миграции/Тесты/Фикстуры/OAuth ключи/Генерация документации)*
api-php-fpm        |                                                     |  PHP клиент для API
api-queue-consumer |                                                     |  Точка входа для выполнения задач из очереди *(Консольное приложение)*
project-db         | <sup>54321</sup>                                    |  <p>БД PostgreSQL для API </p><sup><p>Подключение: `pgsql://api:secret@project-db:54321/api` </p></sup>
frontend-node      | <sup>8080</sup>                                     |  <p>Веб-сервер Фронтенда </p><sup><p> Имеющееся учетная запись: `user@app.dev:secret`</p></sup>
mailer             | <sup>8082</sup>                                     |  Почтовый сервер *(Получение писем с токеном при регистрации нового пользователя)*
project-ws         | <sup>8084</sup>                                     |  WebSocket-сервер *(Для отслеживания состояния выполнения задач)*
project-amqp       | <sup><p>8085 <i>(админка)</i></p><p>5672 </p></sup> |  <p>RabbitMQ сервер *(Очередь для взаимодействия сервисов)* </p><sup><p>Логин/Пароль `rabbit:rabbit`</p></sup>
swagger-ui         | <sup>8086</sup>                                     |  Документация API
maintenance        |                                                     |  Сервисный контейнер для инициализации проекта

## Переменные окружения

Путь к файлу переменных окружения: `./.env`

> Переменные окружения определены в `./.env.example`

Имя                          | Описание
---------------------------- | ----------------------------------------------------
`SWAGGER_CONFIG_URL`         | Путь к конфигурации Swagger
`SWAGGER_URL`                | URL Swagger UI для CORS защиты веб-сервера API
`API_ENV`                    | Окружение проекта
`API_DEBUG`                  | Включить DEBUG режим
`API_DB_URL`                 | URL подключения PostgreSQL
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
`VUE_APP_API_URL`            | URL API сервера для фронтенда
`VUE_APP_WS_URL`             | URL WebSocket сервера для фронтенда
`WS_JWT_PUBLIC_KEY`          | Путь к публичному ключу для WebSocket сервера
`WS_AMQP_URI`                | URI подключения к AMQP серверу для WebSocket сервера

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
