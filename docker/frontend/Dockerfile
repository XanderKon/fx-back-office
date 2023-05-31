FROM node:18.16-alpine

RUN npm install -g http-server

WORKDIR /var/www

COPY ./package*.json ./
COPY ./ .

RUN npm i && npm run build

EXPOSE 8080
CMD [ "http-server", "dist" ]
