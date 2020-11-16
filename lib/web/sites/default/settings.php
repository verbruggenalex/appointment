<?php

/**
 * @file
 * This is the settings file.
 */

$databases['default']['default'] = [
  'database' => 'appointment',
  'driver' => 'mysql',
  'host' => 'mysql',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'password' => '',
  'port' => 3306,
  'prefix' => '',
  'username' => 'root',
];

$settings['trusted_host_patterns'] = [
  '.*',
];

// Use development config in dev environments.
$config['config_split.config_split.config_dev']['status'] = (getenv('ENVIRONMENT') == 'development') ? TRUE : FALSE;

$settings['hash_salt'] = 'rFikc364CPkTMP22vy_PhbXU0HTq0U-jSmg5v22qpiKNNPnO3-6LQq4FG43GVFs4-L1vc1X1CA';

$settings["config_sync_directory"] = '../lib/web/sites/default/config/sync';

$config['locale.settings']['translation']['use_source'] = 'remote_and_local';

// Used because the reverse proxy is on https and apache is on http.
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = [$_SERVER['REMOTE_ADDR']];

// Use development services.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

// Disable css js aggregation.
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

// Disable caches.
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';

// Local SMTP settings.
$config['smtp.settings']['smtp_on'] = TRUE;
$config['smtp.settings']['smtp_host'] = 'smtp';
$config['smtp.settings']['smtp_username'] = '';
$config['smtp.settings']['smtp_password'] = '';


// Test for production, not needed I think?
$settings['reverse_proxy_trusted_headers'] = \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_ALL | \Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED;
