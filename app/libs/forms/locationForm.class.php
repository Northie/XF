<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class locationForm extends manager {
	
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
	
		$this->form_name = 'Location';
		$this->action = \core\System_Settings::Load()->getSettings('ADMIN_BASE').'/location';
		
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
		/*
		
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
			,"notes"=>"<a href='/admin/GalleryManager/update' class='btn btn-mini btn-success'>Create New Gallery</a> | <a href='/admin/GalleryManager' class='btn btn-mini btn-info'>See All</a>"
		];
		//*/
		
		//*/
		$form[] = [
			 "label"=>"Banner Image"
			,"name"=>"banner"
			,"input_type"=>"image"
			,"required"=>0
			//,"validate"=>"text"
			,"data_type"=>"text"
		];
		/*//
		/*
		$form[] = [
			 "label"=>"Banner Text"
			,"name"=>"banner_text"
			,"input_type"=>"text"
			,"required"=>0
			//,"validate"=>""
			,"data_type"=>"text"
		];
		 * 
		 */
		
		$form[] =  [
			 "label"=>"Location"
			,"name"=>"property_location"
			,"input_type"=>"textarea"
			,"required"=>0
			,"data_type"=>"text"
			,"notes"=>"Enter Your Property Address Here. Click the preview button to check the marker is in the correct loction. <a href='#'  class='btn btn-ifo btn-mini map-preview'>Preview</a>"
		];
		
		$form[] =  [
			 "label"=>"Map Centre"
			,"name"=>"map_centre"
			,"input_type"=>"textarea"
			,"required"=>0
			,"data_type"=>"text"
			,"notes"=>"Where should the map be centered? If left blank the centre will be on the property location (above) Click the preview button to check the map below.  <a href='#' class='btn btn-ifo btn-mini map-preview'>Preview</a>"
		];
		
		$form[] =  [
			 "label"=>"Map Zoom"
			,"name"=>"map_zoom"
			,"input_type"=>"text"
			,"required"=>0
			,"data_type"=>"text"
			,"notes"=>"Map Zoom Level (1 = whole word visible, 22 = far too close! Default = 13). Click the Preview button to check.  <a href='#' class='btn btn-ifo btn-mini map-preview'>Preview</a>"
		];
		
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
			,"notes"=>"Recommended content: Your Address, Phone number, etc"
		];
		
		$this->submit_label = 'Save Location Page';
		
		return $form;
	}
}