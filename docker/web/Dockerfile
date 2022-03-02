FROM nginx:latest

USER root
COPY docker/web/default.conf /etc/nginx/conf.d/default.conf
RUN mkdir -p /opt
COPY app /opt/app
RUN chown -R 1001:0 /opt/app

# implement changes required to run NGINX as an unprivileged user
RUN sed -i -e '/listen/!b' -e '/80;/!b' -e 's/80;/8080;/' /etc/nginx/conf.d/default.conf \
    && sed -i -e '/user/!b' -e '/nginx/!b' -e '/nginx/d' /etc/nginx/nginx.conf \
    && sed -i 's!/var/run/nginx.pid!/tmp/nginx.pid!g' /etc/nginx/nginx.conf \
    && sed -i "/^http {/a \    proxy_temp_path /tmp/proxy_temp;\n    client_body_temp_path /tmp/client_temp;\n    fastcgi_temp_path /tmp/fastcgi_temp;\n    uwsgi_temp_path /tmp/uwsgi_temp;\n    scgi_temp_path /tmp/scgi_temp;\n" /etc/nginx/nginx.conf

# nginx user must own the cache directory to write cache
RUN chown -R 1001:0 /var/cache/nginx \
    && chmod -R g+w /var/cache/nginx

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stdout /var/log/nginx/error.log

EXPOSE 8080

STOPSIGNAL SIGTERM

USER 1001

CMD ["nginx", "-g", "daemon off;"]
