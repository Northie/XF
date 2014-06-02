<?php

namespace libs\email;

class Content_Editor {
	
	public function __construct($id,$edition) {
		$template = \libs\models\Resource::Load('template')->read(array("id"=>$id))->getOne();
		
		//var_dump($template);
	
		$str = $template['config_config']."\n".$template['additional_configuration'];
		
		$this->serviceType = $template['config_name'];
		
		$p = new Parser($id);
		$p->parse($str);
		$this->config = $p->getConfig();
		
		$this->parser = $p;
		
		$this->formActionlabel = 'Save Newsletter';
		
		$this->id = &$id;
		$this->edition = &$edition;
		$this->service_id = $template['config_id'];
	}
	
	public function getForm() {
		$i = 1;
		foreach($this->config as $set => $details) {
			$content.="
			<fieldset id='set".$i."' class='fstab ".($details['meta']['multiple'] ? "multiple": '')."' data-multiple-unique='".$i."'>
				<legend><span>".$set."</span></legend>
				<ul>
			
			";
			
			$provider = $details['meta']['source'] ? $details['meta']['source'] : 'client';
			
			foreach($details['fields'] as $field) {
				$value = $this->parser->getOpt($set,$field['label'],$provider,$this->edition);
				
				if($details['meta']['multiple']) {
					$value = json_decode($value,1);
					
					$m = count($value);
					
					if($m > 1) {
						for($j=1;$j<$m;$j++) {
							$content.=$this->hiddenField($field,$set,$value[$j],$j);
						}
					}
					
					$value = stripslashes(trim($value[0],'"'));
				}
				
				switch($field['xtype']) {
					case 'text':
					case 'url':
						$form_row = $this->textField($field,$set,$value,$details['meta']['multiple']);
						break;
					case 'zoopla':
						$form_row = $this->zoopla($field,$set,$value,$details['meta']['multiple']);
						break;
					case 'textarea':
						$form_row = $this->textArea($field,$set,$value,$details['meta']['multiple']);
						break;
					case 'richtext':
						$form_row = $this->richText($field,$set,$value,$details['meta']['multiple']);
						break;
					case 'image':
						$form_row = $this->imageField($field,$set,$value,$details['meta']['multiple']);
						break;
					case 'feed':
						$form_row = $this->rssFeedInput($field,$set,$value,$details['meta']['multiple']);
						break;
							
				}
				
				$form_row = "<li class='form-row'>".$form_row."</li>";
				
				$content.=$form_row;
			}
			
			$content.="
				</ul>
			</fieldset>\n";
			
			$tabLinks[] = "<a class='fstab ".($details['meta']['multiple'] ? "multiple": '')."' ".($details['meta']['multiple'] ? "data-multiple-unique='".$i."' data-multiple-used='".$m."' data-multiple-limit='".$details['meta']['limit']."'": '')." href='#set".$i."'>".$set."</a>";
			
			$i++;
		}
		
		$content = "
		<div class='kube' style='width:960px; margin:0px auto;'>
			<form id='myNewsletter' class='forms columnar' action='?".$_SERVER['QUERY_STRING']."' method='post' enctype='multipart/form-data'>
				<div class='row split'>
					<div class='span9' style='padding-top:9px; text-align:left;'>
						<a class='btn btn-success preview'	href='/?template/read/forId/".$this->id."/edition/".$this->edition."/service/".$this->service_id."' id='preview-newsletter'><i class='icon-white icon-th-large'></i> Preview Newsletter</a>
						<a class='btn btn-warning send-test'		href='/newsletters/client/release2.php?newsletter=template/read/forId/".$this->id."/edition/".$this->edition."/service/".$this->service_id."&test=test' id='send-test-newsletter'><i class='icon-white icon-envelope'></i> Send Test Newsletter</a>
						<a class='btn btn-danger send-now'	href='/newsletters/client/release2.php?newsletter=template/read/forId/".$this->id."/edition/".$this->edition."/service/".$this->service_id."&quick=quick' id='approve-schedule-newsletter'><i class='icon-white icon-play'></i> Send Now</a>						
						<a class='btn btn-inverse schedule'	href='/newsletters/client/release2.php?newsletter=template/read/forId/".$this->id."/edition/".$this->edition."/service/".$this->service_id."' id='approve-schedule-newsletter'><i class='icon-white icon-calendar'></i> Approve &amp; Schedule</a>
					</div>
					<div class='span3' style='text-align:right;'>
						<a class='btn btn-primary btn-large' href='#myNewsletter' id='submit' style='margin-right:20px;'><i class='icon-white icon-edit'></i> ".$this->formActionlabel."</a>
					</div>
				</div>
				<div class='row split'>
					<div class='quarter span3'>
						<h4>Edit Your Features</h4>
						<ul id='featureNav'>
							<li>".implode("</li>\n\t\t\t\t<li>",$tabLinks)."</li>
						</ul>
					</div>
					<div class='threequarter span9 closeUp'>

						".$content."
					
					</div>
				</div>
			</form>
		</div>
		";
		
		$this->content.=$content;
	}
	
	public function save() {
		foreach($this->config as $set => $details) {
			foreach($details['fields'] as $field) {
				$key = $this->HTMLise($set."--".$field['label']);
				$provider = $details['meta']['source'] ? $details['meta']['source'] : 'client';
				
				switch(true) {
					case ($details['meta']['multiple']):
						
						$value = json_encode($_POST[$key]);
						
						break;
					
					case ($field['xtype'] == 'feed'):
						
						$v = array(
							"title"=>$_POST['rss-title'],
							"body"=>$_POST['rss-body'],
							"pub-date"=>$_POST['rss-pub-date'],
							"link"=>htmlentities($_POST['rss-link']),
						);
						
						$value = $_POST[$key]."|".json_encode($v);
						
						break;
					
					default:
						
						$value = $_POST[$key];
						
				}
				
				$this->parser->setOpt($set,$field['label'],$value,$provider,$this->edition);
			}
		}
		 
	}
	
