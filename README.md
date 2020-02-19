# Приложение Обработчик задач [![Build Status](https://travis-ci.com/vovancho/slim-vue-demo.svg?token=73g6SyN3hhjhz6WZX1ws&branch=master)](https://travis-ci.com/vovancho/slim-vue-demo)

Демонстрационное приложение представляет из себя динамичный многопользовательский обработчик задач. Каждый пользователь может завести свою задача *(как приватную, так и видимую для всех)*. При этом он может наблюдать за процессом обработки, номером в очереди своих и чужих задач в динамическом списке.

Приложение имеет свою систему регистрации/аутентификации пользователей.

![demo_animated.gif](doc/resources/demo_animated.gif)
<p align="center"><sup><i>(Анимация работы приложения)</i></sup></p>

* [Установка](#установка)
* [Описание сервисов приложения](#описание-сервисов-приложения)
* [Стек и схема работы](#стек-и-схема-работы)
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

Сервис             | Открытые порты                           | Учетные данные                            |  Описание
------------------ | ---------------------------------------- | ----------------------------------------- |  -----------------------------------------------------------------------------------
api-nginx          | 8081                                     |                                           |  Веб-сервер API
api-php-cli        |                                          |                                           |  Сервисный клиент PHP *(Миграции/Тесты/Фикстуры/Oauth ключи/Генерация документации)*
api-php-fpm        |                                          |                                           |  PHP клиент для API
api-queue-consumer |                                          |                                           |  Точка входа для выполнения задач из очереди *(Консольное приложение)*
project-db         | 54321                                    | `pgsql://api:secret@project-db:54321/api` |  БД PostgreSQL для API
frontend-node      | 8080                                     | `user@app.dev:secret`                     |  Веб-сервер Фронтенда
mailer             | 8082                                     |                                           |  Почтовый сервер *(Получение писем с токеном при регистрации нового пользователя)*
project-ws         | 8084                                     |                                           |  WebSocket-сервер *(Для отслеживания состояния выполнения задач)*
project-amqp       | <p>8085 <i>(админка)</i></p><p>5672 </p> | `rabbit:rabbit`                           |  RabbitMQ сервер *(Очередь для взаимодействия сервисов)*
swagger-ui         | 8086                                     |                                           |  Документация API
maintenance        |                                          |                                           |  Сервисный контейнер для инициализации проекта

## Стек и схема работы

![scheme_of_work.svg](doc/resources/scheme_of_work.svg)
<p align="center"><sup><i>(Схема работы приложения)</i></sup></p>

### Стек Api

 - Slim 4
 - PostgreSQL
 - RabbitMQ

### Стек Frontend

 - VueJS
 - Vue CLI 3
 - Vuetify
 - Stylus

## Особенности приложения

## Запуск тестов
