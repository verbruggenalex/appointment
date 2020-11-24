# Appointment project

## Development setup.

```bash
# Make sure your environment has all needed variables.
[ ! -f .env ] && cp .env.dist .env
# Edit the file.
nano .env
# Setup your development environment.
docker-compose up -d
# Install codebase.
composer install
# Install a clean Drupal site from config.
composer drupal-instal-clean
```

## Build distribution.
```bash
# Compresses to dist.tar.gz
composer build-dist
```

## Todo's

* Provide the first real behat tests.
* Fix profile picture!!!!!!
* Start cleaning up user sidebar.
* Provide business picture
* Contact page form.
* Put traefik inside of this project docker-compose.yml?
* Keep traefik in seperate docker-compose.yml or repo?
* Fix breadcrumb.
* Change appointment creation message to be more user friendly.
* Create licensing options
* Calculate percentage booked to show on month calendar on days?
* Implement lighthouse CI.
* See if we need to provide an ical to provide a feed for users appointments:
https://www.drupal.org/project/views_ical
