<?php

namespace Drupal\app_user_language_negotiation\Plugin\LanguageNegotiation;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Url;
use Drupal\language\LanguageSwitcherInterface;
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
    return $path;
  }

  /**
   * {@inheritdoc}
   */
  public function getLanguageSwitchLinks(Request $request, $type, Url $url) {
    $links = [];
    $new_url = clone $url;

    foreach ($this->languageManager->getNativeLanguages() as $language) {
      // Add prefix of the language to switch to:
      $langcode = $language->getId();
      $query['destination'] = $new_url->toString();

      $links[$langcode] = [
        'url' => $url = Url::fromUri('internal:/language/redirect/' . $langcode),
        'title' => $language->getName(),
        'language' => NULL,
        'attributes' => ['class' => ['language-link']],
        'query' => $query,
      ];
    }

    return $links;
  }

}
