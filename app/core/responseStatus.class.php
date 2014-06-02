<?php

namespace core;

class responseStatus {
	private $data = array();
	
	private $alertDefault	=	false;
	private $typeDefault	=	"";
	private $messageDefault	=	"";
	private $proceedDefault	=	false;
	
	public function __construct($status=false) {

		$this->data["alert"]	=	$this->altertDefault;
		$this->data["type"]	=	$this->typeDefault;
		$this->data["message"]	=	$this->messageDefault;
		$this->data["proceed"]	=	$this->proceedDefault;

		if($status) {
			$this->set($status);
		}
	}
	
	public function set($status) {
		$this->data["alert"]	=	$status['alert']	?	$status['alert']	:	$this->data["alert"];
		$this->data["type"]	=	$status['type']		?	$status['type']		:	$this->data["type"];
		$this->data["message"]	=	$status['message']	?	$status['message']	:	$this->data["message"];
		$this->data["proceed"]	=	$status['proceed']	?	$status['proceed']	:	$this->data["proceed"];
	}
	
	public function setAlert($val) {
		$this->data["alert"] = !!$val;
	}
	
	public function setType($type) {
		$this->data["type"] = $type;
	}
	
	public function setMessage($msg) {
		$this->data["message"] = $msg;
	}
	
	public function setProceed($val) {
		$this->data["proceed"] = !!$val;
	}
	
	public function getStatus() {
		return $this->data;
	}
}