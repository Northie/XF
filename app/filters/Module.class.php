<?php

namespace filters;

class module extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		
		//echo "Load user Modules\n";
		
		//if user does not have module, return false;
		$this->package->logExecutedFilter(__CLASS__,$this);
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		//echo "If layout, Add user modules to response\n";
		
		$this->RWD(__CLASS__);
	}
}
