<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class frontContactForm extends manager {
	
	public function __construct() {
		
		parent::__construct(__CLASS__);
		
		$this->setInputFilter($this,'filterInput');
		$this->setOutputFilter($this,'filterOutput');
		
	}
	
	public function filterInput($type,$data) {
		return $data;
	}
	
	public function filterOutput($type,$data) {
		
		return \libs\misc\Tools::html_escape($data);
	}

	public function getFormDefinition() {
	
		$this->form_name = 'Front Contact';
		$this->action = \core\System_Settings::Load()->getSettings('WEB_BASE').'/contact';
		
		$form[] = [
			 "label"=>"Title"
			,"name"=>"title"
			,"input_type"=>"select"
			,"required"=>1
			//,"validate"=>"text"
			,"data_type"=>"text"
			,"option_data"=>[
			    ["display"=>"Ms","post"=>"Ms"],
			    ["display"=>"Miss","post"=>"Miss"],
			    ["display"=>"Mrs","post"=>"Mrs"],
			    ["display"=>"Mr","post"=>"Mr"]
			]
		];		
		$form[] = [
			 "label"=>"Name"
			,"name"=>"name"
			,"input_type"=>"text"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Email"
			,"name"=>"email"
			,"input_type"=>"text"
			,"required"=>1
			,"validate"=>"email"
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Telephone"
			,"name"=>"telephone"
			,"input_type"=>"text"
			,"required"=>0
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Enquiry"
			,"name"=>"enquiry"
			,"input_type"=>"textarea"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Address"
			,"name"=>"address"
			,"input_type"=>"textarea"
			,"required"=>0
			//,"validate"=>""
			,"data_type"=>"text"
		];
		
		$settings = \libs\misc\Tools::getSettings();
		
		if($settings['use_captcha'] == 1 && $_SESSION['domain']['level'] > 1) {
		
			$form[] = [
				 "label"=>"Anti Spam"
				,"name"=>"address"
				,"input_type"=>"recaptcha"
				,"required"=>0
				,"validate"=>"recaptcha"
				,"data_type"=>"text"
				,"notes"=>"Enter the two words as you see them"
			];
		}

		$this->submit_label = 'Submit Inquiry';
		
		
		return $form;
	}
}