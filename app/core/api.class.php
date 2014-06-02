<?php
namespace core;

/**
 * 
 * \core\API::Request("GET","user/1");
 */

class API {
	//*/
	
	public function __construct($method,$query,$v='v1/') {
		$this->method = $method;
		$this->query = $query;
		$this->v = $v;
	}
	
	public function setContext($c) {
		$this->context = $c;
	}
	
	public function doRequest($data=false) {
		$request = new \core\request(false);
		$request->setAPIRequest($this->method, $this->v.ltrim($this->query,"/"),$data);
		$api = new \controllers\apiController($request);
		$api->Execute();
		$this->data = $api->getResponse();
		\filters\defaultFilter::$package_uniq = $this->context;
	}
	
	public function getData() {
		return $this->data;
	}
	
	//*/
	
	/*
	public static function Request($method,$query,$v='v1/') {

		if($method == 'GET') {
			return json_decode(file_get_contents("http://127.0.0.1/".$v.ltrim($query,"/")));
		} else {
			//use curl?
		}
	 
		
	}
	//*/
	
}