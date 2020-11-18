<?php

namespace Drupal\smart_date_selector\Plugin\FullcalendarViewProcessor;

use Drupal\fullcalendar_view\Plugin\FullcalendarViewProcessorBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Smart Date plugin.
 *
 * @FullcalendarViewProcessor(
 *   id = "fullcalendar_view_smart_date_selector",
 *   label = @Translation("Smart date selector processor"),
 *   field_types = {
 *     "smartdate"
 *   }
 * )
 */
class SmartDateSelectorProcessor extends FullcalendarViewProcessorBase {

  /**
   * Process retrieved values before being passed to Fullcalendar.
   *
   * Processing view results of fullcalendar_view for a smart date field.
   *
   * this is not to be supported by smart date anyway
   */
  public function process(array &$variables) {
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $variables['view'];
    $view_index = key($variables['#attached']['drupalSettings']['fullCalendarView']);

    $fields = $view->field;
    $options = $view->style_plugin->options;
    $start_field = $options['start'];
    $start_field_options = $fields[$start_field]->options;
    // If not a Smart Date field or not existing config, nothing to do.
    if (strpos($start_field_options['type'], 'smartdate') !== 0 || empty($variables['#attached']['drupalSettings']['fullCalendarView'][$view_index]['calendar_options'])) {
      return;
    }

    // Get some resource data to set calendar settings.
    $currentUserId = \Drupal::currentUser()->id();
    $resourceNid = isset($view->argument['field_resource_target_id']->value[0]) ? $view->argument['field_resource_target_id']->value[0] : NULL;
    $resource = \Drupal::entityTypeManager()->getStorage('node')->load($resourceNid);
    $officeHours = $resource->get('field_office_hours')->getValue();
    $slotDuration = $resource->get('field_slot_duration')->getValue() ? $resource->get('field_slot_duration')->getValue()[0]['value'] : 30;
    // Get start and end hours.
    $startHour = DrupalDateTime::createFromFormat('Hi', sprintf('%04d', min(array_column($officeHours, 'starthours'))));
    $endHour = DrupalDateTime::createFromFormat('Hi', sprintf('%04d', max(array_column($officeHours, 'endhours'))));
    $validRange = new \StdClass();
    $validRange->start = \Drupal::service('date.formatter')->format(time(), 'custom', 'Y-m-d');
    // @todo set end range per license type.
    // $validRange->end = \Drupal::service('date.formatter')
    // ->format((time() + 691200), 'custom', 'Y-m-d');
    $calendar_options = json_decode($variables['#attached']['drupalSettings']['fullCalendarView'][$view_index]['calendar_options'], TRUE);

    foreach ($calendar_options['events'] as $key => &$event) {
      if (($entity = $variables['rows'][$key]->_entity) && $currentUserId === $entity->getOwnerId()) {
        // Change color for current user events to green.
        $event['title'] = t('We are expecting you!');
        $event['backgroundColor'] = '#C9F7F5';
      }
    }

    $calendar_options['allDaySlot'] = FALSE;
    $calendar_options['slotDuration'] = '00:' . $slotDuration . ':00';
    $calendar_options['validRange'] = $validRange;
    $calendar_options['height'] = 'auto';
    $calendar_options['contentHeight'] = 'auto';
    // @todo fetch mintime maxtime from resource.
    $calendar_options['minTime'] = $startHour->format('H:i:s');
    $calendar_options['maxTime'] = $endHour->format('H:i:s');
    // // @todo fetch office hours from resource.
    $businessHours = [];
    foreach ($officeHours as $hour) {
      $fromTo = $hour['starthours'] . '-' . $hour['endhours'];
      if (!isset($businessHours[$fromTo])) {
        $businessHours[$fromTo] = new \StdClass();
        $businessHours[$fromTo]->startTime = DrupalDateTime::createFromFormat('Hi', sprintf('%04d', $hour['starthours']))->format('H:i');
        $businessHours[$fromTo]->endTime = DrupalDateTime::createFromFormat('Hi', sprintf('%04d', $hour['endhours']))->format('H:i');
        $businessHours[$fromTo]->daysOfWeek[] = $hour['day'];
      }
      else {
        $businessHours[$fromTo]->daysOfWeek[] = $hour['day'];
      }
    }
    $calendar_options['businessHours'] = array_values($businessHours);

    $variables['#attached']['drupalSettings']['fullCalendarView'][$view_index]['calendar_options'] = json_encode($calendar_options);
  }

}
