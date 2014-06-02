<?php

namespace controllers;

abstract class defaultController implements iController {
	public function __construct() {
		$this->t1 = microtime(1);
		session_start();
	}
}