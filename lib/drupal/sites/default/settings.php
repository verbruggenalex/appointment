<?php

/**
 * @file
 * This is the settings file.
 */

$dbName = preg_match('/\/build\/(dist|dev)\/((?:[0-9]+\.?)+)\//i', __DIR__, $matches) ? $matches[1] . '_' . str_replace('.', '_', $matches[2]) : 'appointment';
$domain = getenv('TRAEFIK_DOMAIN');
$environment = getenv('ENVIRONMENT');
$isProductionEnvironment = $environment === 'prod';
$isDevelopmentEnvironment = $environment === 'dev';
$hasDevelommentModule = file_exists(DRUPAL_ROOT . '/modules/contrib/devel/devel.info.yml');
$notAbsoluteProduction = strpos($_SERVER['DOCUMENT_ROOT'], '/production/') === FALSE && !$isProductionEnvironment;
$isAbsoluteDevelopment = ($isDevelopmentEnvironment && $hasDevelommentModule) ? TRUE : FALSE;

$databases['default']['default'] = [
  'database' => $dbName,
  'driver' => 'mysql',
  'host' => 'mysql',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'password' => '',
  'port' => 3306,
  'prefix' => '',
  'username' => 'root',
];

// With Traefik setup we always limit to the exact root domain.
$settings['trusted_host_patterns'] = [$domain ? '.*' . str_replace('.', '\.', $domain) . '$' : '.*'];

// Use the local translations for anything other than absolute development.
$config['locale.settings']['translation']['use_source'] = 'local';
$config['locale.settings']['translation']['path'] = '../lib/drupal/translations';

// Never send out emails directly from any environment other than an absolute
// production environment.
$config['mail_safety.settings']['enabled'] = $notAbsoluteProduction;
$config['mail_safety.settings']['send_mail_to_dashboard'] = $notAbsoluteProduction;

// Disable caching only for an absolute development environment.
$config['config_split.config_split.config_dev']['status'] = $isAbsoluteDevelopment;
if ($config['config_split.config_split.config_dev']['status']) {

  // Use development services.
  $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

  // Disable css js aggregation.
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;
  $config['system.performance']['cache']['page']['max_age'] = 0;

  // Disable caches.
  $settings['cache']['bins']['render'] = 'cache.backend.null';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
  $settings['cache']['bins']['page'] = 'cache.backend.null';

  // Translations local and remote.
  $config['locale.settings']['translation']['use_source'] = 'remote_and_local';
}

$settings['hash_salt'] = 'rFikc364CPkTMP22vy_PhbXU0HTq0U-jSmg5v22qpiKNNPnO3-6LQq4FG43GVFs4-L1vc1X1CA';

$settings["config_sync_directory"] = '../lib/drupal/config/sync';

// Used because the reverse proxy is on https and apache is on http.
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = [$_SERVER['REMOTE_ADDR']];

// phpcs:ignore
$settings['reverse_proxy_trusted_headers'] = \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_ALL | \Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED;
