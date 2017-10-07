<?php

namespace Drupal\resume\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
* 
*/
class ResumeController extends ControllerBase
{
	public function mytest(){
       /* return array(
          '#title' => 'Hello World!',
          '#markup' => 'This is some content. <a href="http://192.168.100.11/project/radhey/drupal-8/mymodule/radhey">Link 1</a>',
           
        );*/
       // $aaa = $this->config();
        //$aaa =$this->config('system.site')->get('mail');
        echo "<pre>";
        $aaa =$this->config('system.site')->get();
        print_r($aaa);
        die;
    }
 
	public function display() {
		/*return array(
      	  '#title' => 'Hello World!',
          '#markup' => 'This is some content. <a href="http://192.168.100.11/project/radhey/drupal-8/mymodule/radhey">Link</a>',
           
    	);*/
    	
    	//create table header
    	$header_table = array(		
    			/*'id' => t('SrNo'),
    			'condidate_name' => t('Mame'),
    			'condidate_email' => t('Email'),
    			'condidate_number' => t('Mobile'),
    			'condidate_dob' => t('Date of Birth'),
    			'condidate_gender' => t('Gender'),
    			'condidate_confirmation' => t('Confirmation'),
    			'condidate_copy' => t('Copy'),
    			'uid' => t('Uid'),
    			'username' => t('UserName'),
    			'roles' => t('Roles'),
    			'opt1' => t('Delete'),
    			'opt2' => t('Edit'),*/
                array('data' => 'SrNo', 'field' => 'id', 'sort' => 'asc'),
                array('data' => $this->t('Name'), 'field' => 'condidate_name'),
                array('data' => $this->t('Email')),
                array('data' => $this->t('Mobile'), 'field' => 'condidate_number'),
                array('data' => $this->t('Date of Birth')),
                array('data' => $this->t('Gender')),
                array('data' => $this->t('Confirmation')),
                array('data' => $this->t('Copy')),
                array('data' => $this->t('Uid')),
                array('data' => $this->t('UserName')),
                array('data' => $this->t('Roles')),
                array('data' => $this->t('Delete')),
                array('data' => $this->t('Edit')),
       		);

    	//select records from table
    	$query = \Drupal::database()->select('custom_resume', 'cr');
    	$query->fields('cr', ['id','condidate_name','condidate_email','condidate_number','condidate_dob','condidate_gender','condidate_confirmation','condidate_copy','uid','username','roles']);
        $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header_table);
        $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
        $result = $pager->execute();   

    	//$result = $query->execute()->fetchAll();
    	$rows = array();

    	foreach ($result as $data) {
    		$delete = Url::fromUserInput('/resume/myform/delete/'.$data->id);
    		$edit   = Url::fromUserInput('/resume/myform?id='.$data->id);

    		 //print the data from table
    		$rows[] = array(
	    		'id' => $data->id,
	    		'condidate_name' => $data->condidate_name,
	    		'condidate_email' => $data->condidate_email,
	    		'condidate_number' => $data->condidate_number,
	    		'condidate_dob' => $data->condidate_dob,
	    		'condidate_gender' => $data->condidate_gender,
	    		'condidate_confirmation' => $data->condidate_confirmation,
	    		'condidate_copy' => $data->condidate_copy,
	    		'uid' => $data->uid,
	    		'username' => $data->username,
	    		'roles' => $data->roles,	
	    		\Drupal::l('Delete', $delete),
	    		\Drupal::l('Edit', $edit),
	    	);	
    	}

    	//display data in site
        $url = Url::fromUri('http://192.168.100.11/project/radhey/drupal-8/resume/myform');
        $external_link = \Drupal::l(t('+ Add Register'), $url);

        $form = array(
               '#markup' => $external_link
            );

      /*  $site_name = \Drupal::config();
        print_r($site_name);
        die;*/

    	$form['table'] = [
    					'#type' => 'table',
    					'#header' => $header_table,
    					'#rows' => $rows,
    					'#empty' => t('No User Fount...!!!'),
    				];	
        $form['pager'] = [
                        '#type' => 'pager'
                    ];            
    	return $form;
	}
}