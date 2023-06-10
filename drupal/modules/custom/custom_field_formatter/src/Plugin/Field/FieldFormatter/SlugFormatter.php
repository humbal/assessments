<?php

namespace Drupal\custom_field_formatter\Plugin\Field\FieldFormatter;

use Drupal;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\custom_field_formatter\ConvertToSlugInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'slugify' formatter.
 *
 * @FieldFormatter(
 *   id = "slugify",
 *   label = @Translation("Slugify"),
 *   field_types = {
 *     "string", "string_long", "text_long", "text"
 *   }
 * )
 */
class SlugFormatter extends FormatterBase {

  /**
   * The Slug service
   *
   * @var \Drupal\custom_field_formatter\ConvertToSlugInterface
   */
  protected $convertSlug;

  /**
   * Construct a Slugify object.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ConvertToSlugInterface $convertSlug) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    
    $this->convertSlug = $convertSlug;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('custom_field_formatter.slug')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $separator = $this->getSetting('separator');
    $summary[] = $this->t('The separator is: ' . $separator);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $separator = $this->getSetting('separator');

    foreach ($items as $delta => $item) {
      // Convert the text into a slug.
      //$converted_text = \Drupal::service('custom_field_formatter.slug')->slugifyText($item->value, $separator);
      $converted_text = $this->convertSlug->slugifyText($item->value, $separator);

      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $converted_text,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'separator' => '_',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Separator'),
      '#size' => 10,
      '#required' => TRUE,
      '#default_value' => $this->getSetting('separator'),
     ];

     return $elements;
  }
}
