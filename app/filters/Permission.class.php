<?php

namespace filters;

class permission extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		$user = \core\Users::load()->getuser();
		
		//check permission for allowed action
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		$user = \core\Users::load()->getuser();
		
		//set ACL entries for created entities
		
		$this->RWD(__CLASS__);
	}
}