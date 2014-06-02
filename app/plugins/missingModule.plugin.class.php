<?php

namespace Plugins;

class missingModule extends DefaultPlugin {

	public static function RegisterMe() {
		Plugins::Load()->Register(__CLASS__,"onMissingModule");
		return;
	}
	
	public function __construct() {
		//this is the only contructor - there is no parent constructor;
	}
	
	public function Execute() {
	
		//var_dump($this);
		
		$this->caller->data['content'] = array("missing_module"=>$_SERVER['QUERY_STRING']);
		
		return true;
	}

}
