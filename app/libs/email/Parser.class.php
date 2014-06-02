<?php

namespace libs\email;

class Parser {

	private $config = array();

	public function __construct($template_id) {
		$this->td = new \libs\models\template_data;
		$this->template_id = $template_id;
	}

	public function setOpt($set,$element,$value,$provider,$edition) {
		
		$data['feature_name']		=	$set;
		$data['feature_property']	=	$element;
		$data['feature_value']		=	$value;
		$data['template_id']		=	$this->template_id;
		$data['client_id']		=	$_SESSION['client_id'];	//check and make sure!!!
		
		$row_id = $this->td->getRowID($this->template_id,$set,$element,$edition);
		
		if($row_id) {
			
			\libs\models\Resource::Load('template_data')->update($row_id)->set($data);
		} else {
			$data['campaign_id']	=	$edition;
			
			\libs\models\Resource::Load('template_data')->create($data);
		}
	}
	
	public function getOpt($set,$element,$provider,$edition,$forceDefault=false) {
		
		$row_id = $this->td->getRowID($this->template_id,$set,$element,$edition);

		$data['feature_name']		=	$set;
		$data['feature_property']	=	$element;

		$where = array(
		    "id"=>$row_id
		);

		$td = \libs\models\Resource::Load('template_data')->read($where)->getOne();

		if($td['feature_value'] == '' || $forceDefault) {
			//look for last months value
			if($provider == 'nlh') {
				//look for current content
				//echo "$set, $element, $edition";
				
				//$service_id = ????
				
				//\libs\models\Resource::Load('service')->read(array("id"))->getOne();
				$template = \libs\models\Resource::Load('template')->read(array("id"=>$this->template_id))->getOne();
				$config_id = $template['config_id'];
				
				$service = \libs\models\Resource::Load('service')->read(array("config_id"=>$config_id))->getOne();
				$service_id = $service['id'];
				
				$where = array(
					//"service_id"=>$this->template_id,
					"feature_name"=>$set,
					"feature_property"=>$element,
					"campaign_id"=>$edition,
					"service_id"=>$service_id
				);
				
				$td = \libs\models\Resource::Load('service_item_content')->read($where)->getOne();
				
				return $td['feature_value'];
				
				
			}
			
			if($provider == 'client') {
				//look for last months value
				if(date('m') == 1) {
					$edition = (date('Y')-1)."12";
				} else {
					$edition = $edition - 1;
				}
					
				$row_id = $this->td->getRowID($this->template_id,$set,$element,$edition);

				$td = \libs\models\Resource::Load('template_data')->read(array("id"=>$row_id))->getOne();

				return $td['feature_value'];
			}
			
		} else {
			return $td['feature_value'];
		}
	
	}

	public function getNLHOpt($set,$element,$edition) {
		
		$dmo = \libs\models\Resource::Load('service_item_content');
		
		$row_id = $dmo->getRowID($this->template_id,$set,$element,$edition);
		
		if(!$row_id) {
			return '';	//new edition
		}

		/*
		 $where = array(
		 
			"service_id"=>$this->template_id,
			"feature_name"=>$set,
			"feature_property"=>$element,
			"feature_value"=>$edition
		);
		 */
		
		$where['id'] = $row_id;

		$td = $dmo->read($where)->getOne();

		return $td['feature_value'];
	
	}
	
	public function setNLHOpt($set,$element,$value,$edition) {
		
		$data['feature_name']		=	$set;
		$data['feature_property']	=	$element;
		$data['feature_value']		=	$value;
		$data['service_id']		=	$this->template_id;
		
		$dmo = \libs\models\Resource::Load('service_item_content');
		
		$row_id = $dmo->getRowID($this->template_id,$set,$element,$edition);
		
		if($row_id) {
			$dmo->update($row_id)->set($data);
		} else {
			$data['campaign_id']	=	$edition;
			$dmo->create($data);
		}
	}
	
	public function parse($str) {
		
		$t = explode("@@",$str);
		
		for($i=0;$i<count($t)-1;$i++) {

			list($trash,$type,$variable) = preg_split("/(\@[A-Z]+\:)/",$t[$i],null,PREG_SPLIT_DELIM_CAPTURE);

			$parse[] = array('type'=>$type,'cmd'=>$variable);
		}

		//print_r($parse);
		
		//die();
		
		foreach($parse as $todo) {
			$this->find[] = $todo['type'].$todo['cmd']."@@";

			$type = str_replace(array('@',':'),'',$todo['type']);

			switch($type) {
				case 'JSON':
					$cmds = json_decode(str_replace("'",'"',$todo['cmd']),1);
					
					if(is_array($cmds)) {

						foreach($cmds as $c) {
							foreach($c as $key => $val) {
								$this->config[$key] = $val;
							}
						}
					}
					break;
				case 'XML':
					$o = simplexml_load_string($todo['cmd']);
					
					if(is_array($o) || is_object($o)) {
					
						foreach($o as $key => $val) {						
							$this->config[$key]['fields'] = array();

							foreach($val as $field) {
								$this->config[$key]['fields'][] = array(
									'label'=>(string) $field->label,
									'xtype'=>(string) $field->xtype
								);
							}

						}
					}
					
					break;
				case 'IMPORT':
					$c = \libs\pdo\DB::Load('template')->read(array("name"=>$todo['cmd']))->getOne();
					
					$this->parse($c['config']);
					
					break;
					
			}
		}
	}
	
	public function getConfig() {
		return $this->config;
	}
}

?>
