<?php

namespace Drupal\smart_date_selector\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'UserButtonBlock' block.
 *
 * @Block(
 *  id = "user_button_block",
 *  admin_label = @Translation("User button block"),
 * )
 */
class UserButtonBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
    $build = [];
    $build['#theme'] = 'user_button_block';
    $build['#login_url'] = Url::fromRoute('user.login');
    if ($user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id())) {
      $build['#firstname'] = $user->field_first_name->value;
      $build['#picture'] = !is_null($user->user_picture->entity) ? $user->user_picture->entity->getFileUri() : NULL;
    }
    return $build;
  }

}
