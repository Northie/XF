<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class pricingForm extends manager {
	
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
	
		$this->form_name = 'Pricing';
		$this->action = \core\System_Settings::Load()->getSettings('ADMIN_BASE').'/pricing';
		
		$form[] = [
			 "name"=>"id"
			,"input_type"=>'hidden'
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Active"
			,"name"=>"active"
			,"input_type"=>"select"
			//,"required"=>1
			//,"validate"=>"text"
			,"data_type"=>"text"
			,"option_data"=>[
			    ["display"=>"No","post"=>"0"],
			    ["display"=>"Yes","post"=>"1"]
			]
			,"notes"=>"Active pages show on your website, Inactive pages do not"
		];		
		$form[] = [
			 "label"=>"Title (meta details)"
			,"name"=>"meta_title"
			,"input_type"=>"text"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Description (meta details)"
			,"name"=>"meta_description"
			,"input_type"=>"textarea"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Keywords (meta details)"
			,"name"=>"meta_keywords"
			,"input_type"=>"textarea"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];
		$form[] = [
			 "label"=>"Page Title"
			,"name"=>"pagetitle"
			,"input_type"=>"text"
			,"required"=>1
			//,"validate"=>""
			,"data_type"=>"text"
		];

		
		//*/
		$form[] = [
			 "label"=>"Banner Image"
			,"name"=>"banner"
			,"input_type"=>"image"
			,"required"=>0
			//,"validate"=>"text"
			,"data_type"=>"text"
		];
		//*/

		
		$form[] = [
			 "label"=>"Page Content"
			,"name"=>"content"
			,"input_type"=>"richtext"
			,"required"=>0
			//,"validate"=>""
			,"data_type"=>"text"
		];
		
		$this->submit_label = 'Save Pricing Page';
		
		return $form;
	}
}