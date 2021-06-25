<?php

namespace Drupal\phone_book\Controller;

use Drupal\Core\Entity\Controller\EntityController;
use Drupal\Core\Database\Database;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\InvokeCommand;

class DefaultController extends EntityController {

	/**
	 * Manage Add and list forms.
	 * 
	 * @return array
	 */
	function managePhoneBook() {
		$render_array = [];
		
		$form['form'] = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookForm');
		$render_array = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookTableForm', 'All');
		$form['form1'] = $render_array;
		
		return [
			'#theme' => 'phone_book_index_page',
			'form' => $form,
		];
	}

	/**
	 * Edit phone number using Ajax
	 * 
	 * @param $id
	 */
	function editPhoneNumberAjax($id) {
		$conn = Database::getConnection();
		$query = $conn->select('phone_book', 'st');
		$query->condition('id', $id)->fields('st');
		$record = $query->execute()->fetchAssoc();

		$render_array = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookEditForm', $record);
		$response = new AjaxResponse();
		$response->addCommand(new OpenModalDialogCommand('Edit Form', $render_array, ['width' => '800']));

		return $response;
	}

	/**
	 * Delete the provided phone number details.
	 * 
	 * @param $id
	 */
	function deletePhoneNumberAjax($id) {
		$res = \Drupal::database()->query("delete from phone_book where id = :id", array(':id' => $id));

		$render_array = \Drupal::formBuilder()->getForm('Drupal\phone_book\Form\PhoneBookTableForm', 'All');
		$response = new AjaxResponse();

		$response->addCommand(new HtmlCommand('.result_message', ''));
		$response->addCommand(new AppendCommand('.result-section', $render_array));

		return $response;
	}
}
