<?php

namespace filters;

trait filterManagement {
	public function __construct() {
		//$this->filters = new SplDoublyLinkedList;
		$this->filters = new \libs\misc\DoublyLinkedList;
	}

	public function getFilters() {
		return $this->filters;
		//return $this;
	}
	
	public function getFilterList($r=false) {
		if($r) {
			return $this->filters->exportBackward();
		}
		return $this->filters->exportForward();
			
	}
	
	public function addFilter($filter) {
		$this->newFilter = $filter;
		return $this;
	}
	
	protected function asStart() {
		try {
			if($this->newFilter) {
				$this->filters->insertFirst($this->newFilter);
				$this->newFilter = false;
			} else {
				throw new \Exception("New Filter Not Set");
			}
		} catch(\Exception $e) {
			echo $e->getMessage();
			die();
		}
	}
	
	protected function afterFilter($key) {
		
		try {
			if(!$this->filters) {
				throw new Exeception("Filters Not Initialised");
			} else {
				try {
					if($this->newFilter) {
						$this->filters->insertAfter($key, $this->newFilter);
						$this->newFilter = false;
					} else {
						throw new \Exception("New Filter Not Set");
					}
				} catch(\Exception $e) {
					echo $e->getMessage();
					die();
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			die();			
		}
	}
	
	protected function beforeFilter($key) {
		try {
			if(!$this->filters) {
				throw new Exeception("Filters Not Initialised");
			} else {
				try {
					if($this->newFilter) {
						$this->filters->insertBefore($key, $this->newFilter);
						$this->newFilter = false;
					} else {
						throw new \Exception("New Filter Not Set");
					}
				} catch(\Exception $e) {
					echo $e->getMessage();
					die();
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			die();			
		}		
	}
	
}
