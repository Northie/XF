<?php

namespace packages\frontend\C;

class index extends \packages\package {

	//use packageTools;

	use \libs\wizard\wizardTrait {
		\libs\wizard\wizardTrait::__construct as wizardConstruct;
	}

	public function __construct() {
		parent::__construct();
		$this->wizardConstruct();
	}

	public function Execute() {

		if (!\Plugins\Plugins::Load()->DoPlugins('onBefore' . __METHOD__, $this)) {
			return false;
		}

		if ($someCondition) {
			$this->getWizard()->stepCompleted($this->currentStep);
		} else {
			$this->getWizard()->stepFailed($this->currentStep);
		}

		$this->data = array();

		\Plugins\Plugins::Load()->DoPlugins('onAfter' . __METHOD__, $this);
	}

}
