<?php

namespace filters;

class data extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		//$this->dataService = new \stdClass();
		
		//$this->deps["dataService"] = $this->dataService;
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {

		$this->RWD(__CLASS__);
	}
}