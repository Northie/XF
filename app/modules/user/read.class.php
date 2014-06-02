<?php

namespace modules\user;

//class read extends \modules\Default_Action {
//class read extends \modules\Authenticated_Action {
class read extends \modules\api {

	use \libs\rest\read {
		\libs\rest\read::Execute as restReadExecute;
	}

	public function __construct() {
		parent::__construct();
	}
	
	public function Execute() {
		if(!\Plugins\Plugins::Load()->DoPlugins("onBeforeUserReadExecute",$this)) {
			return false;
		}
		
		$this->restReadExecute();

		\Plugins\Plugins::Load()->DoPlugins("onAfterUserReadExecute",$this);
	}
}

