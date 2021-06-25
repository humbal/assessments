
custom_field_formatter
----------------------

This custom module defines field formatters that can only be used on text fields.
There will be three types of custom field formatter:

1. Rot13​ - The text field value should be printed with the ROT13 encoding (​https://en.wikipedia.org/wiki/ROT13​). For example, “Lorem Ipsum” would become “Yberz Vcfhz”. You must do this without using the​ ​str_rot13()​ ​ function.
2. Slugify​ - The text field value will be converted into a slug. A service (​https://www.drupal.org/node/2133171​) must be created using the ​cocur/slugify​ package. The user should be able to specify the separator in the field formatter settings form. The settings summary should display the separator that has been entered.
3. Tooltip ​- Show a tooltip on hover of the outputted text. A JavaScript library such as qTip2 (​https://github.com/qTip2/qTip2​) should be used. The JavaScript library and any custom JavaScript should be added to the site only if the field formatter is being used. A custom twig template must be used to format the HTML correctly. The text shown in the tooltip’s tip can be hardcoded to whatever you want.


How To Use
----------

After enabling the module, you can choose any Content type, for instance 'Article':
 1. Go to structure > Content types > Article > Manage field.
 2. Add 3 different fields wity type 'Text(Plain)'
 3. Then click on 'Manage display' tab and under 'Format', you can select desire custom fields.

Referances
----------
Drupal.org (https://www.drupal.org/docs/creating-custom-modules/creating-custom-field-types-widgets-and-formatters/create-a-custom)
​https://en.wikipedia.org/wiki/ROT13
https://github.com/qTip2/qTip2​
