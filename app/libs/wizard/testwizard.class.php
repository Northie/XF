<?php

namespace libs\wizard;

class testWizard extends wizard {

	public function __construct() {
		parent::__construct();

		$this->key = __CLASS__;

		$this->addStep('packages\\frontend\\A\\index')->asStart();
		$this->addStep('packages\\frontend\\B\\index')->afterStep('packages\\frontend\\A\\index');
		$this->addStep('packages\\frontend\\C\\index')->afterStep('packages\\frontend\\B\\index');
	}

}
