<?php

namespace Plugins;

class Plugins {
	private static $instance;
	
	public $plugins = array();
	static $use_plugins = false;
	
	private function __construct() {

	}
	
	public static function RegisterPlugins() {

		//$plugin_path = \core\System_Settings::Load()->get('plugin_path');
		
		$plugin_path = XENECO_PATH."/../app/plugins/";
		
		$files = scandir($plugin_path);		//cache? auto generate? like with class list?
		
		for($i=0;$i<count($files);$i++) {
			if(strpos($files[$i],".plugin.class.php") > -1) {
				require_once($plugin_path.$files[$i]);
				$plugins[] = str_replace(".plugin.class.php","",$files[$i]);
			}
		}
		
		for($i=0;$i<count($plugins);$i++) {
			if(method_exists('\Plugins\\'.$plugins[$i],"RegisterMe")) {
				call_user_func(array('\Plugins\\'.$plugins[$i],"RegisterMe"));
			}
		}
		
		self::$use_plugins = true;
	}
	
	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	public function Register($cls_name,$when) {
		
		$this->plugins[$when][] = $cls_name;
	}
	
	public function DoPlugins($when,$obj,$options=false) {
		
		if(!self::$use_plugins) {
			return true;
		}

		for($i=0;$i<count($this->plugins[$when]);$i++) {
			$tmp = new $this->plugins[$when][$i];
			set_time_limit(30);	//update with settings default???
			$o = $tmp->Initiate($obj,$options,$when);
			set_time_limit(30);	//update with settings default???
			if(!$o) {
				return false;
			}
		}
		
		return true;
	}	
}