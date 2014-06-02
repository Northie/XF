<?php

namespace filters;

class view extends defaultFilter {

	public function __construct() {
		parent::__construct();
	}


	public function in() {
		$this->package->logExecutedFilter(__CLASS__,$this);
		
		/**
		 * If not domain not active && package is front end then use site not ready template
		 * If user suspended/not active && package is front end then use site not ready template
		 * If user suspended/not active && package is back end then use empty admin theme
		 * 
		 * break to out on each case
		 */
		
		
		$this->FFW(__CLASS__,$this->deps);
	}

	public function out() {
		
		$v = $this->package->getView();
		
		$view = new $v;
		
		foreach($this->package->forms as $key => $val) {
			
			$this->generateForm($key,$val);
		}
		
		$view->setPackage($this->package);
		
		$view->Execute();
		
		$view->renderPage();
		
		$this->RWD(__CLASS__);
	}
	
	private function generateForm($key,$val) {		
		$c = new \libs\forms\Renderer($val);
		
		if($val->submit_label) {
			$c->setSubmitText($val->submit_label);
		}

		switch (true) {
			case ($val->isSubmitted() && $val->isValid()):
				$c->setStage('PASSED');
				break;
			case ($val->isSubmitted() && !$val->isValid()):
				$c->setStage('FAILED');
				break;
			default:
				$c->setStage('NEW');

		}

		$this->package->response->addContent('_form_html_'.$key,$c->getHtml());
	}
}