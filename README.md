# Open Appointment

## Preparing the host environment.

### Requirements

Execute the following commands on a linux environment. You can/should change the
environment variables according to your needs.

```bash
# Install docker:
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker <your-user>
# Install docker-compose:
sudo curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
# Add required environment variables:
echo "export TRAEFIK_DOMAIN=localhost.com" >> ~/.bashrc
echo "export TRAEFIK_NETWORK=proxy" >> ~/.bashrc
echo "export TRAEFIK_BASIC_AUTH=$(htpasswd -nb admin admin)" >> ~/.bashrc
# Source .bashrc file.
. ~/.bashrc
```

### Makefile

All functions needed to set up the environment are to be placed in the
[Makefile](Makefile) that is found in the root of this project. Execute the
following command after you have met the requirements:

```bash
# Installs a locally trusted certificate and sets up network and hosts.
make mkcert_setup mkcert_install docker_network_create setup_hosts example_build
```


## Starting the project environment.

### Development.

```bash
# Starts all required services for a development environment.
make dev
```

### Continuous integration.

```bash
# Starts all required services for a ci environment.
make ci
```

### Production.

```bash
# Starts all required services for a production environment.
make prod
```
