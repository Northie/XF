<?php

namespace controllers;

class apiController extends defaultController {

	private $request;
	private $response;
	private $module;
	private $view = 'json';
	

	public function __construct($request=false) {
	
		parent::__construct();
	
		if($request && $request->type == \core\request::API) {
			$this->request = $request;
		} else {
			$this->request = new \core\request(__CLASS__);
		}
		
	}
	
	public function Execute() {
		
		//$file = \core\System_Settings::Load()->getFileForClass($this->request->REQUEST);
		
		try {
			//if(is_null($file)) {
			//	throw new fileNotFoundException("Module File Not Found");
			//}
			
			$this->api = \packages\Factory::build($this->request->REQUEST);
			
			try {
				
				\Plugins\Plugins::RegisterPlugins();
				
				$this->response = new \core\response;
				
				$this->api->setRequest($this->request);
				$this->api->setResponse($this->response);
				
				$filter_list = $this->api->getFilterList();
				
				$filter_manager = new \core\filterManager($this->api,$filter_list);
				
				$filter_manager->Execute();
				
				$response = $this->api->response->getResponse();
				
				unset($response['defaults']);
				unset($response['request']['data']);
				unset($response['securityToken']);
				

				//$mem = round((memory_get_peak_usage(true) / (1024*1024)),2);
				$mem = memory_get_peak_usage(true);

				$this->t2 = microtime(1);

				$t = $this->t2 - $this->t1;
				
				$response['meta'] = [
				   // "files"=>get_included_files(),
				    "memory"=>$mem,
				    "time"=>round($t,6)
				    //"events"=>\core\Events::Load()->getAllEvents()
				];
				
				$this->response = $response;
				
				
				
			} catch (fileNotFoundException $e) {
				//handle no view
			}
			
			
		} catch (fileNotFoundException $e) {
			//handle no package
		}
	}
	
	public function renderJson() {
		
		header("content-type: application/json");
		header("X-Security-Token: ".$response['securityToken']);
		
		echo json_encode($this->response,JSON_PRETTY_PRINT);
	}
	
	public function getResponse() {
		return $this->response;
	}
}