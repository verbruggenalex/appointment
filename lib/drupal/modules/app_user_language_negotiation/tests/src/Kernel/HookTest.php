<?php

namespace Drupal\Tests\app_user_language_negotiation\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;

/**
 * Tests our hooks.
 *
 * @group app_user_language_negotiation
 */
class HookTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'language',
    'locale',
    'app_user_language_negotiation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('locale', [
      'locales_source',
      'locales_target',
      'locales_location',
    ]);
  }

  /**
   * Test our hook_install implementation.
   */
  public function testIfInstallHookImportsTranslationsOfLanguageNames() {
    ConfigurableLanguage::create(['id' => 'fi'])->save();

    require_once __DIR__ . '/../../../app_user_language_negotiation.install';
    app_user_language_negotiation_install();

    self::assertEquals('Suomi', t('Finnish', [], ['langcode' => 'fi']));
  }

}
