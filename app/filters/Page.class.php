<?php

namespace filters;

class page extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}


	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		//get pages for domain
		//get active/inactive
		//get subscription type = > free or extended
		
		$pages = \libs\models\Resource::Load('page')->forDomain($_SESSION['domain']['id']);
		
		//print_r($pages);
		
		$this->package->response->addContent('_page_list',$pages);
		
		
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		$this->RWD(__CLASS__);
	}
	
}