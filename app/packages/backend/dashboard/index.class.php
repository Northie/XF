<?php
namespace packages\backend\dashboard;

class index extends \packages\package {
	use \packages\authenticator {
		\packages\authenticator::__construct as authenticatorConstruct;
	}
	
	public function __construct() {
		
		parent::__construct();
		
		$this->authenticatorConstruct();
		
		$this->data['status'] = 'HTTP/1.1 200 OK';
		
		$this->view  = str_replace("packages\\","\\views\\",__CLASS__);
		
	}
	
	public function Execute() {

		if(!\Plugins\Plugins::Load()->DoPlugins('onBefore'.__METHOD__,$this)) {
			return false;
		}
		
		//execute package logic
		
		//build data array for view to use
		$this->data = array();
		
		\Plugins\Plugins::Load()->DoPlugins('onAfter'.__METHOD__,$this);
	}	
}