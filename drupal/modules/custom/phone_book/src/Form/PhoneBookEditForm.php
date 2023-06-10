<?php

namespace Drupal\phone_book\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\Core\Database\Database;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Implement the edit form.
 */

class PhoneBookEditForm extends FormBase {
  
  protected $error = false;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'phone_book_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $record = NULL): array {
    $conn = Database::getConnection();

    if(isset($record['id'])){
      $form['id'] = [
        '#type' => 'hidden',
        '#attributes' => array(
          'class' => ['txt-class'],
        ),
        '#default_value' => (isset($record['id'])) ? $record['id'] : '',
      ];
    }

    // Name
    $form['name'] = [
      '#type'     => 'textfield',
      '#placeholder' => 'Name',
      '#required' => true,
      '#attributes' => array(
        'class' => ['input-field', 'form-control'],
      ),
      '#default_value' =>'',
      '#prefix' => '<div id="div-name" class="form-group col-md-3">',
      '#suffix' => '<div id="div-name-message" class="error-msg"></div></div>',
      '#default_value' => (isset($record['name'])) ? $record['name'] : '',

      '#ajax' => [
        'callback' => '::__checkNameValidation',
        'effect' => 'fade',
        'event' => 'change',
      ],
    ];

    // Phone number
    $form['phone_number'] = [
      '#type'     => 'textfield',
      '#placeholder' => 'Phone Number',
      '#required' => true,
      '#maxlength' => 10,
      '#attributes' => array(
        'class' => ['input-field', 'form-control'],
      ),
      '#prefix' => '<div id="div-edit-phone" class="form-group col-md-3">',
      '#suffix' => '<div id="div-edit-phone-message" class="error-msg"></div></div>',
      '#default_value' => (isset($record['phone_number'])) ? $record['phone_number'] : '',
      '#ajax' => [
        'callback' => '::__checkPhoneNumberValidation',
        'effect' => 'fade',
        'event' => 'change',
      ],
    ];

    // Favorite
    $form['is_fav'] = [
      '#type'     => 'checkboxes',
      '#options' => ['yes' => $this->t('Favorite?')],
      '#default_value' => (isset($record['is_fav'])) ? [$record['is_fav']] : '',
      '#prefix' => '<div id="div-fav" class="col-md-3 checkbox-inline">',
      '#suffix' => '<div id="div-fav-message" class="error-msg"></div></div>',
    ];

    // Action
    $form['actions'] = [
      '#type' => 'button',
      '#value' => (isset($record['name'])) ? $this->t('Update') : $this->t('Save'),
      '#attributes' => array(
          'class' => ['btn', 'btn-default'],
      ),
      '#prefix' => '<div id="div-edit-submit" class="form-group col-md-2">',
      '#suffix' => '</div>',
      '#ajax' => [
        'callback' => '::__updatePhoneBookData',
      ],
    ];

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>'
    ];

    $form['#attributes']['class'][] = 'form-inline edit-form mt-4';

    $form['#prefix'] = '<div class="form-div-edit" id="form-div-edit">';
    $form['#suffix'] = '</div>';

    $render_array['#attached']['library'][] = 'phone_book/global_styles';

    return $form;
  }

  /**
   * Update Phone book
   * 
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function __updatePhoneBookData(array $form, FormStateInterface $form_state) {
    $conn = Database::getConnection();

    $field = $form_state->getValues();

    $fields["name"] = $field['name'];
    $fields["phone_number"] = $field['phone_number'];

    $fav = $field['is_fav'];
    $fields["is_fav"] = $fav['yes'];

    $response = new AjaxResponse();

    $checkPhoneNumber = $this->__checkPhoneNumber($field['phone_number']);
    
    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
        $response->addCommand(new ReplaceCommand('#form-div-edit', $form));
    }
    else {
      $conn = Database::getConnection();
      $field = $form_state->getValues();

      $fields["name"] = $field['name'];
      $fields["phone_number"] = $field['phone_number'];

      $conn->update('phone_book')
        ->fields($fields)->condition('id', $field['id'])->execute();

      $response->addCommand(new OpenModalDialogCommand("Success!", '<p class="success-msg">The record has been updated.</p>', ['width' => 800]));
      $render_array = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookTableForm','All');

      $response->addCommand(new HtmlCommand('.result_message','' ));
      $response->addCommand(new AppendCommand('.result-section', $render_array));
      $response->addCommand(new InvokeCommand('.pagination-link', 'removeClass', array('active')));
      $response->addCommand(new InvokeCommand('.pagination-link:first', 'addClass', array('active')));

    }

    return $response;
  }

  /**
   *  Validate Name 
   * 
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function __checkNameValidation(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $msg = '';
    $status = true;
    $field = $form_state->getValues();

    if(!empty($field['name'])) {
      $css = ['border' => '1px solid #CCCCCC'];
      $text_css = ['color' => 'inherit'];
      $response->addCommand(new CssCommand('#div-edit-name', $css));
      $response->addCommand(new CssCommand('#div-edit-name-message', $text_css));
      $response->addCommand(new HtmlCommand('#div-edit-name-message', ''));

      return $response;
    }
  }

  /**
   * Phone no validation - currently only is_numeric is checked.
   * 
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   * todo validate name no pattern using regex.
   */
  public function __checkPhoneNumberValidation(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $msg = '';
    $status = true;
    $field = $form_state->getValues();
    $this->error = false;

    $phone_number = $field['phone_number'];
    $checkPhoneNumber = $this->__checkPhoneNumber($phone_number);

    if(!is_numeric($phone_number)) {
      $status = false;
      $msg = 'Only numbers are allowed';
    } else if($checkPhoneNumber) {
      $status = false;
      $msg = 'Phone number has been already taken!';
    }

    if($status === false) {
      $this->error = true;
      $css = ['border' => '1px solid red'];
      $text_css = ['color' => 'red'];
      $response->addCommand(new CssCommand('#edit-phone-number', $css));
      $response->addCommand(new CssCommand('#div-edit-phone-message', $text_css));
      $response->addCommand(new HtmlCommand('#div-edit-phone-message', $msg));

      return $response;
    }

    $css = ['border' => '1px solid #CCCCCC'];
    $text_css = ['color' => 'inherit'];
    $response->addCommand(new CssCommand('#edit-phone-number', $css));
    $response->addCommand(new CssCommand('#div-edit-phone-message', $text_css));
    $response->addCommand(new HtmlCommand('#div-edit-phone-message', ''));

    return $response;
  }

  /**
   * Check phone number exists or not
   * 
   * @param $phoneNumber
   * @return bool
   */
  public function __checkPhoneNumber($phoneNumber) {
    $results = \Drupal::database()->select('phone_book', 'st');
    $results->fields('st');
    $results->condition('st.phone_number', $phoneNumber, '=');
    $res = $results->execute()->fetchAll();

    return (count($res) == 0) ? false : true;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

 /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    kint('testing');die;
  }
}
