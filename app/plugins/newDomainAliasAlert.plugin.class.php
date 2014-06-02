<?php

namespace Plugins;

class newDomainAliasAlert extends DefaultPlugin {

	public static function RegisterMe() {
		Plugins::Load()->Register(__CLASS__,"onAfterDomainAliasCreateExecute");
		return;
	}
	
	public function __construct() {
		//this is the only contructor - there is no parent constructor;
	}
	
	public function Execute() {
	
		//email client
		
		$data = $this->caller->request->getData();
		
		return true;
	}

}
