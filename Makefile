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
	echo "127.0.0.1  traefik.${TRAEFIK_DOMAIN} portainer.${TRAEFIK_DOMAIN} dev.${TRAEFIK_DOMAIN} ide.${TRAEFIK_DOMAIN} web.${TRAEFIK_DOMAIN} production.${TRAEFIK_DOMAIN} pre-production.${TRAEFIK_DOMAIN} post-production.${TRAEFIK_DOMAIN}" | sudo tee -a /etc/hosts


dev:
	docker-compose up -d traefik portainer dev mysql selenium smtp
ci:
	docker-compose up -d traefik ci mysql selenium
prod:
	docker-compose up -d traefik portainer prod mysql

# Deployment commands
deploy:
		export BACKUP_TIME=$$(date +'%y%m%d%H%M%S') \
		&& export BACKUP_DATE=$$(date +'%y%m%d') \
		&& export BACKUP_LOC=$${APACHE_DOCUMENT_ROOT}/build/bak/$${BACKUP_DATE} \
		&& rm -rf build/dist/$(tag) && mkdir -p build/dist/$(tag) \
		&& if [ ! -f dist.tar.gz ]; then wget https://github.com/verbruggenalex/appointment/releases/download/$(tag)/dist.tar.gz; fi \
		&& tar -zxf dist.tar.gz -C build/dist/$(tag) \
		&& ln -sfn dist/$(tag)/ $${PWD}/build/pre-production \
		&& mkdir -p $${BACKUP_LOC} \
		&& drush @prod sql-dump --result-file=$${BACKUP_LOC}/production-$${BACKUP_TIME}.sql \
		&& ln -sfn $${BACKUP_LOC}/production-$${BACKUP_TIME}.sql $$(dirname $${BACKUP_LOC})/production-latest.sql \
		&& drush @pre-prod sql-drop -y \
		&& drush @pre-prod sql-create -y \
		&& drush @pre-prod sqlc < $${BACKUP_LOC}/production-$${BACKUP_TIME}.sql \
		&& rm -rf dist.tar.gz \
