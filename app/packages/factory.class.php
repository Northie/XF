<?php
namespace packages;

class Factory {
	public static function Build($cls) {
		
		$c = new $cls;
		
		return $c;
	}
}