<?php

namespace Plugins;

class restClearCache extends DefaultPlugin {

	public static function RegisterMe() {
		Plugins::Load()->Register(__CLASS__,"onAfterRestCreate");
		Plugins::Load()->Register(__CLASS__,"onAfterRestUpdate");
		Plugins::Load()->Register(__CLASS__,"onAfterRestDestroy");
		return;
	}
	
	public function __construct() {
		//this is the only contructor - there is no parent constructor;
	}
	
	public function Execute() {
	
		//var_dump($this);
		
		if(function_exists('apc_clear_cache') && $this->caller->cacheUsed()){
			apc_clear_cache('user');
		}
		
		return true;
	}

}
