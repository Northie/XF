<?php

namespace packages\frontend\A;

class index extends \packages\package {

	//use packageTools;

	use \libs\wizard\wizardTrait {
		\libs\wizard\wizardTrait::__construct as wizardConstruct;
	}

	public function __construct() {
		parent::__construct();
		$this->wizardConstruct('testWizard');
	}

	public function Execute() {

		if (!\Plugins\Plugins::Load()->DoPlugins('onBefore' . __METHOD__, $this)) {
			return false;
		}

		$this->data = array('wizard' => $this->getWizard());

		if ($someCondition) {
			$this->getWizard()->stepCompleted($this->currentStep);
		} else {
			$this->getWizard()->stepFailed($this->currentStep);
		}

		\Plugins\Plugins::Load()->DoPlugins('onAfter' . __METHOD__, $this);
	}

}
