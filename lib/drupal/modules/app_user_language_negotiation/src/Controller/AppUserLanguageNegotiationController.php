<?php

namespace Drupal\app_user_language_negotiation\Controller;

use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides redirect path to change language.
 */
class AppUserLanguageNegotiationController extends ControllerBase {

  /**
   * Switch language and redirect to previous page.
   */
  public function languageRedirect($langcode) {
    $destination = Url::fromUserInput(\Drupal::destination()->get());
    if ($destination->isRouted()) {
      $id = $this->currentUser()->id();
      if ($id) {
        $user = User::load($id);
        $user->set('preferred_langcode', $langcode);
        $user->save();
      }
      else {
        $_SESSION['language-anon'] = $langcode;
      }
      // Valid internal path.
      return $this->redirect($destination->getRouteName(), $destination->getRouteParameters());
    }
  }

}
