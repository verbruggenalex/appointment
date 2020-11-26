# Notes

## Installing offline page through service worker.

### Download three files from [this repository](https://github.com/GoogleChrome/samples/tree/gh-pages/service-worker/custom-offline-page)

```bash
mkdir lib/offline
cd lib/offline
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/manifest.json
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/offline.html
wget https://raw.githubusercontent.com/GoogleChrome/samples/gh-pages/service-worker/custom-offline-page/service-worker.js
```

### Use drupal/core-composer-scaffold settings to place the files in the root directory.

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

### Get the Service Worker Registration module, enable and configure it.

```bash
composer require drupal/sw_register
drush en sw_register -y
drush php:eval "Drupal::configFactory()->getEditable('sw_register.settings')->set('service_worker_js_script_path', 'service-worker.js')->save(TRUE);"
drush cex
```
