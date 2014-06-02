<?php

/**
 * Description of FilterStack
 *
 * @author Chris
 */

namespace filters;

class FilterStack {
	protected static $instance;
	protected static $i2 = array();
	protected static $context;
	
	public $filters = array();
	
	private $usedFilters = array();
	
	private function __construct() {
		
	}
	/*
	public static function Load()  {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	//*/
	
	//*
	public static function Load($id)  {
		
		if (!isset(self::$i2[$id])) {
			$c = __CLASS__;
			self::$i2[$id] = new $c;
		}
		self::$context = $id;
		return self::$i2[$id];
	}
	//*/
	public function getFilterList() {
		return $this->filters[self::$context];
	}
	
	public function setFilterList($f) {
		$this->filters[self::$context] = $f;
	}
	
	public function stack($name,$filter) {
		//add a filter to the used stack
		$this->usedFilters[self::$context][$name] = $filter;
	}
	
	public function getStack() {
		//return list of used filters
		return $this->usedFilters[self::$context];
	}
}

