<?php

namespace Drupal\app_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'UserProfileBlock' block.
 *
 * @Block(
 *  id = "user_profile_block",
 *  admin_label = @Translation("User profile block"),
 * )
 */
class UserProfileBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    $build = [];
    $build['#theme'] = 'user_profile_block';
    $build['#displayname'] = $user->getDisplayName();
    $build['#email'] = $user->getEmail();
    $build['#logout_url'] = Url::fromRoute('user.logout');
    $build['#profile_url'] = Url::fromRoute('entity.user.edit_form', ['user' => $user->id()]);
    $build['#picture'] = !is_null($user->user_picture->entity) ? $user->user_picture->entity->createFileUrl() : NULL;

    return $build;
  }

}
