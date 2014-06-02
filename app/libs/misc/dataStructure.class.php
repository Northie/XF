<?php

namespace libs\misc;

class dataStructure {

	private static $instance;

	private function __construct() {
		$this->db = \libs\pdo\DB::Load();
		$this->generate();
	}
	
	public static function Load() {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	
	public function table_exists($table,$retry=true) {
		$e = $this->structure[$table] ? true : false;
		
		if($e) {
			return true;
		} else {
		
			if($retry) {		
				$this->generate();
				return $this->table_exists($table,false);
			} else {
				return false;
			}
		}
	}
	
	public function field_exists($table,$field,$retry=true) {
		$e = $this->structure[$table][$field] ? true : false;
		
		if($e) {
			return true;
		} else {
			if($retry) {
				$this->generate();
				return $this->field_exists($table,$field,false);
			} else {
				return false;
			}
		}
	}
	
	private function generate() {
		$this->db->Execute("SHOW TABLES")->fetchArray($table_data);
		
		foreach($table_data as $tables) {
			
			foreach($tables as $table) {

				$this->db->Execute("DESCRIBE `".$table."`;")->fetchArray($fields);

				foreach($fields as $field) {
					$field = $field['Field'];
					$this->structure[$table][$field] = true;
				}
			}
		}
	}

}