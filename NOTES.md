# Notes

## Deployment

These notes are to keep track of the sequence we use to deploy new releases of
our project.

<details>
 <summary>Deployment sequence</summary>

```bash
# All commands below are executed within the environment
docker-compose exec prod bash

# Create release folder
mkdir -p build/dist/1.0.x
# @TODO: Download pre-production codebase
wget https://github.com/verbruggenalex/appointment/releases/download/1.0.x/dist.tar.gz
# Extract pre-production codebase
tar -zxf dist.tar.gz build/dist/1.0.x
# Symlink pre-production codebase
ln -sfn dist/1.0.x/ build/pre-production

# @TODO: Make website readonly?
drush @prod something
# Dump production database (caches might not be needed?)
drush @prod sql-dump --result-file=../../../pre-production/dump.sql
# Make sure we have a new database ready.
drush @pre-prod sql-create -y
# Import the production database into pre-production.
drush @pre-prod sqlc < build/pre-production/dump.sql

# Deployment sequence
drush @pre-prod cache:rebuild
drush @pre-prod updatedb -y --no-post-updates
drush @pre-prod config:import -y
drush @pre-prod updatedb -y --post-updates
drush @pre-prod cache:rebuild
drush @pre-prod status

# Change symlinks of environments (if pre-production got accepted)
ln -sfn dist/1.0.x/ build/production
ln -sfn dist/1.0.(x-1)/ build/post-production
# Remove symlink to older environment
rm build/pre-production
# @TODO: Maybe perform some cleanup actions so we dont get clutter on the
# environments.
```
</details>

## Drupal core

### Stop requiring drupal/core!

Because drupal/core is a subtree split you will not be sure that all the
dependencies will be of the version that you tested by the time your project
reaches production. Instead use drupal/core-recommended. This will ensure you
have the fixed versions from the release.

 * [Documentation](https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies)
 * [Vendor hardening in drupal/core-recommended](https://github.com/drupal/core-recommended/blob/8.9.10//composer.json#L4)
 * [Vendor hardening in drupal/core](https://github.com/drupal/drupal/blob/8.9.10/composer.json#L15)

## Drupal caching

### Max age bubbling

See: https://www.drupal.org/project/drupal/issues/2352009

I'm just taking a note of this to see if this module could be a candidate to
solve some of the caching issues.

```bash
composer require drupal/cache_control_override
drush en cache_control_override
```

## Lighthouse performance

### Installing offline page through service worker.


<details>
 <summary>Download three files from <a href="https://github.com/GoogleChrome/samples/tree/gh-pages/service-worker/custom-offline-page">this repository</a></summary>

```bash
mkdir lib/offline
cd lib/offline
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/manifest.json
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/offline.html
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/service-worker.js
```
</details>

<details>
 <summary>Use drupal/core-composer-scaffold settings to place the files in the root directory.</summary>

```json
    "extra": {
        "drupal-scaffold": {
file-mapping": {
    [web-root]/manifest.json": "lib/offline/manifest.json",
    [web-root]/offline.html": "lib/offline/offline.html",
    [web-root]/service-worker.js": "lib/offline/service-worker.js"
            }
        }
    }
```
</details>

<details>
 <summary>Get the Service Worker Registration module, enable and configure it.</summary>

```bash
composer require drupal/sw_register
drush en sw_register -y
drush php:eval "Drupal::configFactory()->getEditable('sw_register.settings')->set('service_worker_js_script_path', 'service-worker.js')->save(TRUE);"
drush cex
```
</details>


### Set app start_url in manifest.json.

<details>
 <summary>Information on what the start_url is exactly.</summary>
<p>The start_url is required and tells the browser where your application should
start when it is launched, and prevents the app from starting on whatever page
the user was on when they added your app to their home screen. Your start_url
should direct the user straight into your app, rather than a product landing
page. Think about what the user will want to do once they open your app, and
place them there.</p>
</details>

<details>
 <summary>Use metatag module to define the mobile manifest.json</summary>

We will use https://www.drupal.org/project/metatag to set the tag on the page.

Important: core might be including this functionality.
See: https://www.drupal.org/project/drupal/issues/2698127

```bash
composer require drupal/metatag
drush en metatag_mobile -y
drush php:eval "Drupal::configFactory()->getEditable('metatag.metatag_defaults.global')->set('tags.web_manifest', 'manifest.json')->save(TRUE);"
drush php:eval "Drupal::configFactory()->getEditable('metatag.metatag_defaults.global')->set('tags.theme_color', '#FFFFFF')->save(TRUE);"
drush cex
```
</details>

<details>
 <summary>Show manifest.json example</summary>

More information on the manifest can be found here: https://w3c.github.io/manifest/


```json
{
  "lang": "en",
  "dir": "ltr",
  "name": "Open Appointment",
  "short_name": "Open Appoint",
  "icons": [
    {
      "src": "themes/custom/bbc/icon-128.png",
      "sizes": "128x128"
    }
  ],
  "start_url": "/",
  "display": "browser",
  "orientation": "portrait",
  "theme_color": "#ffffff",
  "background_color": "#ffffff"
}
```
</details>
