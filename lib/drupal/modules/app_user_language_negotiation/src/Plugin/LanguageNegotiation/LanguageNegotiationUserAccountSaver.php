<?php

namespace Drupal\app_user_language_negotiation\Plugin\LanguageNegotiation;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Url;
use Drupal\language\LanguageSwitcherInterface;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;
use Drupal\user\Entity\User;
use Drupal\user\Plugin\LanguageNegotiation\LanguageNegotiationUser;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class for identifying language via URL prefix or domain.
 *
 * @LanguageNegotiation(
 *   id = \Drupal\app_user_language_negotiation\Plugin\LanguageNegotiation\LanguageNegotiationUserAccountSaver::METHOD_ID,
 *   types = {\Drupal\Core\language\LanguageInterface::TYPE_INTERFACE,
 *   \Drupal\Core\language\LanguageInterface::TYPE_CONTENT,
 *   \Drupal\Core\language\LanguageInterface::TYPE_URL},
 *   name = @Translation("User account saver"),
 *   description = @Translation("Language from the user; saves in user when switching."),
 *   weight = 49
 * )
 */
class LanguageNegotiationUserAccountSaver extends LanguageNegotiationUser implements InboundPathProcessorInterface, LanguageSwitcherInterface {

  /**
   * The language negotiation method id.
   */
  const METHOD_ID = 'language-user-account-saver';

  /**
   * {@inheritdoc}
   */
  public function getLangcode(Request $request = NULL) {
    $langcode = NULL;

    if ($request && $this->languageManager) {
      $languages = $this->languageManager->getLanguages();
      $negotiation_config = \Drupal::config('language.negotiation');

      $request_path = urldecode(trim($request->getPathInfo(), '/'));
      $path_args = explode('/', $request_path);
      $prefix = array_shift($path_args);

      // Search prefix within added languages.
      foreach ($languages as $language) {
        $langcode = $language->getId();
        $expected_prefix = $negotiation_config->get('url.prefixes.' . $langcode);
        $query_lang = $request->query->all()['language'] ?? '';
        if (($expected_prefix == '' && $query_lang == $langcode) || $expected_prefix == $prefix) {
          $id = $this->currentUser->id();
          if ($id) {
            $user = User::load($id);
            $user->set('preferred_langcode', $langcode);
            $user->save();
          }
          else {
            $_SESSION['language-anon'] = $langcode;
          }

          // The URL contains a path prefix.
          return $langcode;
        }
      }
    }

    $langcode = $_SESSION['language-anon'] ?? NULL;
    if ($langcode && $this->currentUser->isAnonymous()) {
      return $langcode;
    }

    // No path prefix, so check user account instead:
    return parent::getLangcode($request);
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    $parts = explode('/', trim($path, '/'));
    $prefix = array_shift($parts);
    $negotiation_config = \Drupal::config('language.negotiation');

    // Search prefix within added languages.
    foreach ($this->languageManager->getLanguages() as $language) {
      if ($negotiation_config->get('url.prefixes.' . $language->getId()) == $prefix) {
        // Rebuild $path with the language removed.
        $path = '/' . implode('/', $parts);
        break;
      }
    }

    return $path;
  }

  /**
   * {@inheritdoc}
   */
  public function getLanguageSwitchLinks(Request $request, $type, Url $url) {
    $links = [];
    $query = $request->query->all();
    $negotiation_config = \Drupal::config('language.negotiation');

    foreach ($this->languageManager->getNativeLanguages() as $language) {
      // Add prefix of the language to switch to:
      $new_url = clone $url;
      $langcode = $language->getId();
      $prefix = $negotiation_config->get('url.prefixes.' . $langcode);
      unset($query['language']);
      if ($prefix && $negotiation_config->get('url.source') == LanguageNegotiationUrl::CONFIG_PATH_PREFIX)
        $new_url->setOption('prefix', $prefix . '/');
      else
        $query['language'] = $langcode;


      $links[$langcode] = [
        'url' => $new_url,
        'title' => t($language->getName(), [], ['langcode' => $langcode]),
        'language' => $language,
        'attributes' => ['class' => ['language-link']],
        'query' => $query,
      ];
    }

    return $links;
  }

}
