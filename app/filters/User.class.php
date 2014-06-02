<?php

namespace filters;

class user extends defaultFilter {

	private $login = true; //ie do login

	public function __construct() {
		parent::__construct();
		$this->admin_base = \core\System_Settings::Load()->getSettings('ADMIN_BASE');
	}

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		$user = \core\Users::Load()->getUser();
		
		//print_r($user);
		
		if($user['id'] > 0) {
			$this->login = false;	//user must log in
			//$this->out();
		}
		
		if($this->login) {
			$this->out();
		} else {
			$this->FFW(__CLASS__,$this->deps);
		}
	}

	public function out() {	
			
		if($this->login) {
		
			if($this->request->mode == \core\FrontController::API) {
				\core\response::Load()->status = new \core\ResponseStatus(array(
				    "alert"=>true,
				    "type"=>"error",
				    "message"=>"You are not logged in",
				    "proceed"=>false
				));
				\core\response::Load()->success = false;
				\core\response::Load()->auth = false;
			} else {
				header("Location: ".$this->admin_base."/user/logout");
				die();
			}

		} else {
			if($this->request->mode == \core\FrontController::API) {
				\core\response::Load()->auth = true;
			}
			
			$this->package->response->self = \core\Users::Load()->getUser();
		}
		
		$this->RWD(__CLASS__);
	}
}

