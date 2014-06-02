<?php

namespace filters;

class context extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		//load settings for context
		
		list($context) = explode(".",$_SERVER['SERVER_NAME']);
		
		if($mode == 'DEV') {
			$context = 'loyaltymatters';
		}
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		$this->RWD(__CLASS__);
	}
}
