<?php

namespace libs\xml;

class xml2json {
	
	private $xml;
	
	public function __construct($xml=false) {
		$this->xml = $xml;
	}
	
	public function getFromURL($url) {
		$this->xml = file_get_contents($url);
	}
	
	public function getJSON() {
		if($this->xml) {
			$doc = simplexml_load_string($this->xml);
			return json_encode($doc);
		}
	}
}
