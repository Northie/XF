<?php

namespace packages;

trait authenticator {
	public function __construct() {
		
		$api = new \core\API("DELETE","_cache");
		$api->setContext($this->uniq);
		$api->doRequest();
		
		$this->addFilter('\\filters\\user')->afterFilter('\\filters\\security');
		$this->addFilter('\\filters\\permission')->afterFilter('\\filters\\user');
		
		$this->admin_base = \core\System_Settings::Load()->getSettings('ADMIN_BASE');
		
	}
}