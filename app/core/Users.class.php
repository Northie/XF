<?php

namespace core;

class Users {

	private $user = false;
	private $modules = false;
	
	public static $instance;

	private function __construct() {
		$this->user = $_SESSION['user'];
		
	}
	
	public static function Load() {
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}		
		return self::$instance;
	}
		
	public function getUser() {
		
		return $this->user;
	}
	
	public function get($key=false) {
	
		if($key) {
			return $this->user[$key] ? $this->user[$key] : false;
		}
		
		return $this->user;
	
	}
	
	public function isLoggedIn() {
		return $this->user['loggedin'];
	}
	
	public function logIn($user) {
		if($user['store']) {
			$user = $user['store'];
		}
		unset($user['password']);
		$this->user = $user;
		$this->user['loggedin'] = true;
		$this->committ();
	}
	
	public function logOut() {
		$this->user['loggedin'] = false;
		$this->committ();
	}


	public function getUnid() {
		return $this->user['unid'];
	}

	private function committ() {
		$_SESSION['user'] = $this->user;
	}
	
	public function __destruct() {
		$this->committ();
	}
}