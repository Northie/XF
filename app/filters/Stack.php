<?php

/**
 * Description of FilterStack
 *
 * @author Chris
 */

namespace filters;

class Stack {
	protected static $instance;
	
	public $filters = array();
	
	private $usedFilters = array();
	
	public function __construct($f) {
		$this->filters = $f;
	}
	
	public function getFilterList() {
		return $this->filters;
	}
	
	public function stack($name,$filter) {
		//add a filter to the used stack
		$this->usedFilters[$name] = $filter;
	}
	
	public function getStack() {
		//return list of used filters
		return $this->usedFilters;
	}
}

