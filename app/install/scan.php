<?php

if(substr(PHP_OS,0,3) == 'WIN') {
	define('DIRECTORY_SEPARATOR','\\');
} else {
	define('DIRECTORY_SEPARATOR','/');
}

$c = dirname(__FILE__);

$p = explode(DIRECTORY_SEPARATOR,$c);

array_pop($p);
array_pop($p);

$c = implode(DIRECTORY_SEPARATOR,$p);

getFiles($c);

function getFiles($path) {
	
	$files = scandir($path);

	foreach($files as $file) {
		if($file[0] == '.') {
			continue;
		}
		
		if(is_dir($path.DIRECTORY_SEPARATOR.$file)) {
			getFiles($path.DIRECTORY_SEPARATOR.$file);
		} else {
			FileRegister::Load()->Register($path.DIRECTORY_SEPARATOR.$file);
		}
		
	}	
}

class FileRegister {
	
	private static $instance;
	
	private $files = array();
	
	public static function Load() {

		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}
	
	public function Register($path) {
		$this->files[] = $path;
	}
	
	public function getFiles() {
		return $this->files;
	}
}

$files = FileRegister::Load()->getFiles();

foreach($files as $file) {
	
	$exts = explode(".",$file);
	
	$ext = array_pop($exts);
	
	if($ext == 'php') {
		$f = file($file);
		
	}
	
	
	
}