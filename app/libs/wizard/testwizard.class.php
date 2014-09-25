<?php

namespace libs\wizard;

class testWizard extends wizard {

	public function __construct() {
		parent::__construct();

		$this->addStep('A')->asStart();
		$this->addStep('B')->afterStep('A');
		$this->addStep('C')->afterStep('B');
	}

}
