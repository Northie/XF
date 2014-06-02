<?php

namespace filters;


class cache extends defaultFilter {

	//private $useCache = false;
	private $storeInCache = false;
	
	private $useCache = true;
	//private $storeInCache = true;
	
	public function __construct() {
		parent::__construct();

		$req = $_SERVER['REQUEST_URI'];
		$this->key = sha1($req);
		
		if($_SERVER["REQUEST_METHOD"] == 'GET') {
			
			if($_GET['_force_dc']) {
				$this->useCache = false;
			} else {
			
				$req = $_SERVER['REQUEST_URI'];
				$dc = "/&_dc(\=[^&]*)?(?=&|$)|^_dc(\=[^&]*)?(&|$)/";

				$req = preg_replace($dc, "", $req);

				$this->key = sha1($req);
				//$this->key = $req;
				$this->useCache = true;
			}
		} else {
			$this->useCache = false;
		}
			
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		if($this->useCache) {
			
			$data = apc_fetch($this->key);
			
			if($data === false) {		//if there is no cache
				$this->storeInCache = true;				//then flag to make sure output is cached
				$this->FFW(__CLASS__,$this->deps);			//and then proceed
			} else {						//else
				$this->data = &$data;	
				$this->out();						//jump to out
			}
		} else {
			$this->FFW(__CLASS__,$this->deps);	
		}
	}

	public function out() {
		
		if($this->useCache) {
			
			$this->package->response->cacheKey = $this->key;
			
			if($this->storeInCache) {
				$data = $this->package->getData();
				
				//var_dump($this->package);
				
				apc_store($this->key, $data, 60 * 60);
			} else {
				$this->package->response->status = new \core\ResponseStatus(array(
				    "alert"=>false,
				    "type"=>"info",
				    "message"=>"Request executed sucessfully",
				    "proceed"=>true
				));
				$this->package->response->success = true;
				$this->package->response->fromCache = true;
				$this->package->response->data = $this->data;
			}
		} 

		$this->RWD(__CLASS__);
	}
}
