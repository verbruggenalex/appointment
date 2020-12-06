ARG PHP_EXTENSIONS="apcu bcmath gd mysqli pdo_mysql"
ARG PHP_VERSION=7.4
ARG INSTALL_CRON=1

FROM thecodingmachine/php:$PHP_VERSION-v3-slim-apache as prod

USER root

# For now we are unable to manage vhosts for Drupal through Traefik only. This
# is why we use apache2 to map our environment domains to their respective
# folders. For issues on this look at the following links:
# @see: https://github.com/symfony/symfony/issues/32307
# @see: https://www.drupal.org/project/drupal/issues/3086483
# @see: https://github.com/drupal/drupal/blob/8.9.x/sites/default/default.settings.php#L376-L404
# @see: https://doc.traefik.io/traefik/middlewares/addprefix/

# That solution would use addprefix in the docker-compose with traefik labels
# like this:
#
# traefik.enable: true
# # Production environment.
# traefik.http.routers.production.tls: true
# traefik.http.routers.production.rule: Host(`production.${TRAEFIK_DOMAIN}`)
# traefik.http.routers.production.middlewares: production-redirect
# traefik.http.middlewares.production-redirect.addprefix.prefix: /build/production
# # Pre production environment
# traefik.http.routers.pre-production.tls: true
# traefik.http.routers.pre-production.rule: Host(`pre-production.${TRAEFIK_DOMAIN}`)
# traefik.http.routers.pre-production.middlewares: pre-production-redirect
# traefik.http.middlewares.pre-production-redirect.addprefix.prefix: /build/pre-production
# # Post production environment
# traefik.http.routers.post-production.tls: true
# traefik.http.routers.post-production.rule: Host(`post-production.${TRAEFIK_DOMAIN}`)
# traefik.http.routers.post-production.middlewares: post-production-redirect
# traefik.http.middlewares.post-production-redirect.addprefix.prefix: /build/post-production

COPY apache2/sites-available/* /etc/apache2/sites-available/
RUN a2dissite 000-default.conf && \
    a2dissite default-ssl.conf && \
    a2ensite web.conf && \
    a2ensite production.conf && \
    a2ensite pre-production.conf && \
    a2ensite post-production.conf

# Change back Apache user and group to www-data use production ini.
ENV APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    TEMPLATE_PHP_INI=production \
    PHP_INI_DISABLE_FUNCTIONS=phpinfo

RUN apt-get update && \
    apt-get install -y patch mysql-client --no-install-recommends

RUN curl -sS https://getcomposer.org/installer | php -- --2 --install-dir=/usr/local/bin --filename=composer

FROM prod as ci

ENV PHP_INI_DISABLE_FUNCTIONS=""
