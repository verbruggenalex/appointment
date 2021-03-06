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
	echo "127.0.0.1	${TRAEFIK_DOMAIN} traefik.${TRAEFIK_DOMAIN} portainer.${TRAEFIK_DOMAIN} dev.${TRAEFIK_DOMAIN} ide.${TRAEFIK_DOMAIN} web.${TRAEFIK_DOMAIN} production.${TRAEFIK_DOMAIN} pre-production.${TRAEFIK_DOMAIN} post-production.${TRAEFIK_DOMAIN}" | sudo tee -a /etc/hosts


up-dev:
	docker-compose up -d traefik portainer dev mysql selenium backstop
down-dev:
	docker-compose rm -s -f traefik portainer dev mysql selenium backstop
up-ci:
	docker-compose up -d traefik ci mysql selenium
down-ci:
	docker-compose rm -s -f traefik ci mysql selenium
up-prod:
	docker-compose up -d traefik prod mysql
down-prod:
	docker-compose rm -s -f traefik prod mysql

unpack:
	rm -f dist.tar.gz
	wget https://github.com/verbruggenalex/appointment/releases/download/$(tag)/dist.tar.gz && \
	sudo rm -rf build/dist/$(tag) && mkdir -p build/dist/$(tag) && \
	tar -zxf dist.tar.gz --directory=build/dist/$(tag) && \
	ln -sfn dist/$(tag)/ build/pre-production

deploy:
	docker-compose exec -T prod composer --working-dir=build/pre-production reset-permissions && \
	docker-compose exec -T prod drush @prod sql-dump  --skip-tables-list=cache,cache_* --result-file=../../../pre-production/dump.sql && \
	docker-compose exec -T prod drush @pre-prod sql-drop -y && \
	docker-compose exec -T prod drush @pre-prod sql-create -y && \
	docker-compose exec -T prod drush @pre-prod sqlc < build/pre-production/dump.sql && \
	docker-compose exec -T prod drush @pre-prod cache:rebuild && \
	docker-compose exec -T prod drush @pre-prod updatedb -y --no-post-updates && \
	docker-compose exec -T prod drush @pre-prod config:import -y && \
	docker-compose exec -T prod drush @pre-prod updatedb -y --post-updates && \
	docker-compose exec -T prod drush @pre-prod cache:rebuild && \
	docker-compose exec -T prod drush @pre-prod status

accept:
	ln -sfn dist/$$(basename $$(readlink -f build/production)) build/post-production && \
	ln -sfn dist/$$(basename $$(readlink -f build/pre-production)) build/production
	ln -sfn dist/0.0.1 build/pre-production

rollback:
	ln -sfn dist/$$(basename $$(readlink -f build/production)) build/pre-production && \
	ln -sfn dist/$$(basename $$(readlink -f build/post-production)) build/production && \
	ln -sfn dist/0.0.1 build/post-production

cleanup:
	find build/dist -maxdepth 1 -mindepth 1 -type d ! -name 0.0.1 ! -name 0.0.2 ! -name 0.0.3 ! -name "$$(basename $$(readlink -f build/production))" ! -name "$$(basename $$(readlink -f build/pre-production))" ! -name "$$(basename $$(readlink -f build/post-production))" -exec sudo rm -rf "{}" \;

drop-dbs-incomplete:
	find build/dist -maxdepth 1 -mindepth 1 -type d ! -name "$$(basename $$(readlink -f build/production))" ! -name "$$(basename $$(readlink -f build/pre-production))" ! -name "$$(basename $$(readlink -f build/post-production))" -exec sudo docker-compose exec mysql mysql -u root DROP DATABASE IF EXISTS 'sed -e "s/^build\///" | sed -r "s/\//_/" <(echo $1)' {} \;
