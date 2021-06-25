/**
 * @file
 * Loads the qTip2 library.
 */

(function ($) {
  'use strict'
  Drupal.behaviors.custom_field_formatter = {
    attach: function() {
      $('.text_field_tooltip[title]').qtip();
    }
  };
})(jQuery);