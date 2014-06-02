<?php

namespace libs\misc;


/**
 * RunTimeSettings
 *
 */

class RunTimeSettings {

	/**
	 * @var object instance of self, see public static function settings()
	 */
	private static $instance;
	
	/**
	 * @var settings held here
	 */
	public $settings = null;

	/**
	 *	private construct, does nothing
 	 */

	private function __contrsuct() {

	}

	/**
	 * @return object an instance of it's self
 	 */
	
	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	public function getValue($key) {
		return $this->settings[$key];
	}
	
	public function setValue($key,$value) {
		$this->settings[$key] = $value;
	}
	
	public function __clone() {

	}
}