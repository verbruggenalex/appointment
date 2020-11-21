<?php

namespace Drupal\app_core\EventSubscriber;

use Drupal\commerce_order\Event\OrderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event to change order status when it's paid.
 */
class OrderPaidSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.order.paid' => 'onPaid',
    ];
    return $events;
  }

  /**
   * Completes the order after it has been fully paid through manual payment.
   *
   * @param \Drupal\commerce_order\Event\OrderEvent $event
   *   The event.
   */
  public function onPaid(OrderEvent $event) {
    $order = $event->getOrder();
    if ($order->getState()->getId() == 'pending') {
      $order->getState()->applyTransitionById('validate');
    }
  }

}
