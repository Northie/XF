<?php

namespace libs\models;

class Resource {
	private static $instance;
	private $resources = array();

	private function __construct() {
		include('model.cache.php');
		$this->model = $schema;
		$this->schema = $schema;
	}
	public static function Load($resource) {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance->useResource($resource);
	}
	
	public function useResource($r) {
		
		if(!$this->resources[$r]) {
			$c = __NAMESPACE__."\\".$r;
			$this->resources[$r] = new $c($this->schema);
			$this->resources[$r]->setModel($this->model[$r]);
		}
		
		return $this->resources[$r];
	}
}