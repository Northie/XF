<?php

namespace core;

class request {
	private $request = array();
	public static $instance;
	
	const API = 1;
	const PACKAGE = 2;
	
	public $type = 0;
	
	public $data = array();

	
	public function __construct($type,$request=false) {
		
		switch($type) {
			case 'controllers\packageController':
				
				//var_dump($_SERVER['QUERY_STRING']);
				//var_dump($_SERVER['REQUEST_URI']);
				//$this->setPackageRequest($_SERVER['QUERY_STRING']);
				$this->setPackageRequest($_SERVER['REQUEST_URI']);
				break;
			case 'controllers\apiController':
				switch(true) {
					case (isset($_POST['_method'])):
						$method = strtoupper($_POST["_method"]);
						break;
					case (isset($_GET['_method'])):
						$method = strtoupper($_GET["_method"]);
						break;
					default:
						$method = strtoupper($_SERVER["REQUEST_METHOD"]);
				}
				$this->setAPIRequest($method,$request);
				break;
		}

	}
	
	public function setAPIRequest($method=false,$req=false,$data=false) {
		
		$this->type = self::API;
		
		$method = $method ? $method : $_SERVER["REQUEST_METHOD"];
		
		$_SERVER["REQUEST_METHOD"] = $method;
		
		if(!is_array($_POST)) {
			$_POST = array();
		}
		
		if(!is_array($data)) {
			$data = array();
		}
		
		switch($method) {
			case 'GET':
				$action = 'read';
				break;
			case 'POST':
				$action = 'create';
				$raw = file_get_contents("php://input");
				break;
			case 'PUT':
				$action = 'update';
				$raw = file_get_contents("php://input");
				break;
			case 'DELETE':
				$action = 'destroy';
				$raw = file_get_contents("php://input");
				break;
			case 'HEAD':
				$action = 'read';
				break;
			default:
				$action = 'read';
		}
		
		if($raw) {
			
			if(http_build_query($_POST) == $raw) {
				parse_str($raw,$request_data);
			} else {
				$request_data = json_decode($raw,1);
			}
						
			if(!is_array($request_data)) {
				$request_data = array();
			}
			

			
			$GLOBALS['_'.$method] = $_POST + $request_data + $data;
		}
		
		$modules = $resources = array();
		
		$req = $req ? $req : $_SERVER['REQUEST_URI'];
		
		list($req,$get) = explode("?",$req);
		
		parse_str($get,$_get);
		
		$_GET = $_GET + $_get;
		
		//need better way fo integrating post data
		
		$_POST = $_POST + $data;
		$data = $_POST;
		
		$req = trim($req,"/");
		
		$req = explode("/",$req);
		
		$this->version = array_shift($req);
		
		if($req[0] == '' && $req[1] != '') {
			array_shift($req);
		}
		
		while (count($req) > 0) {
			$m = array_shift($req);
			
			if(!is_null($m)) {
				$modules[] = $m;
			}
			
			$r = array_shift($req);
			
			if(!is_null($r)) {
				$resources[] = $r;
			}
		}
		
		$this->set("REQUEST","\\modules\\".$modules[0]."\\".$action);
		
		$this->level		=	'modules';
		$this->modules		=	$modules;
		$this->action		=	$action;
		$this->resources	=	$resources;
		$this->data		=	$data;
		
		
		$file = System_Settings::Load()->getFileForClass("modules\\".$modules[0]."\\".$action);
		
		if(is_null($file)) {
			$action = $this->resources[0];
			
			$cls = "modules\\".$modules[0]."\\".$action;
			
			$file = System_Settings::Load()->getFileForClass($cls);	
			
			if($file != '') {
				$this->action =	$action;
				array_shift($this->resources);
				
				$this->set("REQUEST","\\modules\\".$modules[0]."\\".$action);
			}
		}
		
	}
	
	public function setPackageRequest($qs) {
		
		$this->type = self::PACKAGE;
		
		$this->context = System_Settings::Load()->getSettings('CONTEXT');
		
		list($req,$qs) = explode("?",$qs);
		
		parse_str($qs,$get);
		
		$_GET = &$get;

		$req = explode("/",$req);
			
		if($req[0] == '' && $req[1] != '') {
			array_shift($req);
		}

		switch(true) {
			case ($this->context == 'backend'):
				$admin = array_shift($req);
				$package = array_shift($req);
				$action  = array_shift($req);
				
				if(!$package) {
					$package = "dashboard";
				}
				break;
			default:
				
				$package = array_shift($req);
				$action  = array_shift($req);
				
				if(!$package) {
					$package = "homepage";
				}
				
		}
		
		$package = $this->context ? $this->context."\\".$package : $package;
		
		if(!$action) {
			$action = "index";
		}

		for($i=0;$i<count($req);$i+=2) {
			
			$req[$i] = str_replace(
				 array("forId")
				,array("id")
				,$req[$i]
			);
			
			$_GET[urldecode($req[$i])] = urldecode($req[$i+1]);
		}
		
		$this->level		=	'packages';
		$this->package		=	$package;
		$this->action		=	$action;

		$this->set("REQUEST","\\packages\\".$package."\\".$action);
	}
	
	public function set($key,$val) {
		$this->__set($key,$val);
	}
	
	public function  _GET($key1,$key2=false) {
		try {
			if($key2) {
				if(is_set($_GET[$key1][$key2])) {
					return $_GET[$key1][$key2];
				} else {
					throw new RequestException(\libs\misc\i18n::T(EXPECTED_HTTP_GET_NOT_SET));
				}
			} else {
				if(is_set($_GET[$key1])) {
					return $_GET[$key1];
				} else {
					throw new RequestException(\libs\misc\i18n::T(EXPECTED_HTTP_GET_NOT_SET));
				}			
			}
		} catch (RequestException $e) {
			$msg = $e->getMessage();
			
			$status = new responseStatus;
			
			$status->setAlert(true);
			$status->setMessage($msg);
			$status->setType('error');
			$status->setProceed(false);
			
			response::Load()->status = $status;
		}
	}

	public function get($key) {
		return $this->__get($key);
	}
	
	public function __set($key,$value) {	
		$this->request[$key] = $value;
	}
	
	public function __get($key) {
		return $this->request[$key];
	}
	
	public function __isset($key) {
		return isset($this->request[$key]);
	}
	
	public function __unset($key) {
		$this->request[$key] = $this->defaults[$key];
	}

	public function getRequest() {
		return $this->request;
	}
	
	public function getData($key=false) {
		if($key) {
			return $this->data[$key];
		}
		
		return $this->data;
	}
	
	public function setData($key=false,$val='') {
		if($key) {
			$this->data[$key] = $val;
		}
		if(is_array($val)) {
			$this->data = $val;
		}
	}
}