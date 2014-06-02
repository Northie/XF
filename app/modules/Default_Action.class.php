<?php

namespace modules;

abstract class Default_Action implements iAction {

	protected $status = -1;
	protected $dataService = false;
	protected $model = false;

	const NOT_READY = 0;
	const NOT_VALID = 1;
	const COMPLETE = 2;
	
	public static $filters = array(
		'filters\default_filter',
		'filters\security',
		'filters\data',
		'filters\view',
		'filters\API'
	);

	public static function getFilterList() {
		
		return self::$filters;
	}
	
	public static function addFilter($filter) {				
		array_splice(self::$filters,2,0,array($filter));
	}
	
	public function __construct($dataService,$model) {

		$this->filterManagementConstruct();
		
		$this->addFilter('\\filters\\default_filter')->asStart();
		$this->addFilter('\\filters\\security')->afterFilter('\\filters\\default_filter');
		$this->addFilter('\\filters\\data')->afterFilter('\\filters\\security');
		$this->addFilter('\\filters\\view')->afterFilter('\\filters\\data');
		$this->addFilter('\\filters\\package')->afterFilter('\\filters\\view');		
		
		
		$this->dataService = $dataService;
		$this->model = $model;
	}
	
	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public final function setRequest($r) {
		$this->request = $r;
	}
	
	public final function setResponse($r) {
		$this->response = $r;
	}
	
	public function getModel() {
		return $this->model->getModel();
	}

}