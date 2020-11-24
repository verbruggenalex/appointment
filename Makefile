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
