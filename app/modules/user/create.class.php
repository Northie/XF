<?php

namespace modules\user;

//class create extends \modules\Default_Action {
//class create extends \modules\Authenticated_Action {
class create extends \modules\api {

	use \libs\rest\create {
		\libs\rest\create::Execute as restCreateExecute;
	}

	public function __construct() {
		parent::__construct();
	}
	
	public function Execute() {
		if(!\Plugins\Plugins::Load()->DoPlugins("onBeforeUserCreateExecute",$this)) {
			return false;
		}
		
		$this->restCreateExecute();

		\Plugins\Plugins::Load()->DoPlugins("onAfterUserCreateExecute",$this);
	}
}

