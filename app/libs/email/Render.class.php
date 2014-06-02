<?php

namespace libs\email;

class Render {
	
	public function __construct($id) {
		$this->template = \libs\models\Resource::Load('template')->read(array("id"=>$id))->getOne();
		
		$this->str = $this->template['config_config']."\n".$this->template['additional_configuration'];
	}
	
	public function Execute($edition) {
		
		$parser = new Parser($this->template['id']);
		$parser->parse($this->str);
		
		$config = $parser->getConfig();
	
		foreach($config as $set => $details) {
			$provider = $details['meta']['source'] ? $details['meta']['source'] : 'client';

			foreach($details['fields'] as $field) {
				$value = $parser->getOpt($set,$field['label'],$provider,$edition);

				if($details['meta']['multiple']) {
					$value = json_decode($value,1);
				}
				
				if($field['xtype'] == 'feed') {
					$rs = explode("|",$value);
					$rs = $rs[1];
					if(count($rs) > 1) {
						$rs = implode("|",$rs);
					}
					
					$value = json_decode($rs,1);
					
					$data[$set] = $value;
				} else {
					$data[$set][$field['label']] = $value;	
				}
				
				$config_s[$set][$field['label']] = $field;
				
			}
		}
		
		$this->data = $data;
		
		$c = str_replace($parser->find,"",$this->template['template']);
		
		$t1 = explode ("}",$c);
		foreach($t1 as $p) {
			$t2 = explode("{",$p);
			
			$tags[] = "{".$t2[1]."}";
		}
		
		$last = array_pop($tags);
		
		if($last != '{}') {
			$tags[] = $last;
		}
		
		foreach($tags as $tag) {
			
			$tag_find[] = $tag;
			
			$tag = trim($tag,"{}");
			
			list($feature,$element,$args) = explode("|",$tag);
			
			$source = $config[$feature]['meta']['source'] ? $config[$feature]['meta']['source'] : 'client';
			
			$d = $data[$feature][$element];
			
			switch(true) {
				//case 'RSS Feed':
				//	$tag_replace[] = $this->rssToHtml($d);
				//	break;
				case ($config_s[$feature][$element]['xtype'] == 'image'):
					$tag_replace[] = $this->image($d,$args);
					break;
				case ($element == 'Zed Index Region'):
					$tag_replace[] = $this->zooplaFeed($d);
					break;
				default:
					$tag_replace[] = $d;
			}
		}
		
		$c = str_replace($tag_find,$tag_replace,$c);
		
		$this->content = $c;
		
		$m1 = explode("`]]",$c);

		for($i=0;$i<count($m1);$i++) {

			$m2 = explode("[[!",$m1[$i]);
			array_shift($m2);

			$m2 = $m2[0];

			$m_find[$i] = "[[!".$m2."`]]";

			list($set,$tpl) = explode("?",$m2);

			$tpl = trim(str_replace("&tpl=`","",$tpl));

			$placeholders = explode("]]",$tpl);
			
			$ids = explode("[",$set);

			$set = $ids[0];
			
			if(is_array($data[$set])) {
				$sample_field = key($data[$set]);
			}
			
			$range = array();
			
			if(count($ids) == 2) {
				
				$ids[1] = trim($ids[1],"[]");
				
				switch(true) {
					case (strpos($ids[1],"!") > -1) :
						$exclude = str_replace("!","",$ids[1]);
						for($x=0;$x<count($data[$set][$sample_field]);$x++) {
							if($x != $exclude) {
								$range[] = $x;
							}
						}
						break;
					case (strpos($ids[1],"-") > -1 ):
						list($start,$end) = explode("-",$ids[1]);
						for($x=0;$x<count($data[$set][$sample_field]);$x++) {
							if($x >= $start && $x <= $end) {
								$range[] = $x;
							}
						}
						break;
					default:
						$range = array($ids[1]);
						break;
				}
			} else {
				$range = range(0,count($data[$set][$sample_field])-1);
			}

			//*
			$section = "";
			for($k=0;$k<count($data[$set][$sample_field]);$k++) {	
				if(in_array($k,$range)) {
					$ph_find = $ph_replace = array();
					for($j=0;$j<count($placeholders);$j++) {
						list($start,$element) = explode("[[+",$placeholders[$j]);
					
						$replace_key = $element;
					
						list($element,$args) = explode("|",$element);
						

						$value = $data[$set][$element][$k];

						switch(true) {
							case ($config_s[$set][$element]['xtype'] == 'image'):
								$value = $this->image($value,$args);
								break;
							case ($element == 'Zed Index Region'):
								$value = $this->zooplaFeed($d);
								break;
							default:
								$value = $value;
						}
						
						$ph_find[] = "[[+".$replace_key."]]";
						$ph_replace[] = $value;
					}
					
					$section.=str_replace($ph_find,$ph_replace,$tpl);
				}
			}
			$m_replace[] = $section;
			//*/
		}
		
		$c = str_replace($m_find,$m_replace,$c);
		
		$this->content = $c;
	}
	
	private function zooplaFeed($d) {
		//return $d;
		
		$z = new \libs\zoopla\API_Content();
		
		$z->Execute($d);
		
		if($this->template['config_name'] == 'Estate Agent Silver') {
			return $z->getSilver();
		}
		
		if($this->template['config_name'] == 'Estate Agent Gold') {
			return $z->getGoldNew();
		}
		
	}
	
	private function image($d,$args='') {
		
		parse_str($args,$attrs);
		
		if(!filter_var($d,FILTER_VALIDATE_URL)) {
			//local image
			$d = "http://".$_SERVER['SERVER_NAME'].$d;
			
			$img_path = $_SERVER['DOCUMENT_ROOT'].$d;
			
			if(file_exists($img_path)) {
			
				$s = getimagesize($img_path);
			
				if($s[0] > 700) {
					$d = "http://".$_SERVER['SERVER_NAME']."/img/pt/?src=".$img_path."&w=700";
				}
			} else {
				//where is it then????
				;
			}
			
		} else {
			//remote image
			;
		}
		
		if(is_array($attrs) && count($attrs) > 0) {
			$attr_s = array();
			foreach($attrs as $key => $val) {
				$attr_s[] = $key."='".$val."'";
			}
			return "<img src='".$d."'  ".implode(" ",$attr_s)."/>";
		} else {
			return $d;
		}
		
		
	}
}