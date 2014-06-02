<?php

namespace core;

class System_Log {
	
	public static $instance;

	private $log_path = '';

	private function __construct() {
		$this->log_path = dirname(__FILE__).'/../cms.log';
		
		if(!file_exists($this->log_path)) {
			file_put_contents($this->log_path,'');
		}
		
		$this->fp = fopen($this->log_path,'a');
	}

	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	public function addEntry($text) {
		$line = date('r');
		$line.="\t";
		$line.=$_SERVER['REMOTE_ADDR'];
		$line.="\t";
		$line.=$_SERVER['QUERY_STRING'];
		$line.="\t";
		$line.=$text;
		$line.="\n";
		fwrite($this->fp,$line);
	}
	
	public function __destruct() {
		fclose($this->fp);
	}
}