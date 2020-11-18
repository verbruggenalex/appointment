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

* For sure lower the Googel Maps API callback count!
 * Cache?
 * Image replacement?
 * Disable geocoding on node save?
* Put traefik inside of this project docker-compose.yml?
* Keep traefik in seperate docker-compose.yml or repo?
* Fix breadcrumb and url alias for resource pages.
* Change appointment creation message to be more user friendly.
* Try to keep the same date on form submit (not going to be easy).
* Create licensing options
 * Free: 1 week appointments - 3 months license.
 * Basic: 1 month appointments - 1 year license - Pay 19 euro per month = 228 euro
 * Pro: 6 months appointments - 1 year license - Pay 29 euro per month = 348 euro
* Calculate percentage booked to show on month calendar on days?
* Implement CI
* Implement lighthouse CI.
* See if we need to provide an ical to provide a feed for users appointments:
https://www.drupal.org/project/views_ical
