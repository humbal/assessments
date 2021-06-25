<?php

namespace Drupal\Tests\custom_field_formatter\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Kernel testing of the SlugFormatter service.
 * @coversDefaultClass \Drupal\custom_field_formatter\SlugFormatter
 */
class SlugFormatterTest extends KernelTestBase {
  
  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['custom_field_formatter'];

  /**
   * {@inheritdoc}
   *
   * Kernel tests do not invoke hook_schema() or hook_install(). Therefore we
   * have to do it if our tests expect them to have been run.
   */
  protected function setUp() {
    parent::setUp();
    
    // Install the schema we defined in hook_schema().
    $this->installSchema('custom_field_formatter', 'custom_field_formatter');
    
    // Inovke hook_install().
    $this->container->get('module_handler')->invoke('custom_field_formatter', 'install');
  }

  /**
   * Tests for string Slug
   */
  public function testTextSlugify() {
    /* @var $convertSlug \Drupal\custom_field_formatter\SlugFormatter */
    $convertSlug = $this->container->get('custom_field_formatter.slug');

    $text = 'Drupal Test';
    $separator = '_';
    $out_put = 'Drupal_Test';
    // slugifyText
    $checkResult = $convertSlug->slugifyText($text, $separator);
    $this->assertEquals($checkResult, $out_put, 'Result success');
  }
}