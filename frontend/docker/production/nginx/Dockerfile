FROM node:13-alpine as builder

ENV TZ=Europe/Moscow

WORKDIR /var/www

COPY ./package.json ./yarn.lock ./
RUN yarn install

COPY ./ ./
RUN yarn build

FROM nginx:1.17-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /var/www

COPY --from=builder /var/www/build ./public
