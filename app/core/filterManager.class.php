<?php

namespace core;

class filterManager {
	
	public function __construct($package,$filter_list) {
		$this->package = $package;
		$this->filters = $filter_list;
	}

	
	public function Execute() {
		//*
		
		\filters\FilterStack::Load($this->package->uniq)->setFilterList($this->filters);
		
		$stack = new \filters\Stack($this->filters);
		
		\filters\defaultFilter::start($this->package,$stack);
		
		//$f = new \filters\defaultFilter;
		//$f->start($this->package,$stack);
		
		 //*/
	}
	
	public function Execute2() {
		
	}
}