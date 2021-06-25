<?php

namespace Drupal\phone_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides the list of Students.
 */
class PhoneBookTableForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'phone_book_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$pageNo = NULL) {

    //$pageNo = 2;
    if($pageNo != '') {
      $form['table'] = [
        '#type' => 'table',
        '#rows' => $this->get_phone_book_list($pageNo),
        '#empty' => $this->t('No record found'),
        '#attributes' => array(
          'class' => ['result-table'],
        ),
      ];
    }else{
      $form['table'] = [
        '#type' => 'table',
        '#rows' => $this->get_phone_book_list("All"),
        '#empty' => $this->t('No records found'),
        '#attributes' => array(
          'class' => ['result-table'],
        ),
      ];
    }
    
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'phone_book/global_styles';

    $form['#prefix'] = '<div class="result_message mt-4">';
    $form['#suffix'] = '</div>';
    $form['#cache'] = [
      'max-age' => 0
    ];

    $form['#attributes']['class'][] = 'result-form';

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {

  }

  /**
   * Get Phone book list
   * 
   * @param $opt
   * @return array
   */
  function get_phone_book_list($opt) {
    $res = [];
    //$opt = 2;
    if($opt == "All") {
      $results = \Drupal::database()->select('phone_book', 'st');
      $results->fields('st');
      $results->range(0, 15);
      $results->orderBy('st.is_fav','DESC');
      $results->orderBy('st.id','DESC');
      $res = $results->execute()->fetchAll();
      $result = [];
    }else{
      $query = \Drupal::database()->select('phone_book', 'st');

      $query->fields('st');
      $query->range($opt*15, 15);
      $query->orderBy('st.is_fav','DESC');
      $query->orderBy('st.id','DESC');
      $res = $query->execute()->fetchAll();
      $result = [];
    }

    foreach ($res as $row) {
      $edit = Url::fromUserInput('/ajax/phone_book/item/edit/' . $row->id);
      $delete = Url::fromUserInput('/del/phone_book/item/delete/' . $row->id,array('attributes' => array('onclick' => "return confirm('Are you Sure ?')")));

      $edit_link = Link::fromTextAndUrl(t('Edit'), $edit);
      $delete_link = Link::fromTextAndUrl(t('Delete'), $delete);
      $edit_link = $edit_link->toRenderable();
      $delete_link  = $delete_link->toRenderable();
      $edit_link['#attributes'] = ['class'=>'use-ajax edit-option'];
      $delete_link['#attributes'] = ['class'=>'use-ajax delete-option'];
      $delete_link['#prefix'] = '<i class="fa fa-trash" ></i>';

      $d = [
        'name' => $row->name,
        'phone_number' => $row->phone_number,
        'opt' => render($delete_link),
        'opt1' => render($edit_link),
      ];
      
      $result[] = [
        'data' => $d,
        'class' => ['is-fav-'.$row->is_fav],
      ];
    }

    return $result;
  }
}
