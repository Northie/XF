<?php

namespace libs\forms;

class NewUser extends manager {
	//*
	public function __construct() {
		parent::__construct(__CLASS__);
		//$this->caller = $caller;
	}
	//*/
	public function getFormDefinition() {
	
		$this->form_name = 'New User';
		
		$form[] = array(
				"label"=>"First Name",
				"name"=>"firstname",
				"required"=>1,
				"input_type"=>"text",
				"data_type"=>"text"
		);
		$form[] = array(
				"label"=>"Last Name",
				"name"=>"lastname",
				"required"=>1,
				"input_type"=>"text",
				"data_type"=>"text"
		);
		$form[] = array(
				"label"=>"Username",
				"name"=>"username",
				"required"=>1,
				"input_type"=>"text",
				"data_type"=>"text"
		);
		$form[] = array(
				"label"=>"Email",
				"name"=>"email",
				"required"=>1,
				"validate"=>"email",
				"input_type"=>"text",
				"data_type"=>"text"
		);
		$form[] = array(
				"label"=>"Password",
				"name"=>"password",
				'type'=>'password',
				"required"=>1,
				"validate"=>"match:confirm-password",
				"input_type"=>"password",
				"data_type"=>"password"
		);
		$form[] = array(
				"label"=>"Confirm Password",
				"name"=>"confirm-password",
				"required"=>1,
		    		"validate"=>"match:password",
				"input_type"=>"password",
				"data_type"=>"password"
		);
		$form[] = array(
				"label"=>"Account ID",
				"name"=>"account_id",
				"input_type"=>"hidden",
				"data_type"=>"text",
				"value"=>1
		);
		
		//$form = $this->HTMLise($form);
		
		return $form;
	}
}