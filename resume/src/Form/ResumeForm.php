<?php

namespace Drupal\resume\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
* 
*/
class ResumeForm extends FormBase {
	
	public function getFormId() {
   		return 'resume_form';
  	}

	public function buildForm(array $form, FormStateInterface $form_state) {


		$conn = Database::getConnection();
		$record = array();

		if(isset($_GET['id'])) {
			$query = $conn->select('custom_resume', 'cr')
						  ->condition('id', $_GET['id'])
						  ->fields('cr');
			$record = $query->execute()->fetchAssoc();				 	
			//print_r($record['condidate_email']);
		}	
		
		$url = Url::fromUri('http://192.168.100.11/project/radhey/drupal-8/resume/myform/list');
        $external_link = \Drupal::l(t('+ View Register'), $url);

        $form = array(
               '#markup' => $external_link
        );

		$form['condidate_name'] = array(
				'#type' => 'textfield',
				'#title' => t('Condidate Name : '),
				'#required' => TRUE,
				'#default_value' => (isset($record['condidate_name']) && $_GET['id']) ? $record['condidate_name'] : '',
			);
		$form['condidate_email'] = array(
				'#type' => 'email',
				'#title' => t('Condidate Email : '),
				'#required' => TRUE,
				'#default_value' => (isset($record['condidate_email']) && $_GET['id']) ? $record['condidate_email'] : '',
			);
		$form['condidate_number'] = array(
				'#type' => 'tel',
				'#title' => t('Mobile No : '),
				'#default_value' => (isset($record['condidate_number']) && $_GET['id']) ? $record['condidate_number'] : '',
			);
		$form['condidate_dob'] = array(
				'#type' => 'date',
				'#title' => t('Date of Birth'),
				'#required' => TRUE,
				'#default_value' => (isset($record['condidate_dob']) && $_GET['id']) ? $record['condidate_dob'] : '',
			);
		$form['condidate_gender'] = array(
				'#type' => 'select',
				'#title' => t('Gender'),
				'#options' => array(
						'' => t('--Select--'),
						'Male' => t('Male'),
						'Female' => t('Female'),
					),
				'#default_value' => (isset($record['condidate_gender']) && $_GET['id']) ? $record['condidate_gender'] : '',
			);
		$form['condidate_confirmation'] = array(
				'#type' => 'radios',
				'#title' => t('Are you about 18 Year'),
				'#options' => array(
						'Yes'=>t('Yes'),
						'No'=>t('No'),
					),
				'#default_value' => (isset($record['condidate_confirmation']) && $_GET['id']) ? $record['condidate_confirmation'] : '',
			);
		$form['condidate_copy'] = array(
				'#type' => 'checkbox',
				'#title' => t('Sand me copy of the Application'),
				'#default_value' => (isset($record['condidate_copy']) && $_GET['id']) ? $record['condidate_copy'] : '',
			);
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = array(
				'#type' => 'submit',
				'#value' => $this->t('Save'),
				'#button_type' => 'primary',
			);
		
	/*	$form['getid'] = array(
				'#label' => t('Get ID'), 
				'#markup' => $_GET['id'],
			);*/
		return $form;		
		
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		
	/*	if ($this->currentUser->isAnonymous()) {
      		$form_state->setError($form['add'], $this->t('You must be logged in to add values to the database.'));
    	}*/

		$name = $form_state->getValue('condidate_name');
		if(!preg_match("/^[A-Za-z \s]+$/", $name)) {
			$form_state->setErrorByName('condidate_name', $this->t('You must enter characters with space'));
		}
		
		/*if(!intval($form_state->getValue('condidate_number'))) {
			$form_state->setErrorByName('condidate_number', $this->t('You must enter Mobile Number'));
		}*/
		
		if(strlen($form_state->getValue('condidate_number')) < 10) {
			$form_state->setErrorByName('condidate_number', $this->t('Mobile Number too be short.'));
		}
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
		/*drupal_set_message($this->t('@can_name, Your application is being submitted!, UserID- @user_id, UserName- @user_name, UserRoles- @user_roles', array('@can_name' => $form_state->getValue('condidate_name'), '@user_id' =>$uid, '@user_name'=>$userCurrent, '@user_roles' => $role)));*/
		
		/*foreach ($form_state->getValue() as $key => $value) {
			drupal_set_message($key .' - '. $value);
		}*/

		$uid = \Drupal::currentUser()->id();
		$userCurrent = \Drupal::currentUser()->getUsername(); 

		foreach (\Drupal::currentUser()->getRoles() as $roles_key => $roles_value) {
			$roles[] = $roles_value;
		}
		
		$role = implode(",", $roles);
		
		$select = \Drupal::database()->select('custom_resume','cr')
										->fields('cr',['condidate_email'])
										->condition('uid', $uid, '=')
										->condition('condidate_email', $form_state->getValue('condidate_email'), '=')
										->execute();
		
		foreach ($select as $key => $value) {
			$allReady[] = $value->condidate_email;
		}

		# update data with check alreday eamil.

		if(isset($_GET['id'])) {
			$field = $form_state->getValue();
				
				if(in_array($form_state->getValue('condidate_email'), $allReady)) {

					drupal_set_message(t('Email Alreday Exitss.'), 'error');

				}
				else {
					$fields = array(
						'condidate_name' => $field['condidate_name'],
						'condidate_email' => $field['condidate_email'],
						'condidate_number' => $field['condidate_number'],
						'condidate_dob' => $field['condidate_dob'],
						'condidate_gender' => $field['condidate_gender'],
						'condidate_confirmation' => $field['condidate_confirmation'],
						'condidate_copy' => $field['condidate_copy'],
					);	
					$query = \Drupal::database();
					$query->update('custom_resume')
							->fields($fields)
							->condition('id', $_GET, '=')
							->condition('uid', $uid, '=')
							->execute();
					drupal_set_message('Successfully Updated...'. $field['condidate_name']);
					$form_state->setRedirect('resume.display_table_list');	
				}
		}	
		
		else {
			# insert data with check alreday email.
			if(in_array($form_state->getValue('condidate_email'), $allReady)) {

				drupal_set_message(t('Alreday Register Email Exits.'), 'error');
			}
			else {	
				$insert = \Drupal::database()->insert('custom_resume')
												->fields([
														'condidate_name',
														'condidate_email',
														'condidate_number',
														'condidate_dob',
														'condidate_gender',
														'condidate_confirmation',
														'condidate_copy',
														'uid',
														'username',
														'roles',
													])
												->values(array(
														$form_state->getValue('condidate_name'),
														$form_state->getValue('condidate_email'),
														$form_state->getValue('condidate_number'),
														$form_state->getValue('condidate_dob'),
														$form_state->getValue('condidate_gender'),
														$form_state->getValue('condidate_confirmation'),
														$form_state->getValue('condidate_copy'),
														$uid,
														$userCurrent,
														$role,

													))
												->execute();
				drupal_set_message('Successfully Register.');	
				$form_state->setRedirect('resume.display_table_list');							
			}
		}								
	
	}

}


/*create table custom_resume(id int(10) AUTO_INCREMENT PRIMARY KEY,condidate_name varchar(100) NOT NULL, condidate_email varchar(100), condidate_number varchar(100), condidate_dob varchar(100), condidate_gender varchar(100), condidate_confirmation varchar(100), condidate_copy varchar(100), uid varchar(100), username varchar(100), roles varchar(100));*/
?>