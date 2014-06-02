<?php

namespace core;

class System_Settings {

	public static $instance;
	
	private $classList = array();
	private $settings = array();	

	private $path_to_settings;


	private function __construct() {
		$this->includeClassList();
		$this->includeSettings();
	}

	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	public function includeClassList() {
		include(XENECO_PATH.'class-list.php');
		$this->classList = $classlist;	
	}
	
	public function includeSettings() {
		include(XENECO_PATH.'settings.static.php');
		$this->settings = $settings;
	}

	public function getFileForClass($class_name) {		
		return $this->classList[$class_name];
	}
	
	public function addSetting($key,$value) {
		$this->settings[$key] = $value;
	}
	
	public function getSettings($key1=false,$key2=false) {
		if($key1) {
			
			if($key2) {
				return $this->settings[$key1][$key2];	
			}
		
			return $this->settings[$key1];
		}
		
		return $this->settings;
	}
}
