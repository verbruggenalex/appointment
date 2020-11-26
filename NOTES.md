# Notes

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
            "file-mapping": {
                "[web-root]/manifest.json": "lib/offline/manifest.json",
                "[web-root]/offline.html": "lib/offline/offline.html",
                "[web-root]/service-worker.js": "lib/offline/service-worker.js"
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
drush cex
```
</details>

<details>
 <summary>Show manifest.json example</summary>

More information on the manifest can be found here: https://w3c.github.io/manifest/


```json
{
  "name": "Custom Offline Page",
  "short_name": "Offline Page",
  "icons": [{
    "src": "images/launcher-icon-2x.png",
    "sizes": "96x96"
  }, {
    "src": "images/launcher-icon-4x.png",
    "sizes": "192x192"
  }],
  "start_url": "/",
  "display": "standalone",
  "orientation": "portrait"
}
```
</details>
