<?php

namespace filters;

class domain extends defaultFilter {

	private $redirect_to = false;
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		$domain = explode(".",$_SERVER['SERVER_NAME']);
		/*
		if($domain[1] == '365villas') {
			//subdomain
			$d = \libs\models\Resource::Load('domain')->read(["domain"=>$_SERVER['SERVER_NAME']])->getOne();
		} else {
			//own domain
			$d = \libs\models\Resource::Load('domain')->read(["fqd"=>$_SERVER['SERVER_NAME']])->getOne();
		}
		//*/
		
		$d2 = \libs\models\Resource::Load('domain')->details($_SERVER['SERVER_NAME']);
		
		
		$c = count($d2);
		if($c > 0) {
			$d = $d2[0];
		}

		$users = array();
		
		for($i=0;$i<$c;$i++) {
			if($d[$i]['use_this_alias']) {
				$d = $d[$i];
				$d['domain'] = $d['alias'];
			}
			$users[$d2[$i]['allowed_user_id']] = $d2[$i]['allowed_user_email'];
		}
		
		$_SESSION['domain'] = $d;
		
		$_SESSION['domain']['users'] = $users;
		
				
		//get settings, put into content
		
		$settings = $settings = \libs\misc\Tools::getSettings();
		
		$this->package->response->addContent('_settings',$settings);

		//*
		if(!$_SESSION['user']['loggedin'] && $d['alias'] != $_SERVER['SERVER_NAME']) {
			$this->redirect_to = $d['alias'];
			$this->out();
			return;
		}
		//*/
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		if(!$_SESSION['user']['loggedin'] && $this->redirect_to) {
			header("Location: http://".$this->redirect_to);
		}
		
		$this->RWD(__CLASS__);
	}
}
