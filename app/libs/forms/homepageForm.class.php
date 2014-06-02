<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class homepageForm extends manager {
	
	public function __construct() {
		
		parent::__construct(__CLASS__);
		
		$this->setInputFilter($this,'filterInput');
		$this->setOutputFilter($this,'filterOutput');
	}
	
	public function filterInput($type,$data) {

		if($type == 'richtext') {
			return \libs\misc\Tools::cleanHtml($data);
		}
		
		return $data;
	}
	
	public function filterOutput($type,$data) {
		
		if($type == 'richtext') {
			return \libs\misc\Tools::cleanHtml($data);
		}
		
		return \libs\misc\Tools::html_escape($data);
	}

	public function getFormDefinition() {
	
		$this->form_name = 'Homepage';
		$this->action = \core\System_Settings::Load()->getSettings('ADMIN_BASE').'/homepage';
		
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
		//*
		
		//if($_SESSION['domain']['level'] > 1) {
		
			$g = \libs\models\Resource::Load('gallery')->read(["domain_id"=>$_SESSION['domain']['id']])->getMany();

			$option_data = [];

			for($i=0;$i<$g['meta']['count'];$i++) {
				$option_data[] = [
				    "display"=>$g['data'][$i]['title'],
				    "post"=>$g['data'][$i]['id']
				];
			}

			$form[] = [
				 "label"=>"Banner Image(s)"
				,"name"=>"banner"
				,"input_type"=>"select"
				,"required"=>1
				//,"validate"=>"text"
				,"data_type"=>"text"
				,"option_data"=>$option_data
				,"notes"=>"<a href='/admin/GalleryManager/update' class='btn btn-mini btn-success' target='_blank'>Create New Gallery</a> | <a href='/admin/GalleryManager' class='btn btn-mini btn-info' target='_blank'>See All</a> | <a class='btn btn-inverse btn-mini' id='refresh-gallery-list' href='http://api.365villas.net/v1/domain/".$_SESSION['domain']['id']."/gallery'>Refresh List</a>"
			];

		//} else {
			/*
			$form[] = [
				 "label"=>"Banner Image"
				,"name"=>"banner"
				,"input_type"=>"image"
				,"required"=>0
				//,"validate"=>"text"
				,"data_type"=>"text"
				,"notes"=>"Upgrade to get rotating banners powered by a gallery"
			];
			//*/	
		//}
		//*/
		/*
		$form[] = [
			 "label"=>"Banner Text"
			,"name"=>"banner_text"
			,"input_type"=>"text"
			,"required"=>0
			//,"validate"=>""
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
		$form[] = [
			 "label"=>"Side Content"
			,"name"=>"side_content"
			,"input_type"=>"richtext"
			,"required"=>0
			//,"validate"=>""
			,"data_type"=>"text"
		];
		
		$this->submit_label = 'Save Homepage';
		
		
		return $form;
	}
}