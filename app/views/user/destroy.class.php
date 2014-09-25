<?php

namespace views\user;

class destroy extends \views\Default_View {
	
	public function __construct() {
		$this->template = "user-destroy.tpl.html";
	}
	
	public function Execute() {
		$obj = $this->app;
		
	}
}
