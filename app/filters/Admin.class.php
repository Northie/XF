<?php

namespace filters;

class admin extends defaultFilter {

	private $login = false; //ie do login

	public function __construct() {
		parent::__construct();
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		$this->FFW(__CLASS__,$this->deps);

	}

	public function out() {	
			
		if($this->login) {
			if($this->request->mode == \core\FrontController::API) {
				$this->response->status = new \core\ResponseStatus(array(
				    "alert"=>true,
				    "type"=>"error",
				    "message"=>"You are not logged in",
				    "proceed"=>false
				));
				$this->response->success = false;
				$this->response->auth = false;
			} else {
				header("Location: /email/app/admin");
				die();	
			}

		} else {
			if($this->request->mode == \core\FrontController::API) {
				$this->response->auth = true;
			}
		}
		
		$this->RWD(__CLASS__);
	}
}

