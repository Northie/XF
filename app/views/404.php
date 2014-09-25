<?php

namespace views;

class FileNotFound extends \views\Default_View {

	public function __construct() {
		$this->template = "404.tpl.html";
	}

	public function Execute() {
		$obj = $this->app;
	}

}
