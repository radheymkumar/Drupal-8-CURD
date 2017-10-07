<?php

namespace Drupal\resume\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
/**
* 
*/
class DeleteForm extends ConfirmFormBase {
	
	public function getFormId() {
		return 'delete_form';
	}

	public $cid;
	
	public function getQuestion() { 
		$path =  \Drupal::request()->getpathInfo();
		$del_id = end(explode('/', $path));
    	return $this->t('Do you want to delete %cid ?', array('%cid' => $del_id));
  	}

  	public function getCancelUrl() {
    	return new Url('resume.display_table_list');
	}

	public function getDescription() {
    	return t('Only do this if you are sure!');
  	}

  	public function getConfirmText() {
    	return t('Delete it!');
  	}

  	public function getCancelText() {
    	return t('Cancel');
  	}

/*	public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = array(
				'#type' => 'submit',
				'#value' => $this->t('Save'),
				'#button_type' => 'primary',
			);
		return $form;
	}*/
/*
	public function validateForm(array &$form, FormStateInterface $form_state) {
		parent::validateForm($form, $form_state);
	}
*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		
		$path =  \Drupal::request()->getpathInfo();
		$del_id = end(explode('/', $path));

		$uid = \Drupal::currentUser()->id();
	
		$query = \Drupal::Database();
		$query->delete('custom_resume')
				->condition('id', $del_id, '=')
				->condition('uid', $uid, '=')
				->execute();
    	drupal_set_message('Record Delete Successfully.');
    	$form_state->setRedirect('resume.display_table_list');	

	} 

}


?>
