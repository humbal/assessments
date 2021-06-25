<?php

/**
 * @file
 * Contains \Drupal\custom_field_formatter\Plugin\Field\FieldFormatter.
 */

namespace Drupal\custom_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Rot13Coding' formatter.
 *
 * @FieldFormatter(
 *   id = "rot13coding",
 *   label = @Translation("ROT13 coding"),
 *   field_types = {
 *     "string", "string_long", "text_long", "text"
 *   }
 * )
 */
class Rot13CodingFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => formatTextToRot13($items->value),
      ];
    }
    
    return $element;
  }
}