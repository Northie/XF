<?php

namespace libs\pdo;


class PDOMySql {
	private $db_host;
	private $db_name;
	private $db_user;
	private $db_pass;
	
	public $conn;

	/**
	 * get settings, noramalise database credentials attempt to connect
	 */

	public function __construct($host,$db,$user,$pass) {
		
		//print_r(func_get_args());
		
		$this->db_host = $host;
		$this->db_name = $db;
		$this->db_user = $user;
		$this->db_pass = $pass;
	}

	/**
	 * Attempt to Connect
	 */
	public function Connect() {
		
		$options = [  
			 \PDO::ATTR_EMULATE_PREPARES => false                     // important! use actual prepared statements (default: emulate prepared statements)  
			,\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION           // throw exceptions on errors (default: stay silent)  
			,\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC      // fetch associative arrays (default: mixed arrays)  
		]; 
		
		try {
			$this->conn = new \PDO(
					 "mysql:host=".$this->db_host.";dbname=".$this->db_name.";charset=utf8"
					,$this->db_user
					,$this->db_pass
					,$options
			);
		} catch (\PDOException $e) {
			echo $e->getMessage();
			$this->conn = false;
			die();
		}
		
		return $this->conn;
			
	}
	
	public function DisConnect() {
		$this->conn = null;
	}
}