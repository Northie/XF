<?php

namespace core;

class FrontController {
	
	public static $instance;
	
	const API = 1;
	const LAYOUT = 2;
	const CONNECT = 4;
	
	public $mode = 0;
	
	private $t1,$t2;
	
	private function __construct($get=false,$post=false,$mode=0,$method='GET') {
		
		$this->t1 = microtime(true);
		
		$this->mode = $mode;
		
		$request = new request;
		
		System_Settings::Load()->addSetting('ZEST_MODE',$mode);
		
		$request->set("MODE",$this->mode);
				
		System_Settings::Load()->addSetting('ZEST_UPLOAD_FIELD_NAME','ZEST_FILE_UPLOAD');
		System_Settings::Load()->addSetting('ZEST_UPLOAD_DIR',dirname(__FILE__)."/../../assets/");
	
		if($_POST['PHPSESSID'] != '') {
			session_id($_POST['PHPSESSID']);
		}

		
		if(!$get) {
			$qs = str_replace("_req=","",$_SERVER['REQUEST_URI']);
		} else {
			$qs = $get;
		}
		
		if($mode == self::LAYOUT) {
			session_regenerate_id();
			$request->setPackageRequest($qs);
		}
		
		if($mode == self::API) {
			$request->setAPIRequest();
		}
		
		if($mode == self::CONNECT) {
			$request->setAPIRequest($method,$get,$data);
		}
		
		session_start();
		
		
		$_POST = $post ? $post : $_POST;
		
		$has_get = !!$get;
		$has_post = !!$_POST;
				
		if($mode == self::API && !$has_get && !$has_post) {
			//set error in response
			//return; 
		}
		
		$request->set("GET",$get);
		$request->set("POST",$_POST);
		
		$response = new response;
		
		$this->request = $request;
		$this->response = $response;

		\Plugins\Plugins::RegisterPlugins();

		$filters = new \core\Filter_Manager($request,$response);
		
		//if($mode != self::CONNECT) {
			$filters->Execute();
		//}
		
	}

	public static function Layout($get=false,$post=false) {
		
		$mode = self::LAYOUT;		
		
		if(!$post) {
			$post = $_POST;
		}
		
		if (!isset(self::$instance)) {
			self::$instance = new \core\FrontController($get,$post,$mode);
		}
		return self::$instance;
	}

	public static function API($get=false,$post=false) {
		
		$mode = self::API;
		
		if(!$post) {
			$post = $_POST;
		}
				
		if (!isset(self::$instance)) {
			self::$instance = new \core\FrontController($get,$post,$mode);
		}
		return self::$instance;
	}

	public static function Connect($method='GET',$get=false,$post=false) {
		
		$mode = self::CONNECT;
				
		self::$instance = new \core\FrontController($get,$post,$mode,$method);
		
		return self::$instance;
	}
	

	public function getLayout($return=false) {

		if($this->mode == self::CONNECT) {
			return $this->response;
		}
		
		$layout = $this->request->get("REQUEST");
		
		if($this->mode == self::API) {
			$layout = 'json';
		}
		
		$layout = str_replace('/','\\',$layout);
		
		$v = 'views\\'.str_replace("\packages\\","",$layout);
		
		$v = str_replace("/packages/","",$v);
		
		$view = new $v;
		
		$view->setResponse($this->response);
		
		$view->Execute();

		if($this->mode == self::LAYOUT) {
			return $view->RenderPage($return);
		}

		if($this->mode == self::API) {
			return $view->RenderJSON($return);
		}
		
	}
	
	public function getData($return=false) {
		$this->mode == self::API;
		return $this->getLayout($return);
	}
	
	public function returnData() {
		$this->mode == self::CONNECT;
		return $this->getLayout();
	}
	
	//*
	public function __destruct() {
	
		$this->t2 = microtime(true);
		
		$t = $this->t2 - $this->t1;
		
		$sql = "
			INSERT INTO
				memory_log
			VALUES (
				 NULL
				,:mem_assigned
				,:mem_used
				,:timestamp
				,:req
				,:t
			);
		";
		
		$args['mem_assigned']	=	memory_get_peak_usage();
		$args['mem_used']	=	memory_get_peak_usage(1);
		$args['timestamp']	=	time();
		$args['req']		=	$_SERVER['REQUEST_URI'];
		$args['t']		=	$t;
		
		//\libs\pdo\DB::Load()->Execute($sql,$args);
		//print_r($args);
		//print_r(get_included_files());
	}
	//*/
	
}

class ZestException extends \Exception {
	public function __construct($msg='') {
		parent::__construct($msg);
	}
}