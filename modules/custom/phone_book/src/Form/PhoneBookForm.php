<?php

namespace Drupal\phone_book\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\AppendCommand;

use Drupal\Core\Database\Database;
use Drupal\Core\Ajax\InvokeCommand;

class PhoneBookForm extends FormBase {
  protected $error = false;
  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
      return 'phone_book_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Name
    $form['name'] = [
      '#type'     => 'textfield',
      '#placeholder' => 'Name',
      '#required' => true,
      '#attributes' => array(
          'class' => ['input-field', 'form-control'],
      ),
      '#default_value' =>'',
      '#prefix' => '<div id="div-name" class="form-group col-md-4">',
      '#suffix' => '<div id="div-name-message" class="error-msg"></div></div>',

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
      '#prefix' => '<div id="div-phone" class="form-group col-md-4">',
      '#suffix' => '<div id="div-phone-message" class="error-msg"></div></div>',
      '#ajax' => [
        'callback' => '::__checkPhoneNumberValidation',
        'effect' => 'fade',
        'event' => 'change',
      ],
    ];

    $form['is_fav'] = [
      '#type'     => 'checkboxes',
      '#options' => ['yes' => $this->t('Favorite?')],
      '#prefix' => '<div id="div-fav" class="col-md-2 checkbox-inline">',
      '#suffix' => '<div id="div-fav-message" class="error-msg"></div></div>',
    ];

    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Add'),
      '#attributes' => array(
          'class' => ['btn', 'btn-default'],
      ),
      '#ajax' => [
          'callback' => '::__submitPhoneBookForm',
      ],
    ];

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>'
    ];

    $form['#attributes']['class'][] = 'form-inline';

    $render_array['#attached']['library'][] = 'phone_book/global_styles';

    return $form;
  }

  /**
   * Submit Phone book
   * 
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function __submitPhoneBookForm(array $form, FormStateInterface $form_state) {
      $conn = Database::getConnection();

      $field = $form_state->getValues();

      $fav = $field['is_fav'];

      $fields["name"] = $field['name'];
      $fields["phone_number"] = $field['phone_number'];
      $fields["is_fav"] = $fav['yes'];

      $response = new AjaxResponse();

      $checkPhoneNumber = $this->__checkPhoneNumber($field['phone_number']);

      //todo refactor this below code block.
      //========Field value validation
      if($fields["name"] == ''){
        $css = ['border' => '1px solid red'];
        $text_css = ['color' => 'red'];
        $message = ('Field cannot be empty.');

        //$response = new \Drupal\Core\Ajax\AjaxResponse();
        $response->addCommand(new CssCommand('#edit-name', $css));
        $response->addCommand(new CssCommand('#div-name-message', $text_css));
        $response->addCommand(new HtmlCommand('#div-name-message', $message));

        return $response;
      } else if($fields["phone_number"] == '') {
        $css = ['border' => '1px solid red'];
        $text_css = ['color' => 'red'];
        $message = ('Field cannot be empty.');

        //$response = new \Drupal\Core\Ajax\AjaxResponse();
        $response->addCommand(new CssCommand('#edit-phone-number', $css));
        $response->addCommand(new CssCommand('#div-phone-message', $text_css));
        $response->addCommand(new HtmlCommand('#div-phone-message', $message));

        return $response;
      } else if($checkPhoneNumber) {
        $css = ['border' => '1px solid red'];
        $text_css = ['color' => 'red'];
        $msg = 'Phone number has been already taken!';
        $response->addCommand(new CssCommand('#edit-phone-number', $css));
        $response->addCommand(new CssCommand('#div-phone-message', $text_css));
        $response->addCommand(new HtmlCommand('#div-phone-message', $msg));

        return $response;

      } else if($this->error) {
          //todo notify in general message section.
      } else {
        $conn->insert('phone_book')
          ->fields($fields)->execute();

        $dialogText['#attached']['library'][] = 'core/drupal.dialog.ajax';
        $render_array = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookTableForm', 'All');
        $response->addCommand(new HtmlCommand('.result_message', ''));
        $response->addCommand(new AppendCommand('.result-section', $render_array));
        $response->addCommand(new InvokeCommand('.txt-class', 'val', ['']));

        return $response;
      }
  }

  /**
   * Validate Name 
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
      $response->addCommand(new CssCommand('#edit-name', $css));
      $response->addCommand(new CssCommand('#div-name-message', $text_css));
      $response->addCommand(new HtmlCommand('#div-name-message', ''));

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
      $response->addCommand(new CssCommand('#div-phone-message', $text_css));
      $response->addCommand(new HtmlCommand('#div-phone-message', $msg));

      return $response;
    }

    $css = ['border' => '1px solid #CCCCCC'];
    $text_css = ['color' => 'inherit'];
    $response->addCommand(new CssCommand('#edit-phone-number', $css));
    $response->addCommand(new CssCommand('#div-phone-message', $text_css));
    $response->addCommand(new HtmlCommand('#div-phone-message', ''));

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
    $results->condition('st.phone_number',$phoneNumber, '=');
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
