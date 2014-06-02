<?php

namespace modules;

abstract class Authenticated_Action extends Default_Action {

	protected $user = false;

	public static function getFilterList() {
		
		//self::addFilter('filters\context');
		//self::addFilter('filters\security');
		//self::addFilter('filters\user');
		//self::addFilter('filters\permission');
		//self::addFilter('filters\module');
		
		//security filter added to default
		
		self::addFilter('filters\user');
		self::addFilter('filters\permission');
		
		return self::$filters;
	}
	
	public function __construct() {
		$this->user = \core\Users::Load()->getUnid();
	}

}