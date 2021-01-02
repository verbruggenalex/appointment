#!make

include .env

mkcert_setup:
	curl -s https://api.github.com/repos/FiloSottile/mkcert/releases/latest \
	| grep "browser_download_url.*-linux-amd64" \
	| cut -d : -f 2,3 \
	| tr -d \" \
	| sudo wget -O /usr/local/bin/mkcert -qi - \
	&& sudo chmod +x /usr/local/bin/mkcert
mkcert_install:
	mkcert -install \
	&& mkdir -p certs \
	&& mkcert -cert-file certs/local-cert.pem -key-file certs/local-key.pem "${TRAEFIK_DOMAIN}" "*.${TRAEFIK_DOMAIN}"
docker_network_create:
	docker network create ${TRAEFIK_NETWORK}
setup_hosts:
	echo "127.0.0.1	${TRAEFIK_DOMAIN} traefik.${TRAEFIK_DOMAIN} portainer.${TRAEFIK_DOMAIN} smtp.${TRAEFIK_DOMAIN} dev.${TRAEFIK_DOMAIN} ide.${TRAEFIK_DOMAIN} web.${TRAEFIK_DOMAIN} production.${TRAEFIK_DOMAIN} pre-production.${TRAEFIK_DOMAIN} post-production.${TRAEFIK_DOMAIN}" | sudo tee -a /etc/hosts


dev:
	docker-compose up -d traefik portainer dev mysql selenium smtp backstop
ci:
	docker-compose up -d traefik ci mysql selenium smtp
prod:
	docker-compose up -d traefik prod mysql smtp

unpack:
	wget https://github.com/verbruggenalex/appointment/releases/download/$(tag)/dist.tar.gz \
	&& rm -rf build/dist/$(tag) && mkdir -p build/dist/$(tag) \
	&& tar -zxf dist.tar.gz --directory=build/dist/$(tag) \
	&& ln -sfn dist/$(tag)/ build/pre-production

deploy:
	docker-compose exec prod drush @pre-prod cache:rebuild && \
	docker-compose exec prod drush @pre-prod updatedb -y --no-post-updates && \
	docker-compose exec prod drush @pre-prod config:import -y && \
	docker-compose exec prod drush @pre-prod updatedb -y --post-updates && \
	docker-compose exec prod drush @pre-prod cache:rebuild && \
	docker-compose exec prod drush @pre-prod status

accept:
	ln -sfn dist/$$(basename $$(readlink -f build/production)) build/post-production && \
	ln -sfn dist/$$(basename $$(readlink -f build/pre-production)) build/production
	ln -sfn dist/0.0.1 build/pre-production
