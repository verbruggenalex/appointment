<?php

namespace Drupal\app_core\Plugin\Block;

use Drupal\user\Form\UserLoginForm;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'AddNodeAppointmentFormBlock' block.
 *
 * @Block(
 *  id = "add_node_appointment_form_block",
 *  admin_label = @Translation("Add node appointment form block"),
 * )
 */
class AddNodeAppointmentFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityFormBuilderInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityFormBuilder = $container->get('entity.form_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    if (!\Drupal::currentUser()->id()) {
      $form = \Drupal::formBuilder()->getForm(UserLoginForm::class);
      $build['form'] = $form;
    }
    else {
      // Get the current entity of the page.
      $currentEntity = $this->getRouteEntity();
      $entity_type = $currentEntity ? $currentEntity->getEntityTypeId() : NULL;
      $entity_bundle = $currentEntity ? $currentEntity->bundle() : NULL;

      // If the entity is a resource prepare the appointment form.
      if ($currentEntity && $entity_type == 'node' && $entity_bundle == 'resource') {
        // Create a dummy appointment for the form creation.
        $appointmentNode = $this->entityTypeManager->getStorage('node')->create(['type' => 'appointment']);
        $appointmentNode->field_resource->target_id = $currentEntity->id();
        $form = $this->entityFormBuilder->getForm($appointmentNode, 'default');
        // Disable stuff we don't want.
        $form['advanced']['#access'] = FALSE;
        $form['status']['#access'] = FALSE;
        $form['field_resource']['#access'] = FALSE;
        // @todo We probably want to redirect the form after submission to a
        // more sensible overview page, instead of going to the appointment
        // node.
        $build['form'] = $form;
      }
    }

    return $build;

  }

  /**
   * Private helper function to extract the entity for the supplied route.
   *
   * @return null|\Drupal\Core\Entity\ContentEntityInterface
   *   Returns the entity or null.
   */
  private function getRouteEntity() {
    $route_match = \Drupal::routeMatch();
    // Entity will be found in the route parameters.
    if (($route = $route_match->getRouteObject()) && ($parameters = $route->getOption('parameters'))) {
      // Determine if the current route represents an entity.
      foreach ($parameters as $name => $options) {
        if (isset($options['type']) && strpos($options['type'], 'entity:') === 0) {
          $entity = $route_match->getParameter($name);
          if ($entity instanceof ContentEntityInterface && $entity->hasLinkTemplate('canonical')) {
            return $entity;
          }

          // Since entity was found, no need to iterate further.
          return NULL;
        }
      }
    }
  }

}
