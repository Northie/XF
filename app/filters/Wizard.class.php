<?php

namespace filters;

class wizard extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__, $this);

		$wizard = $this->package->getWizard();

		var_dump($wizard);

		die();

		$this->FFW(__CLASS__, $this->deps);
	}

	public function out() {

		$this->RWD(__CLASS__);
	}

}
