<?php

namespace filters;

class session extends defaultFilter {

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
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		$this->RWD(__CLASS__);
	}
}
