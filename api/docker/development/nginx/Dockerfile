FROM nginx:1.17-alpine

ARG SWAGGER_URL=http://127.0.0.1:8086/

COPY ./common/nginx/conf.d /etc/nginx/conf.d

RUN sed -i -e "s|{SWAGGER_URL}|$SWAGGER_URL|g" /etc/nginx/conf.d/default.conf

WORKDIR /var/www/api
