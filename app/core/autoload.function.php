<?php

//namespace core;



function __autoload($cls) {
	
	$file = \app\core\System_Settings::Load()->getFileForClass($cls);

	if(MODE != 'DEV') {
		
		//if mode is stage or live

		if($file != '') {
			//include the file
			require_once($file);
		} else {
			//or make a fake class
			Zest_Tools::MakeClass($class_name);
		}
	
	} else {

		//if mode is dev

		//scan files and recompile file list
		Zest_Tools::CompileFiles();


		//include the file list again;		
		\app\core\System_Settings::Load()->includeClassList($cls);
		
		
		$file = \app\core\System_Settings::Load()->getFileForClass($cls);

		if($this->classList[$class_name] != '') {
			//include the file
			return $this->classList[$class_name];
		} else {
			//or make a fake class
			Zest_Tools::MakeClass($class_name);
			return false;
		}
	}
}
/*
class System_Settings {

	public static $instance;
	
	private $classList = array();

	private function __construct() {
		$this->includeClassList();
	}

	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function getFileForClass($class_name) {		
		return $this->classList[$class_name];
	}
	
	public function includeClassList() {
		include(ZEST_PATH.'class-list.php');
		$this->classList = $classlist;	
	}
}

class Zest_Tools {
	public static function MakeClass($class_name) {
		
		$str = '
		class '.$class_name.' extends Default_Action {


			public function __construct() {
				parent::construct();
			}

			public function Execute() {

			}
		}
		';

		eval($str);
	}
	
	public static function CompileFiles() {
		
		$path = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'xeneco-svn'.DIRECTORY_SEPARATOR.'trunk'.DIRECTORY_SEPARATOR;
		
		$path = ZEST_PATH;
		
		$ignore = array('.htaccess', 'error_log', 'cgi-bin', 'php.ini', '.ftpquota', '.svn');
		
		$dirTree = self::getDirectory($path, $ignore); 

		foreach($dirTree as $dir => $files) {
			foreach($files as $file) {
				$a = $dir.DIRECTORY_SEPARATOR.$file;
				$a = str_replace('/',DIRECTORY_SEPARATOR,$a);
				$a = str_replace('\\',DIRECTORY_SEPARATOR,$a);

				$a = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$a);

				$all[] = $a;
			}
		}

		foreach($all as $file) {
			$d = self::getContexts($file);

			for($i=0;$i<count($d['classes']);$i++) {

				$class = $d['classes'][$i];

				$lines.='$classlist["'.$prefix.$d['namespaces'][0].($d['namespaces'][0] == '' ? '' : '\\').$class.'"] = "'.$file.'";'."\n";
			}

			for($i=0;$i<count($d['interfaces']);$i++) {
				$interface = $d['interfaces'][$i];

				$lines.='$classlist["'.$prefix.$d['namespaces'][0].($d['namespaces'][0] == '' ? '' : '\\').$interface.'"] = "'.$file.'";'."\n";
			}
		}
	}
	

	public static function getDirectory($path = '.', $ignore = '') {
		$dirTree = array ();
		$dirTreeTemp = array ();
		$ignore[] = '.';
		$ignore[] = '..';

		$dh = @opendir($path);

		while (false !== ($file = readdir($dh))) {

			if (!in_array($file, $ignore)) {
				if (!is_dir("$path/$file")) {
					$dirTree["$path"][] = $file;
				} else {
					$dirTreeTemp = self::getDirectory("$path/$file", $ignore);
					if (is_array($dirTreeTemp)) {
						$dirTree = array_merge($dirTree, $dirTreeTemp);
					}
				}
			}
		}
		closedir($dh);
		return $dirTree;
	}
	
	public static function getContexts($path) {
	
		$c = file_get_contents($path);

		$a = token_get_all($c);

		for($i=0;$i<count($a);$i++) {

			if(strtolower($a[$i][1]) == 'namespace') {
				$j = 1;
				$namespace = '';
				while(true) {
					if(trim($a[$i+$j][1]) == '') {
						if($j != 1) {
							break;
						}
					}

					$namespace.=$a[$i+$j][1];
					$j++;
				}
				$namespaces[] = trim($namespace);
				$i+=$j;
			}

			if(strtolower($a[$i][1]) == 'class') {
				$j = 1;
				$class='';
				while(true) {
					if(trim($a[$i+$j][1]) == '') {
						if($j != 1) {
							break;
						}
					}

					$class.=$a[$i+$j][1];
					$j++;
				}

				$classes[] = trim($class);
				$i+=$j;
			}

			if(strtolower($a[$i][1]) == 'interface') {
				$j = 1;
				$interface='';
				while(true) {
					if(trim($a[$i+$j][1]) == '') {
						if($j != 1) {
							break;
						}
					}

					$interface.=$a[$i+$j][1];
					$j++;
				}

				$interfaces[] = trim($interface);
				$i+=$j;
			}
		}

		return array('namespaces'=>$namespaces,'classes'=>$classes,'interfaces'=>$interfaces);
	}

}
*/