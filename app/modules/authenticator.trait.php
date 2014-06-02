<?php

namespace modules;

trait authenticator {
	public function __construct() {
		
		$this->addFilter('\\filters\\apiuser')->afterFilter('\\filters\\security');
		$this->addFilter('\\filters\\permission')->afterFilter('\\filters\\apiuser');
		
	}
}