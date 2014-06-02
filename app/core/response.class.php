<?php

namespace core;

class response {

	private $response = array();
	public $console = false;
	private $content = array();
	private $data = array();

	//private function __construct() {
	public function __construct() {
	
		//$this->console = \libs\misc\FirePHP::getInstance(true);
		
		$status = new responseStatus;
		
		$this->response["success"]		=	false;
		$this->response['request_timestamp']	=	microtime(true);
		$this->response["auth"]			=	false;
		$this->response["admin"]		=	false;
		$this->response["external"]		=	false;
		$this->response["self"]			=	array();
		$this->response["content"]		=	array();
		$this->response["securityToken"]	=	"";
		$this->response["status"]		=	$status->getStatus();
		
		$this->defaults = $this->response;
		
	}	

	public function getResponse() {
		return $this->response;
	}
	
	public function addContent($key,$content) {
		$this->content[$key][] = $content;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function __set($key,$value) {
	
		if($key == 'status') {
			if(is_a($value,"\\core\\responseStatus") ) {
				$value = $value->getStatus();
			} else {
				throw new responseException("response::status must be of type responseStatus, ".gettype($value)." supplied");
			}
		}
	
		$this->response[$key] = $value;
	}
	
	public function __get($key) {
		return $this->response[$key];
	}
	
	public function __isset($key) {
		return isset($this->response[$key]);
	}
	
	public function __unset($key) {
		$this->response[$key] = $this->defaults[$key];
	}
}

class responseException extends \Exception {
	public function __construct() {
		parent::__construct();
	}
}