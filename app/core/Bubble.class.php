<?php

namespace core;

class Bubble extends \Exception {
	
	private $bubble = true;
	
	public function __construct($event,$value,$bubble=true)  {
		parent::__construct();
		
		$this->event = $event;
		$this->value = $value;
		if(!$bubble) {
			$this->preventBubbling();
		}
		
	}
	
	public function bubblingPrevented() {
		return $this->bubble;
	}
	
	public function preventBubbling() {
		$this->bubble = false;
	}

	public function enableBubbling() {
		$this->bubble = true;
	}

	public function getEvent() {
		return $this->event;
	}
	
	public function getAction() {
		return $this->action;
	}

}