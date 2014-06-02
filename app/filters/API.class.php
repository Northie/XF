<?php

namespace filters;

class api extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {

		$this->package->logExecutedFilter(__CLASS__,$this);
		
		if(!\Plugins\Plugins::Load()->DoPlugins('onBeforeActionFilterIn',$this)) {
			$this->out();
		}
		
		//$request = $this->request->get("REQUEST");
		
		//list($module,$action) = explode("\\",$request);
		$module = $this->package->request->modules;
		$action = $this->package->request->action;
		
		if(strpos($module[0],"_") === false) {
			$model = \libs\models\Resource::Load($module[0]);
		}
		
		$service = new \libs\data\genericDataService();
		$service->setData($this->request->data);

		//$a = 'modules\\'.$request($service,$model);
		
		$this->package->dataService = $service;
		$this->package->model = $model;
		
		$this->package->Execute();
		
		\Plugins\Plugins::Load()->DoPlugins('onAfterActionFilterIn',$this);
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {

		if(!\Plugins\Plugins::Load()->DoPlugins('onBeforeActionFilterOut',$this)) {
			return false;
		}
		
		$this->package->response->status = new \core\ResponseStatus(array(
		    "alert"=>false,
		    "type"=>"info",
		    "message"=>"Request executed sucessfully",
		    "proceed"=>true
		));
		$this->package->response->success = true;
		$this->package->response->content = $data;
		$this->package->response->fromCache = false;
		$this->package->response->data = $this->package->getData();
		
		\Plugins\Plugins::Load()->DoPlugins('onAfterActionFilterOut',$this);
		
		$this->RWD(__CLASS__);
	}
}
