<?php
namespace libs\rackspace;

trait api {
	public function __construct() {
		$s = \core\System_Settings::Load()->getSettings('rackspace','api');
		
		$this->rackspace = new \OpenCloud\Rackspace($s['UK'], ['username'=>$s['username'],'apiKey'=>$s['apikey']]);
	}
}