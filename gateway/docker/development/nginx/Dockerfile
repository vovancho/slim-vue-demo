FROM nginx:1.17-alpine

ARG ELK_KIBANA_USERNAME
ARG ELK_KIBANA_PASSWORD
ARG SWAGGER_UI_USERNAME
ARG SWAGGER_UI_PASSWORD

ENV TZ=Europe/Moscow

RUN test -n "$ELK_KIBANA_USERNAME" \
    && test -n "$ELK_KIBANA_PASSWORD" \
    && test -n "$SWAGGER_UI_USERNAME" \
    && test -n "$SWAGGER_UI_PASSWORD"

RUN apk add --update openssl

COPY ./common/nginx/snippets /etc/nginx/snippets
COPY ./development/nginx/conf.d /etc/nginx/conf.d

WORKDIR /var/www

RUN printf "${ELK_KIBANA_USERNAME}:$(openssl passwd -crypt ${ELK_KIBANA_PASSWORD})\n" >> /var/www/elk.htpasswd
RUN printf "${SWAGGER_UI_USERNAME}:$(openssl passwd -crypt ${SWAGGER_UI_PASSWORD})\n" >> /var/www/swagger.htpasswd
