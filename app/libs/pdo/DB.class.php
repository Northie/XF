<?php

namespace libs\pdo;


class DB {

	private static $instance;
	private $settings;
	private $connections = array();

	private function __construct($db) {
			
	}

	/**
	 * $rs = DB::Load('zest')->Execute($sql,$args)->returnArray();
	 */

	public static function Load($db='default') {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($db);
		}
		return self::$instance->getConnection($db);
	}
		
	public function getConnection($dsn) {
	
		$this->settings = \core\System_Settings::Load()->getSettings('databases');
		
		if(!isset($this->connections[$dsn])) {
			
			switch(strtoupper($this->settings[$dsn]['type'])) {
				case 'MYSQL':
					//create new connection object, and pass in credentials
					$c = new PDOMySql(
						$this->settings[$dsn]['host'],
						$this->settings[$dsn]['name'],
						$this->settings[$dsn]['user'],
						$this->settings[$dsn]['pass']
					);
					
					break;
			}
			
			
			
			//connect, $link passed by reference so is 'returned' with the db link in it
			$link = $c->Connect();

			//create new DMO
			$dmo = new DefaultDMO;

			//pass link to DMO
			$dmo->setConnection($link);

			//keep track of link
			$this->resources[$dsn] = &$c;

			//keep track of DMO
			$this->connections[$dsn] = &$dmo;
			//$this->connections[$dsn] = &$c;
		}

		return $this->connections[$dsn];
	}

	//CleanUp
	//DB::Load('zest')->closeConnections();
	public function closeConnections() {
		foreach($this->resources as $key => $val) {
			$val->DisConnect();
		}
	}
}