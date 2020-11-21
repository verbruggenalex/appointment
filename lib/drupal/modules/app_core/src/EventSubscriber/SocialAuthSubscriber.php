<?php

namespace Drupal\app_core\EventSubscriber;

use Drupal\social_auth\AuthManager\OAuth2ManagerInterface;
use Drupal\social_auth\Event\UserEvent;
use Drupal\social_auth\Event\SocialAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reacts on Social Auth events.
 */
class SocialAuthSubscriber implements EventSubscriberInterface {

  /**
   * The provider auth manager for Google.
   *
   * @var \Drupal\social_auth\AuthManager\OAuth2ManagerInterface
   */
  private $providerAuthGo;

  /**
   * The provider auth manager for Facebook.
   *
   * @var \Drupal\social_auth\AuthManager\OAuth2ManagerInterface
   */
  private $providerAuthFb;

  /**
   * SocialAuthSubscriber constructor.
   *
   *   Used to get an instance of the social auth implementer network plugin.
   *
   * @param \Drupal\social_auth\AuthManager\OAuth2ManagerInterface $providerAuthGo
   *   Used to get the Google provider auth manager.
   * @param \Drupal\social_auth\AuthManager\OAuth2ManagerInterface $providerAuthFb
   *   Used to get the Facebook provider auth manager.
   */
  public function __construct(
    OAuth2ManagerInterface $providerAuthGo,
    OAuth2ManagerInterface $providerAuthFb
  // Fifth.
  ) {

    $this->providerAuthGo = $providerAuthGo;
    $this->providerAuthFb = $providerAuthFb;
  }

  /**
   * {@inheritdoc}
   *
   * Returns an array of event names this subscriber wants to listen to.
   * For this case, we are going to subscribe for user creation and login
   * events and call the methods to react on these events.
   */
  public static function getSubscribedEvents() {
    $events[SocialAuthEvents::USER_CREATED] = ['onUserCreated'];

    return $events;
  }

  /**
   * Alters the user name if the user is being created by Social Auth.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The Social Auth user event object.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onUserCreated(UserEvent $event) {
    $newFirstName = $newLastName = FALSE;
    $user = $event->getUser();

    switch ($event->getPluginId()) {
      case 'social_auth_google':
        $info = $this->providerAuthGo->getUserInfo();
        $newFirstName = $info->getFirstName();
        $newLastName = $info->getLastName();
        break;

      case 'social_auth_facebook':
        $info = $this->providerAuthFb->getUserInfo();
        $newFirstName = $info->getFirstName();
        $newLastName = $info->getLastName();
        break;
    }

    $noFirstName = !$user->get('field_first_name')->getValue();
    $noLastName = !$user->get('field_last_name')->getValue();
    if ($noFirstName && $newFirstName) {
      $user->set('field_first_name', $newFirstName);
    }
    if ($noLastName && $newLastName) {
      $user->set('field_last_name', $newLastName);
    }
    $user->save();
  }

}
