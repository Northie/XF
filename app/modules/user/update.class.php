<?php

namespace modules\user;

//class update extends \modules\Default_Action {
//class update extends \modules\Authenticated_Action {
class update extends \modules\api {

	use \libs\rest\update {
		\libs\rest\update::Execute as restUpdateExecute;
	}

	public function __construct() {
		parent::__construct();
	}
	
	public function Execute() {
		if(!\Plugins\Plugins::Load()->DoPlugins("onBeforeUserUpdateExecute",$this)) {
			return false;
		}
		
		$this->restUpdateExecute();

		\Plugins\Plugins::Load()->DoPlugins("onAfterUserUpdateExecute",$this);
	}
}

