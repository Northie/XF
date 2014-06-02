<?php

namespace libs\models;
use libs\pdo;

abstract class Model implements iModel {
	
	protected $db = false;
	protected $data = array();
	protected $fields = array();

	const READ_LIST = 0;
	const READ_DETAILS = 1;
	
	const OFFSET = 0;
	const LIMIT = 50;

	public function __construct($schema) {
		//$user = \core\Request::Load()->get('user');
		//$this->operator = $user['unid'];
		$this->operator = \core\Users::Load()->get('id');
		$this->db = \libs\pdo\DB::Load();
		$this->schema = $schema;
		//$this->resource = str_replace(__NAMESPACE__."\\","",__CLASS__);
	}
	
	public function getDefinition() {
		return array_keys($this->model);
	}
	
	public function newUnid() {
		return $this->db->Execute("SELECT UUID() as `unid`")->returnVal('unid');
	}
	
	public function commonReturn() {
		return $this->db;
	}
	
	public function getData($key=false) {
		if($key) {
			return $this->data[$key];
		}
		
		return $this->data;
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function setModel($model) {
		foreach($model as $col => $data) {
			$this->model[$this->resource.".".$col] = $data;
		}
	}
	
	protected function Integrate($data=array(),$overwrite=false) {
		
		$this->data = array();
		
		foreach($this->model as $key => $val) {
			
			if(!array_key_exists($key,$this->data) || $overwrite) {
				$this->data[$key] = $data[$key];
			}
			
			$key = str_replace($this->resource.".","",$key);
			
			if(!array_key_exists($key,$this->data) || $overwrite) {
				$this->data[$key] = $data[$key];
			}
			
		}
		
		if(array_key_exists('unid',$this->data) && !$this->data['unid']) {
			$this->data['unid'] = $this->newUnid();
		}
	}

	public final function beginTransaction() {
		pdo\DB::Load()->conn->beginTransaction();
	}

	public final function commit() {
		pdo\DB::Load()->conn->commit();
	}

	public final function rollBack() {
		pdo\DB::Load()->conn->rollBack();
	}

	public function create($data) {
	
		$this->integrate($data);		
		$cols = $this->getDefinition();
		
		//print_r($data);
		//print_r($cols);
		
		foreach($cols as $col) {
			if($this->data[$col]) {				
				$sql_p[] = $col;
				
				$args[$col] = $this->data[$col];
			}
			
			$col = str_replace($this->resource.".","",$col);
			
			if($this->data[$col]) {				
				$sql_p[] = $col;
				
				$args[$col] = $this->data[$col];
			}
		}
		
		$sql = "
			INSERT INTO
				`".$this->resource."` (
					`".implode('`, `',$sql_p)."`
				) VALUES (
					:".implode(', :',$sql_p)."
				)
			;
		";
		
		$id = $this->db->Execute($sql,$args)->returnLastInsertID();
		$unid = $args['unid'];
		
		$this->data = array("id"=>$id,"unid"=>$unid);
	
		return $this->db;
	}
	
	public function update($id,$col='id',$limit=1) {
		
		$this->where = array("col"=>$col,"val"=>$id);
		$this->limit = (int) $limit;
	
		//return $this->db;
		return $this;
	}
	
	public function read($where=false,$order=false,$mode='AND') {
		
		if(is_array($where)) {
			foreach($where as $key => $val) {
				
				if(strpos($key,".") > -1) {
					$w[] = $key." = :".str_replace(".","_",$key);
					$args[str_replace(".","_",$key)] = $val;
				} else {
					$w[] = $this->resource.".".$key." = :".$key;
					$args[$key] = $val;
				}
				
				
				
			}
			
			$where = "WHERE ".implode(" ".$mode." ",$w);
			
		} else {
			$where = '';
		}
		
		$join = '';
		$joins = array();
		
		$joined = array();
		
		foreach($this->model as $field => $details) {
			if($details['join']) {
				
				if(!$joined[$details['join']['table']]) {

					$field_corrected = explode("as",$field);
					
					$field_corrected = trim($field_corrected[0]);
					
					$joined[$details['join']['table']] = true;
				
					$this->expandModel(Resource::Load($details['join']['table'])->getModel(true));

					$joins[] = "
						INNER JOIN
							".$details['join']['table']."
							ON
							".$details['join']['table'].".".$details['join']['column']." = ".$field_corrected."
					";
				}
			}
		}
		
		$join = implode("\n",$joins);

		
		
		$fields = array();
		
		if(count($this->fields) > 0) {
			$cols = array_keys($this->model);
			foreach($cols as $col) {
				
				$tcol = str_replace($this->resource.".","",$col);
				
				if(in_array($tcol, $this->fields)) {
					$fields[] = $col;
				}
			}
			
			if(count($fields) > 0) {
				$f = implode(", ",$fields);
			} else {
				$f = "[fields]";
			}
			
			$this->fields = array();
			
		} else {
			$f = "[fields]";
		}
		
		$sql = "
			SELECT
				".$f."
			FROM
				`".$this->resource."`

			".$join."

			".$where."
			
			".$order."
			
			".$this->getLimits()."

			;
		";
		
		$this->sql = $sql;
		$this->args = $args;
		
		return $this;
	}
	
	public function setFields($fields) {
		$fields = explode(",",$fields);
		$this->fields = array_map('trim', $fields);
	}
	
	public function search($mode='OR') {

		$cols = array_keys($this->model);
		
		$fields = array();
		
		foreach($cols as $col) {
			
			$tcol = str_replace($this->resource.".","",$col);
			
			if(($mode == 'AND' && $_GET[$tcol]) || $mode == 'OR') {
				//$p[] = "$col LIKE :$tcol";

				if(in_array($tcol, $this->fields)) {
					$fields[] = $tcol;
				}
				
				//$args[$tcol] = "%".($_GET[$tcol] ? $_GET[$tcol] : $_GET['q'])."%";
				
				$arg = ($_GET[$tcol] ? $_GET[$tcol] : $_GET['q']);
				
				if($arg != '') {
					$p[] = "$col LIKE :$tcol";	
					$args[$tcol] = "%".$arg."%";
				}

				
			}
			

		}
		
		if(count($fields) > 0) {
			$fields = implode(", ",$fields);
			$this->fields = array();
		} else {
			$fields = "*";	
		}
		
		$sql = "
			SELECT
				".$fields."
			FROM
				`".$this->resource."`
			WHERE
				".implode(" ".$mode." ",$p)."
			".$this->getLimits()."
		";
		
		$this->args = $args;
		$this->sql = $sql;
		
		return $this;
		
	}
	
	public function destroy($id,$col='id',$limit=1) {
	
		$limit = (int) $limit;
	
		$sql = "
			DELETE FROM
				`".$this->resource."`
			WHERE
				".$col." = :".$col."
			LIMIT
				".$limit."
			;
		";
		
		$args = [
		    $col=>$id
		];
		
		$this->data['count'] = $this->db->Execute($sql,$args)->returnNumAffectedRows();

		
		return $this;
	}
	
	public function getMany($mode=0) {
		
		$sql = $this->sql;
		$args = $this->args;
		
		$l = array_keys($this->model);
		
		$sql = str_replace("[fields]",implode(", ",$l),$sql);
		
		$args = is_array($args) ? $args : array();
		
		//$data = $this->db->Execute($sql,$args)->returnArray();
		
		//return (array) $data;
		
		//*/
		$this->db->Execute($sql,$args)->fetchArray($rs)->fetchNumAffectedRows($c);
		
		$sql_c = "SELECT COUNT(*) as total FROM (".str_replace([$this->getLimits(),';'],'',$sql).") a";
		
		$this->db->Execute($sql_c,$args)->fetchRow($rsc);
		
		return array(
		    "data"=>$rs,
		    "meta"=>array(
			"count"=>$c,
			"limit"=>$this->limit,
			"offset"=>$this->offset,
			"total"=>$rsc['total']
		    )
		);
		
		//*/
		/*
		foreach($this->model as $key => $val) {
			$a = explode(" as ",$key);
			if($a[1]) {
			//if(count($a) > 1) {
				$alias = trim($a[1]);
			} else {
				list($trash,$alias) = explode(".",trim($a[0]));	
			}
			//$this->model[$key]['store_alias'] = $alias;
			
			$return_model[$alias] = $val;
		}
		//*/
		//return array("model"=>$return_model,"store"=>$data);
		return $data;
	}
	
	public function getOne($mode=1) {
		
		$sql = $this->sql;
		$args = $this->args;
		
		$l = array_keys($this->model);
		
		$sql = str_replace("[fields]",implode(", ",$l),$sql);
		
		$args = is_array($args) ? $args : array();
		
		$data = (array) $this->db->Execute($sql,$args)->returnRow();
		/*
		foreach($this->model as $key => $val) {
			$a = explode(" as ",$key);
			if($a[1]) {
			//if(count($a) > 1) {
				$alias = trim($a[1]);
			} else {
				list($trash,$alias) = explode(".",trim($a[0]));	
			}
			$return_model[$alias] = $val;
		}
		//*/
		//return array("model"=>$return_model,"store"=>$data);
		return $data;
	}
	
	public function getModeled() {
		$model = array();
		
		foreach($this->model as $key => $val) {
			$a = explode(" as ",$key);
			if($a[1]) {
			//if(count($a) > 1) {
				$alias = trim($a[1]);
			} else {
				list($trash,$alias) = explode(".",trim($a[0]));	
			}
			//$this->model[$key]['store_alias'] = $alias;
			
			$model[$alias] = $val;
		}
		
		return $model;
	}
	
	public function set($data) {

		$this->integrate($data);		
		$cols = $this->getDefinition();
		$args = array();
			
		foreach($cols as $col) {
			
			if(isset($this->data[$col])) {
				$sql_p[] = $col." = :".$col;
				$args[$col] = $this->data[$col];
			}
			
			$col = str_replace($this->resource.".","",$col);
			
			if(isset($this->data[$col])) {
				$sql_p[] = "`".$col ."` = :".$col;
				$args[$col] = $this->data[$col];
			}
			
		}
		
		$sql = "
			UPDATE
				`".$this->resource."`
			SET
				".implode(", ",$sql_p)."
			WHERE
				`".$this->where['col']."` = :_".$this->where['col']."
			LIMIT
				".$this->limit."
			;
		";
		
		//echo $sql,print_r($args,1);
		
		$args['_'.$this->where['col']] = $this->where['val'];
		
		$this->data['count'] = $this->db->Execute($sql,$args)->returnNumAffectedRows();

		return $this;
	}
	
	protected function expandModel($new_model) {
		
		$m = array();
		
		foreach($new_model as $key => $val) {
			
			if(strpos($key," as ") > -1) {
				;
			} else {
			
				$m[$key." as ".str_replace(".","_",$key)] = $val;
			}
		}
		
		$this->model = array_merge($this->model,$m);
	}
	
	public function forResource($parent,$child,$parent_id,$q=false,$mode='OR') {
		
		$link_table = $child."_".$parent;
		
		if(!dataStructure::Load()->table_exists($link_table)) {
			$link_table = $parent."_".$child;
		}
	
		
		if($_GET['fields'][$child]) {
			$this->setFields($_GET['fields'][$child]);
			
			foreach($this->fields as $field) {
				if(dataStructure::Load()->field_exists($child,$field)) {
					$fields[] = $field;
				}
			}
			
			$this->fields = array();
			
			$fields = implode(", ",$fields);
			
		} else {
			$fields = "*";
		}
		
		if(dataStructure::Load()->table_exists($link_table)) {
		
			$sql = "
				SELECT
					".$child.".".$fields."
				FROM
					`".$child."`
				INNER JOIN
					`".$link_table."`
					ON
					`".$link_table."`.`".$child."_id` = ".$child.".id
					AND
					`".$link_table."`.`".$parent."_id` = :id
			";
		} else {
			$sql = "
				SELECT
					".$child.".".$fields."
				FROM
					`".$child."`
				WHERE
					`".$parent."_id` = :id
			";
		}
	
		
		//*
		if($q) {
			
			//var_dump($this->schema[$child]);
			
			$cols = array_keys($this->schema[$child]);

			$fields = array();

			foreach($cols as $col) {

				$tcol = str_replace($child.".","",$col);

				if(($mode == 'AND' && $_GET[$tcol]) || $mode == 'OR') {
					//$p[] = "$child.$col LIKE :$child_$tcol";
					//$p[] = $child.".".$col." LIKE :".$child."_".$tcol;

					//if(in_array($tcol, $this->fields)) {
					//	$fields[] = $tcol;
					//}
					
					$arg = ($_GET[$tcol] ? $_GET[$tcol] : $_GET['q']);
					
					if($arg != '') {
						$p[] = $child.".".$col." LIKE :".$child."_".$tcol;
						$args[$child."_".$tcol] = "%".$arg."%";
					}
				}
			}
			
			$sql.=" AND (".implode(" ".$mode." ",$p).") ";
		}
		//*/
		
		
		
		$sql_c = $sql;
		
		$sql = $sql.$this->getLimits();
		
		$args["id"] = $parent_id;
		
		//echo $sql,print_r($args,1);

		$this->db->Execute($sql,$args)->fetchArray($rs)->fetchNumAffectedRows($c);
		
		$sql_c = "SELECT COUNT(*) as total FROM (".$sql_c.") a";
		
		$this->db->Execute($sql_c,$args)->fetchRow($rsc);
		
		return array(
		    "data"=>$rs,
		    "meta"=>array(
			"count"=>$c,
			"limit"=>$this->limit,
			"offset"=>$this->offset,
			"total"=>$rsc['total']
		    )
		);
	}
	
	public function getLimits() {
		
		$this->offset = $_GET['offset'] > 0 ? (int) $_GET['offset'] : self::OFFSET;
		$this->limit = $_GET['limit'] > 0 ? (int) $_GET['limit'] : self::LIMIT;
		
		if($_GET['limit'] < 0) {
			return "";
		}
		
		return "LIMIT ".$this->offset.", ".$this->limit."";
		
	}
}
