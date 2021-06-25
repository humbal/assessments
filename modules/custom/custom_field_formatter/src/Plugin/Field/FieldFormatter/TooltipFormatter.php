<?php

namespace Drupal\custom_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tooltip' formatter.
 *
 * @FieldFormatter(
 *   id = "tooltip",
 *   label = @Translation("Tooltip"),
 *   field_types = {
 *     "string", "string_long", "text_long", "text"
 *   }
 * )
 */
class TooltipFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Tooltip formatter.');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = [
        '#theme' => 'field_text_tooltip',
        '#text' => $item->value,
        '#tooltip' => $this->getSetting('tooltip'),
        '#attached' => [
          'library' => [
            'custom_field_formatter/text.tooltip',
          ],
        ],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'tooltip' => t('This is default toltip text.'),
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['tooltip'] = [
      '#type' => 'textfield',
      '#title' => t('Specify the tooltip for the text on hover'),
      '#size' => 20,
      '#required' => TRUE,
      '#default_value' => $this->getSetting('tooltip'),
     ];

     return $elements;
  }
}
