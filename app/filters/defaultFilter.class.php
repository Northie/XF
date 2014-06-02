<?php

namespace filters;

abstract class defaultFilter implements iFilter {

	static $request;
	static $response;
	static $package;
	static $stack;
	static $package_uniq;
	protected $deps = array();
	
	public function __construct() {
		
	}

	public static function start($package,$stack) {
		
		self::$package_uniq = $package->uniq;
		
		self::$stack[self::$package_uniq] = $stack;
		
		$cls =  self::getNext();
		
		self::$package = $package;
		
		$o = new $cls;
		
		FilterStack::Load(self::$package_uniq)->stack($cls,$o);
		FilterStack::Load(self::$package_uniq)->package = $package;
		$o->setPackage(FilterStack::Load(self::$package_uniq)->package);
		$o->in();
		//$o->out();
	}

	private function getNext($cls=false) {
		//get the next filter in the list
		//$arr = FilterStack::Load()->filters;

		$arr = self::$stack[self::$package_uniq]->filters;
		
		if(!$cls) {
			$i = -1;
		} else {
			$i = array_search("\\".$cls,$arr);
		}
		
		if($arr[$i+1]) {
			return $arr[$i+1];
		}
		
	}
	
	private function getPrev($cls) {
		//$arr = FilterStack::Load()->filters;
		$arr = self::$stack[self::$package_uniq]->filters;
		
		$i = array_search("\\".$cls,$arr);
		
		if($arr[$i-1]) {
			//get used filters
			//$filters = FilterStack::Load()->getStack();
			$filters = self::$stack[self::$package_uniq]->getStack();
			return $filters[$arr[$i-1]];
		}
	}
	

	protected function FFW($cls,$deps=false) {
		
		$next = $this->getNext($cls);
		
		if($next) {
			
			$o = new $next;
			
			self::$stack[self::$package_uniq]->stack($next,$o);	
			
			$o->setPackage(FilterStack::Load(self::$package_uniq)->package);
			
			$o->in();
			
			
		} else {
	
			$this->out($deps);
		}	
	}
	
	protected function RWD($cls) {
		
		$prev = $this->getPrev($cls);
		
		if($prev) {
			$prev->out();
		} 
	}
	
	protected function setRequest($r) {
		$this->request = $r;
	}
	
	protected function setResponse($r) {
		$this->response = $r;
	}

	protected function setPackage($p) {
		$this->package = $p;
	}
	
}