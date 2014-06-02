<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class LoginForm extends manager {
	
	public function __construct() {
		
		parent::__construct(__CLASS__);
	}

	public function getFormDefinition() {
	
		$this->form_name = 'Login';
		$this->action = \core\System_Settings::Load()->getSettings('ADMIN_BASE').'/user/login';
		
		$form[] = array("label"=>"Email","name"=>"email","input_type"=>"email","required"=>1,"validate"=>"email","data_type"=>"text");
		$form[] = array("label"=>"Password","name"=>"password","input_type"=>"password","required"=>0,"data_type"=>"text");
		
		$this->submit_label = 'Login';
		
		return $form;
	}
}