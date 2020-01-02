FROM node:12.7-slim

# not mandatory if you EXPORT a fixed port. See below.
ARG YOUR_APP_WEB_HTTP_PORT

RUN apt-get -y update \
	&& apt-get install -y git

RUN yarn global add @vue/cli -g

WORKDIR /var/www/frontend

RUN apt-get autoremove -y \
    && apt-get autoclean -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

# Or just use EXPORT 8080
EXPOSE ${YOUR_APP_WEB_HTTP_PORT}
EXPOSE 8000
# If yout want use vue-cli UI you need to also EXPORT 8000

USER node

# switch to npm if you chose it as package manager
CMD ["yarn", "serve"]
