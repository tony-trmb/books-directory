FROM nginx:stable

ADD ./nginx.conf /etc/nginx/nginx.conf
ADD ./default.conf /etc/nginx/conf.d/default.conf
COPY ./entrypoint.sh /usr/bin/entrypoint.sh

RUN chmod +x /usr/bin/entrypoint.sh

WORKDIR /app

CMD ["/bin/bash", "-c", "/usr/bin/entrypoint.sh"]
