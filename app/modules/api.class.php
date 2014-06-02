<?php

namespace modules;

abstract class api {
	
	use \filters\filterManagement {
		\filters\filterManagement::__construct as filterManagementConstruct;
	}
	
	private $filters = false;
	private $newFilter = false;
	protected $activeForm = false;
	public $viewRequired = true;
	public $forms = array();
	
	//protected $use_cache = false;
	protected $use_cache = true;
	
	public function __construct() {
		
		$this->uniq = uniqid();
		
		$this->filterManagementConstruct();
		
		$this->addFilter('\\filters\\default_filter')->asStart();
		$this->addFilter('\\filters\\security')->afterFilter('\\filters\\default_filter');
		
		if($this->use_cache) {
			$this->addFilter('\\filters\\cache')->afterFilter('\\filters\\default_filter');
			$this->addFilter('\\filters\\data')->afterFilter('\\filters\\cache');
		} else {
			$this->addFilter('\\filters\\data')->afterFilter('\\filters\\security');
		}

		$this->addFilter('\\filters\\api')->afterFilter('\\filters\\data');


		//$this->addFilter('\\filters\\view')->afterFilter('\\filters\\data');
		//$this->addFilter('\\filters\\package')->afterFilter('\\filters\\view');		
		
		
		
	}

	public function setActiveForm($form) {
		$this->activeForm = $form;
	}
	
	public function getView() {
		return $this->view;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public final function setRequest($r) {
		$this->request = $r;
	}
	
	public final function setResponse($r) {
		$this->response = $r;
	}

	public function alert() {
		return $this->alert;
	}
	
	public function logExecutedFilter($filter,$class) {
		
		$filter = str_replace("\\filters\\","",$filter);
		$filter = str_replace("filters\\","",$filter);
		
		$this->ExecutedFilters[$filter] = $class;
		
	}
	
	public function cacheUsed() {
		return $this->use_cache;
	}
}
