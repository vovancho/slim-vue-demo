FROM nginx:1.17-alpine

ENV TZ=Europe/Moscow

COPY ./docker/common/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY ./public ./public
