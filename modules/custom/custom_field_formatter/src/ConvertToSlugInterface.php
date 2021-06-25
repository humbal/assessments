<?php

namespace Drupal\custom_field_formatter;

/**
 * An interface for Slug.
 */
interface ConvertToSlugInterface {
  /**
   * Convert a text into slug.
   * 
   * @param string $string
   * @param string $separator
   * @return string
   *  The slug text.
   */
  public function slugifyText($string, $separator);
}