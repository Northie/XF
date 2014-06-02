<?php

namespace filters;

class default_filter extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * In
	 *
	 * 'Switches' on the parameters
	 */

	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		$this->FFW(__CLASS__);
		
		$this->package->response->request = $this->package->request->getRequest();
		
		if(trim($_POST['_form_name']) == '') {
			$_SESSION['form_data'] = array();
		}
		
	}

	public function out() {
		
		//data filter?
		
		//print_r(debug_backtrace());
		
		$c = $this->package->response->content;
		
		if(is_array($c['store']) && \libs\misc\Tools::isAssoc($c['store'])) {
			foreach($c['store'] as $key => $val) {
				switch(true) {
					case $c['model'][$key]['filter'] == 'toString(*)':
						$c['store'][$key] = $c[$key]['value'];
						break;
				}
			}
		} else {
			for($i=0;$i<count($c['store']);$i++) {
				foreach($c['store'][$i] as $key => $val) {
					switch(true) {
						case $c['model'][$key]['filter'] == 'toString(*)':
							$c['store'][$i][$key] = $c['store'][$i][$key];
							break;
					}
				}	
			}
		}
		
		$this->response->content = $c;
		
		$this->RWD(__CLASS__);
	}
}
