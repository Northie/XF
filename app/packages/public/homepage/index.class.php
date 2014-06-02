<?php

namespace packages\frontend\homepage;

class index extends \packages\package {

	//use packageTools;

	public function __construct() {
		parent::__construct();

		$this->addFilter('\\filters\\page')->afterFilter('\\filters\\data');

		$this->data['status'] = 'HTTP/1.1 200 OK';

		$this->view = str_replace("packages\\", "\\views\\", __CLASS__);
	}

	public function Execute() {

		if (!\Plugins\Plugins::Load()->DoPlugins('onBefore' . __METHOD__, $this)) {
			return false;
		}

		//execute package logic
		die('here');

		//build data array for view to use
		$this->data = array();

		\Plugins\Plugins::Load()->DoPlugins('onAfter' . __METHOD__, $this);
	}

}
