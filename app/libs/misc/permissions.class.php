<?php

namespace libs\misc;

class permissions {
	public static $instance;

	private function __construct() {

		\libs\pdo\DB::Load()->Execute("SELECT role_permissions FROM user_role_templates WHERE role_name = 'ADMIN';")->fetchVal($r,'role_permissions');
				
		$a = explode('|',$r);
		
		for($i=0;$i<count($a);$i++) {
			$val = pow(2,$i);
			define($a[$i],$val);
			$admin+=$val;
		}
		
		define('ADMIN',$admin);
		
	}
	
	public static function Load() {
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public function check($required,$test) {
	
		/*
		$c = \core\Users::Load()->isClient();
	
		if($c) {
			$test = "VIEW_OWN";
		}
		//*/
		
		eval('$a = ('.$required.');');
		eval('$b = ('.$test.');');
		
		return ($a & $b);
	}
}