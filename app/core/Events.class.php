<?php

namespace core;

class Events {

	private $events = array();
	
	public static $instance;

	public static function Load() {
		if (!isset(self::$instance)) {
			self::$instance = new \core\Events;
		}
		return self::$instance;
	}

	public function addEvent($event,$value=true) {
		$this->events[$event] = $value;
		
		throw new \core\Bubble($event,$value);
	}
	
	public function addEventIfNotExists($event,$value=true) {
		if(!$this->eventExists($event)) {
			$this->addEvent($event,$value);
		}
	}
	
	public function eventExists($event) {
		if(array_key_exists($event,$this->events)) {
			return true;
		}
		
		return false;
	}
	
	public function getEvent($event) {
		if($this->eventExists($event)) {
			return $this->events[$event];
		}
		
		return null;
	}
	
	public function getAllEvents() {
		return $this->events;
	}

}