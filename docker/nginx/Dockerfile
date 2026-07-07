FROM nginx:alpine

COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./conf.d/* /etc/nginx/conf.d/

WORKDIR /var/www/html

CMD ["nginx"]

EXPOSE 80 443
