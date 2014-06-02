<?php

namespace modules;

interface iAction {
	public static function getFilterList();
	//public function __construct($dataService,$model);
	public function __construct();
	public function Execute();
	public function getData();
}