<?php

namespace Drupal\app_user_language_negotiation;

use Drupal\Component\Gettext\PoItem;
use Drupal\Core\Language\LanguageManager;
use Drupal\locale\PoDatabaseWriter;
use Drupal\app_user_language_negotiation\Plugin\LanguageNegotiation\LanguageNegotiationUserAccountSaver;

/**
 * Helper class with installation methods.
 */
class ModuleInstallHandler {

  const CONFIG_KEY = 'negotiation.language_interface.enabled';

  /**
   * Install function.
   */
  public function onInstall() {
    $this->translateLanguageNames();
  }

  /**
   * Uninstall function.
   */
  public function onUninstall() {
    $this->disableOurPluginIfNeeded();
  }

  /**
   * Translate language names.
   */
  private function translateLanguageNames(): void {
    $writer = new PoDatabaseWriter();
    $writer->setOptions(['overwrite_options' => []]);
    $item = new PoItem();
    foreach (LanguageManager::getStandardLanguageList() as $langcode => $long_versions) {
      if ($long_versions[0] == $long_versions[1]) {
        // No translation needed.
        continue;
      }

      $item->setSource($long_versions[0]);
      $item->setTranslation($long_versions[1]);

      $writer->setLangcode($langcode);
      $writer->writeItem($item);
    }
  }

  /**
   * Prevent exception for.
   *
   * "The "language-user-account-saver" plugin does not exist".
   */
  private function disableOurPluginIfNeeded(): void {
    $config_factory = \Drupal::configFactory();
    $config = $config_factory->getEditable('language.types');

    $enabled_plugins = $config->get(self::CONFIG_KEY);
    unset($enabled_plugins[LanguageNegotiationUserAccountSaver::METHOD_ID]);
    $config->set(self::CONFIG_KEY, $enabled_plugins);
    $config->save();
  }

}
