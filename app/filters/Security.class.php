<?php

namespace filters;

class security extends defaultFilter {

	private $doNotSet = false;
	
	public function __construct() {
		parent::__construct();
		
		//print_r($_SESSION);
		
		/*
		if($_SERVER['SERVER_NAME'] == 'api.365villas.net') {

			$_POST['security_token'] = str_replace(" ","+",$_POST['security_token']);

			$request_token = $_POST['security_token'];

			if($request_token == '') {
				$request_token = $_SERVER[strtoupper("http_x_security_token")];
			}


			if($_SESSION['security_token'] == '' && $request_token !='') {
				
				echo($request_token);
				
				$rs = \libs\models\Resource::Load('api_token')->read(['token'=>$request_token])->getOne();
				
				print_r($rs);
				die();
				
				$api = new \core\API("GET","api_token/filter?token=".$request_token);
				$api->setContext($this->uniq);
				$api->doRequest();
				$rs = $api->getData();

				if($rs['data']['api_token']['data']['expires'] < time() && $rs['data']['api_token']['data']['IP'] == $_SERVER['REMOTE_ADDR']) {
					session_id($rs['data']['api_token']['data']['session']);
					session_start();
					$_SESSION['security_token'] = $rs['data']['api_token']['data']['token'];
				}


			}
		}
		 * 
		 */
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		if($_GET['type'] == 'app') {
			
			$s = \core\System_Settings::Load()->getSettings('api');
			
			if($_POST['secret'] == $s['secretkey'] && $s['apikey'] == $_POST['apikey']) {
				$_SESSION['api']['loggedin'] = true;
			
				$salt1 = sha1(microtime());
				$salt2 = sha1(rand() * microtime(1));
				$salt3 = sha1(mt_rand() * microtime(1));

				$st = base64_encode($salt1.$salt2.$salt3);

				$_SESSION['security_token'] = $st;
			} else {
				$_SESSION['security_token'] = false;
				$this->doNotSet = true;
			}
				
		} else {
			if(count($_POST) > 0) {

				$_POST['security_token'] = str_replace(" ","+",$_POST['security_token']);

				$request_token = $_POST['security_token'];

				if($request_token == '') {
					$request_token = $_SERVER[strtoupper("http_x_security_token")];
				}

				if($request_token != $_SESSION['security_token']) {

					$this->package->response->status = new \core\ResponseStatus(array(
					    "alert"=>true,
					    "type"=>"error",
					    "message"=>"Action Prevented",
					    "proceed"=>false
					));
					$this->package->response->success = false;
					$this->package->response->content = array("message"=>array(
					    "Security Concern - The requested action did not originate within this application and cannot be trusted",
					    "You may have clicked on a link or a button in another website or in an email.",
					    "Emails sent by this application will never contain links to actions which modify or delete data, which is what this request is attempting to do",
					    "You have not been logged out, but you should restart your session by clicking in your browser address bar and pressing return. Refreshing the page is not advised as this may attempt the send the untrusted request again."
					));				

					$this->out();
				}	
			}
		}
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		//$user = \core\Users::load()->getuser();
		
		if(trim($_SESSION['security_token']) == '' && !$this->doNotSet) {

			$salt1 = sha1(microtime());
			$salt2 = sha1(rand() * microtime(1));
			$salt3 = sha1(mt_rand() * microtime(1));
			
			$st = base64_encode($salt1.$salt2.$salt3);
			
			$_SESSION['security_token'] = $st;
		}
		
		$this->package->response->securityToken = $_SESSION['security_token'];
		
		$this->RWD(__CLASS__);
	}
}