FROM node:10.11-jessie

RUN apt-get -y update \
	&& apt-get install -y netcat

COPY start.sh /start.sh

RUN chmod +x /start.sh

WORKDIR /var/www
