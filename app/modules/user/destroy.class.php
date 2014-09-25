<?php

namespace modules\user;

//class destroy extends \modules\Default_Action {
//class destroy extends \modules\Authenticated_Action {
class destroy extends \modules\api {

	use \libs\rest\destroy {
		\libs\rest\destroy::Execute as restDestroyExecute;
	}

	public function __construct() {
		parent::__construct();
	}
	
	public function Execute() {
		if(!\Plugins\Plugins::Load()->DoPlugins("onBeforeUserDestroyExecute",$this)) {
			return false;
		}
		
		$this->restDestroyExecute();

		\Plugins\Plugins::Load()->DoPlugins("onAfterUserDestroyExecute",$this);
	}
}

