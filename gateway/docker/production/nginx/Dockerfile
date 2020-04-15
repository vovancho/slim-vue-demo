FROM nginx:1.17-alpine

ARG DOMAIN_FRONTEND
ARG DOMAIN_API
ARG DOMAIN_WEBSOCKET
ARG DOMAIN_SWAGGER_UI
ARG DOMAIN_AMQP_MANAGER
ARG DOMAIN_ELK
ARG ELK_KIBANA_USERNAME
ARG ELK_KIBANA_PASSWORD
ARG SWAGGER_UI_USERNAME
ARG SWAGGER_UI_PASSWORD

ENV TZ=Europe/Moscow

RUN test -n "$DOMAIN_FRONTEND" \
    && test -n "$DOMAIN_API" \
    && test -n "$DOMAIN_WEBSOCKET" \
    && test -n "$DOMAIN_SWAGGER_UI" \
    && test -n "$DOMAIN_AMQP_MANAGER" \
    && test -n "$DOMAIN_ELK" \
    && test -n "$ELK_KIBANA_USERNAME" \
    && test -n "$ELK_KIBANA_PASSWORD" \
    && test -n "$SWAGGER_UI_USERNAME" \
    && test -n "$SWAGGER_UI_PASSWORD"

RUN apk add --update openssl

COPY ./common/nginx/snippets /etc/nginx/snippets
COPY ./production/nginx/conf.d /etc/nginx/conf.d

RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_FRONTEND}|$DOMAIN_FRONTEND|g" {} \;
RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_API}|$DOMAIN_API|g" {} \;
RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_WEBSOCKET}|$DOMAIN_WEBSOCKET|g" {} \;
RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_SWAGGER_UI}|$DOMAIN_SWAGGER_UI|g" {} \;
RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_AMQP_MANAGER}|$DOMAIN_AMQP_MANAGER|g" {} \;
RUN find /etc/nginx/conf.d -type f -exec sed -i -e "s|{DOMAIN_ELK}|$DOMAIN_ELK|g" {} \;

WORKDIR /var/www

RUN printf "${ELK_KIBANA_USERNAME}:$(openssl passwd -crypt ${ELK_KIBANA_PASSWORD})\n" >> /var/www/elk.htpasswd
RUN printf "${SWAGGER_UI_USERNAME}:$(openssl passwd -crypt ${SWAGGER_UI_PASSWORD})\n" >> /var/www/swagger.htpasswd
