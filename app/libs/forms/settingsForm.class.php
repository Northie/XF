<?php

namespace libs\forms;

//class LoginForm extends FormHandler {

class settingsForm extends manager {
	
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
	
		$this->form_name = 'Settings';
		$this->action = \core\System_Settings::Load()->getSettings('ADMIN_BASE').'/settings';
		
		//\libs\pdo\DB::Load()->Execute("SELECT * FROM setting_key where level <= ".$_SESSION['domain']['level']." order by sort_order asc")->fetchArray($keys);
		\libs\pdo\DB::Load()->Execute("SELECT * FROM setting_key order by sort_order asc")->fetchArray($keys);
		
		$c = count($keys);
		
		$form = [];
		
		for($i=0;$i<$c;$i++) {
			$keys[$i]['key'];
			$keys[$i]['type'];
			
			$form[$i] = [
				 "label"=>ucwords(str_replace("_"," ",$keys[$i]['key']))
				,"name"=>$keys[$i]['key']
				,"input_type"=>$keys[$i]['type']
				,"required"=>0
				,"data_type"=>"text"
				,"notes"=>$keys[$i]['notes']
				,"disabled"=>($_SESSION['domain']['level'] < $keys[$i]['level'] ? true : false)
			];
			
			if($keys[$i]['validate'] != '') {
				$form[$i]['validate'] = $keys[$i]['validate'];
			}
			
			if($keys[$i]['options'] != '') {
				$form[$i]['option_data'] = json_decode($keys[$i]['options'],1);
			}
		}
		
		$this->submit_label = 'Save Settings';
		
		
		return $form;
	}
}