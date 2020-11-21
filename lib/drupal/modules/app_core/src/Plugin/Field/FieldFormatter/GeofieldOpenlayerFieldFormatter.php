<?php

namespace Drupal\app_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geofield_openlayer_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "geofield_openlayer_field_formatter",
 *   label = @Translation("Geofield openlayer field formatter"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldOpenlayerFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    $elements['library'] = [
      '#attached' => [
        'library' => [
          'app_core/openlayers',
        ],
      ],
    ];

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $values = $item->getValue();
    return '<div id="map" data-lat="' . $values['lat'] . '" data-lon="' . $values['lon'] . '" class="openlayers-map"></div>';
  }

}
