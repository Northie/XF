<?php

namespace filters;

class package extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);

		if(!\Plugins\Plugins::Load()->DoPlugins('onBeforePackageFilterIn',$this)) {
			$this->out();
		}
		
		$this->package->Execute();
		
		\Plugins\Plugins::Load()->DoPlugins('onAfterPackageFilterIn',$this);
		
		$this->FFW(__CLASS__,$this->deps);
		//$this->out($this->deps);
	}

	public function out($deps=false) {
		
		if($deps) {
			parent::__construct($deps);
		}

		if(!\Plugins\Plugins::Load()->DoPlugins('onBeforePackageFilterOut',$this)) {
			return false;
		}
		
		$data = $this->package->getData();
		
		$status = new \core\ResponseStatus(array(
		    "alert"=>$this->package->alert(),
		    "type"=>"info",
		    "message"=>"Request executed sucessfully",
		    "proceed"=>true
		));
		 
		$this->package->response->status = $status;
		 
		$this->package->response->success = true;
		$this->package->response->setData($data);
		
		\Plugins\Plugins::Load()->DoPlugins('onAfterPackageFilterOut',$this);
		
		$this->RWD(__CLASS__);
	}
}