	private function HTMLise($str) {
		return str_replace(" ","_",$str);
	}

	private function hiddenField($field,$set,$value,$m) {
		return "<input class='helper remove' type='hidden' name='".$this->HTMLise($set."--".$field['label'])."[".$m."]' value='".htmlentities($value,ENT_QUOTES,'UTF-8',false)."' data-multiple-source='".$this->HTMLise($set."--".$field['label'])."' />";
	}
	
	private function textField($field,$set,$value,$multiple) {
		
		return "
				<fieldset>
					<section>
						<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']."</label>
					</section>
					<div class='input-append'>
						<input class='span6' type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' value='".$value."'/>
						<a href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."' class='btn btn-info load-default'><i class='icon-refresh icon-white'></i> Load Default Value</a>
					</div>
					<div class='descr help-block'>".($field['desc'] ? $field['desc']."<br />" : '')."</div>
				<fieldset>
		";
	}
	
	private function zoopla($field,$set,$value,$multiple) {
		
		return "
				<fieldset>
					<section>
						<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']."</label>
					</section>
					<div class='input-append'>
						<input class='span4' type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' value='".$value."'/>
						<a href='/connectors/zoopla.php?service=".$this->serviceType."' class='btn btn-warning zoopla-preview'><i class='icon-white icon-th-list'></i> Preview</a>
						<a href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."' class='btn btn-info load-default'><i class='icon-refresh icon-white'></i> Load Default Value</a>
					</div>
					<div class='descr help-block'>".($field['desc'] ? $field['desc']."<br />" : '')."
						<div class='preview'></div>
					</div>
				<fieldset>
		";
	}

	private function textArea($field,$set,$value,$multiple) {
			
		return "
				<fieldset>
					<section>
						<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']."</label>
					</section>
					<textarea class='span6' style='height:100px;' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' class='richtext'>".$value."</textarea>
					<div class='descr help-block'>This is a plain text area - only line breaks will be preserved<br /><a href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."' class='btn btn-small btn-info load-default'><i class='icon-refresh icon-white'></i> Load Default Value</a></div>
				</fieldset>
		";	
	}
	
	private function richText($field,$set,$value,$multiple) {
			
		return "
				<fieldset>
					<section>
						<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']."</label>
					</section>
					<textarea class='rich-text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' class='richtext'>".htmlentities($value,ENT_QUOTES,'UTF-8',false)."</textarea>
					<div class='descr help-block'>This is a rich text editor - you may style the content with the supplied controls in the toolbar<br /><a href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."' class='btn btn-small btn-info load-default'><i class='icon-refresh icon-white'></i> Load Default Value</a></div>
				</fieldset>
		";	
	}
	
	private function imageField($field,$set,$value,$multiple) {
		
		return "
				<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']." (URL)</label>
				<div class='input-append'>
					<input type='text' class='input-xlarge' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' value='".$value."' />
					<a class='btn btn-append choose' href='#'><img src='/libs/icons/application_view_tile.png' /> Choose</a>
					<a href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."' class='btn btn-info load-default'><i class='icon-refresh icon-white'></i> Load Default Value</a>
					<a href='#' class='btn btn-danger clear'><i class='icon-white icon-remove'></i> Clear</a>
				</div>
				<div class='descr help-block'>
					<div class='image-preview'><img src='".$value."' style='width:350px;' /></div>
				</div>
		";
	}
	
	private function rssFeedInput($field,$set,$value,$multiple) {
		
		list($value,$data) = explode("|",$value);
		
		return "
				<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']." (URL or Search Term)</label>
				<div class='input-append'>
					<input class='input-large' type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' value='".$value."' />
					<div class='btn-group'>
						<a class='btn dropdown-toggle' data-toggle='dropdown'>Choose Feed Action <span class='caret'></span></a>
						<ul class='dropdown-menu'>
							<li><a class='get-feed' href='#'><img src='/libs/icons/rss_add.png' /> Load a feed from this URL</a></li>
							<li><a class='build-feed' href='#'><img src='/libs/icons/rss_add.png' /> Build a Feed from this Search Term</a></li>
						</ul>
					</div>
					<a class='feed-load-default btn btn-info' href='/api/?template_data/read/forTemplate/".$this->id."/set/".$set."/field/".$field['label']."/edition/".$this->edition."/service/".$this->service_id."'><i class='icon-refresh icon-white'></i> Load Default</a>
					<a href='#' class='btn btn-danger clear-feed'><i class='icon-white icon-remove'></i> Clear</a>
					<a class='btn btn-success help' data-placement='top' data-content='An RSS feed URL might be your own blogs news feed. Search terms are queried against Google (UK) News' data-title='RSS Feed help'><i class='icon-white icon-question-sign'></i> Help</a>
				</div>
				<div class='feed-descr descr help-block'>
					Enter the URL of an RSS feed and choose 'Load a feed from this URL'.<br />
					Alternatively enter a search term and choose 'Build a Feed from this Search Term'.<br />
					Either way, you will then have the opportunity to select and fine tune content to include in your newsletter.
					<div class='feed-preview'></div>
				</div>
				<script type='text/javascript'>
					FEED = ".($data ? $data : 'false').";
				</script>
		";		
	}
}