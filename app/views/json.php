<?php

namespace views;

class json extends \views\Default_View {
	
	public function __construct() {
		
	}
	
	public function Execute() {
		if(phpversion() >= 5.4) {
			$this->output = json_encode($this->response->getResponse(),JSON_PRETTY_PRINT);	
		} else {
			$this->output = json_encode($this->response->getResponse());
		}
	}
}