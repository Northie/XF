<?php

namespace views;

class Default_View {

	public $output = array();
	
	public $template = '404.tpl.php';
	
	protected $app;

	/*
	public function setApp($app) {
		$this->app = $app;
	}
	//*/
	
	public function setResponse($response) {
		$this->response = $response;
	}
	
	public function setPackage($package) {
		$this->package = $package;
		$this->response = $package->response;
	}
	
	public function RenderPage($r=false) {
		
		$c = $this->response->getContent();
		$d = $this->response->getData();
		
		if($r) {
			ob_start();
		}
		
		//var_dump(get_class($this->package));
		
		$p = get_class($this->package);
		
		if(strpos($p,'frontend') > -1) {
			if(!$d['page']['active'] && !$_SESSION['user']['loggedin']) {
				include($_SERVER['DOCUMENT_ROOT']."/layouts/frontend/404.tpl.php");
			} else {
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/layouts/'.$this->template)) {
					include($_SERVER['DOCUMENT_ROOT'].'/layouts/'.$this->template);
				} else {
					include($_SERVER['DOCUMENT_ROOT'].'/layouts/404.tpl.php');
				}
			}
		} else {
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/layouts/'.$this->template)) {
				include($_SERVER['DOCUMENT_ROOT'].'/layouts/'.$this->template);
			} else {
				include($_SERVER['DOCUMENT_ROOT'].'/layouts/404.tpl.php');
			}			
		}

		if($r) {
			$o = ob_get_contents();
			ob_end_clean();
			return $o;
		}
		
	}
	
	public function RenderJSON($r=false) {
		if($r) {
			return $this->output;
		}
		header("Content-type: application/json");
		echo $this->output;
	}
}