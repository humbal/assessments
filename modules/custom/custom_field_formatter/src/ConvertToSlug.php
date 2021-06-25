<?php

namespace Drupal\custom_field_formatter;

use Cocur\Slugify\Slugify;
use Drupal\custom_field_formatter\ConvertToSlugInterface;

class ConvertToSlug implements ConvertToSlugInterface {
  /**
   * The slugify object.
   */
  protected $obj_slugify;

  public function __construct() {
    $this->obj_slugify = new Slugify();
  }

  /**
   * {@inheritdoc}
   */
  public function slugifyText($string, $separator) {
    return $this->obj_slugify->slugify($string, $separator);
  }
}