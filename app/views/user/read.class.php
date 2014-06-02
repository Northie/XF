<?php

namespace views\user;

class read extends \views\Default_View {
	
	public function __construct() {
		$this->template = "user-read.tpl.html";
	}
	
	public function Execute() {
		$obj = $this->app;
		
	}
}
