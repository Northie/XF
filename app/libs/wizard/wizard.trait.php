<?php

namespace libs\wizard;

trait wizardTrait {

	private $wizard;

	public function __construct($wizard) {

		$wizard = "\\" . __NAMESPACE__ . "\\" . $wizard;

		$this->currentStep = __CLASS__;

		$this->wizard = new $wizard;

		$this->wizard->attemptStep($this->currentStep);

		$this->addFilter('\\filters\\wizard')->afterFilter('\\filters\\security');
	}

	public function getWizard() {
		return $this->wizard;
	}

}
