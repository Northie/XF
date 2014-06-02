<?php

namespace libs\email;

class NLH_Content_Editor {
	
	public function __construct($id,$edition) {
		$template = \libs\models\Resource::Load('service')->read(array("id"=>$id))->getOne();
		
		$str = $template['config_config'];
		
		$p = new Parser($id);
		$p->parse($str);
		$this->config = $p->getConfig();
		
		$this->parser = $p;
		
		$this->formActionlabel = 'Save Edition';
		
		$this->id = &$id;
		$this->edition = &$edition;
	}
	
	public function getForm() {
		$i = 1;
		foreach($this->config as $set => $details) {

			$provider = $details['meta']['source'] ? $details['meta']['source'] : 'client';
			
			if($provider == 'nlh') {
			
				$content.="
				<fieldset id='set".$i."' class='fstab ".($details['meta']['multiple'] ? "multiple": '')."' data-multiple-unique='".$i."'>
					<legend><span>".$set."</span></legend>
					<ul>

				";

				foreach($details['fields'] as $field) {
					$value = $this->parser->getNLHOpt($set,$field['label'],$this->edition);

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
		}
		
		$service = \libs\models\Resource::Load('service')->read(array("id"=>$this->id))->getOne();
		
		$content = "
		<div class='kube' style='width:960px; margin:0px auto;'>
			<form class='forms columnar' action='?".$_SERVER['QUERY_STRING']."' method='post' enctype='multipart/form-data'>
				<div class='row split'>
					<div class='span6'>
						<h1>".$service['name']." ".$service['level']." (".date('F Y',strtotime($_GET['edition'].'01')).")</h1>
					</div>
					<div class='span6' style='text-align:right'>
						<a class='btn' href='/?service/read'>Back to All Services</a>
						<a class='btn btn-large btn-primary' href='#' id='submit'><img src='/libs/icons/page_save.png' /> ".$this->formActionlabel."</a>
					</div>
				</div>
				<div class='row split'>
					<div class='quarter span3'>
						<h4>Edit Newsletterhub Features</h4>
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
				
				if($provider == 'nlh') {
				
					switch(true) {
						case ($details['meta']['multiple']):

							$value = json_encode($_POST[$key]);

							break;

						case ($field['xtype'] == 'feed'):

							$v = array(
								"title"=>$_POST['rss-title'],
								"body"=>$_POST['rss-body'],
								"pub-date"=>$_POST['rss-pub-date'],
								"link"=>$_POST['rss-link'],
							);

							$value = $_POST[$key]."|".json_encode($v);

							break;

						default:

							$value = $_POST[$key];

					}

					$this->parser->setNLHOpt($set,$field['label'],$value,$this->edition);
				}
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
					<input class='input-xxlarge' type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' value='".$value."'/>
					<div class='descr help-block'>".($field['desc'] ? $field['desc']."<br />" : '')."</div>
				<fieldset>
		";
	}
	
	private function richText($field,$set,$value,$multiple) {
			
		return "
				<fieldset>
					<section>
						<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']."</label>
					</section>
					<textarea class='rich-text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' id='".$this->HTMLise($set."--".$field['label'])."' class='richtext'>".htmlentities($value,ENT_QUOTES,'UTF-8',false)."</textarea>
					<div class='descr help-block'>This is a rich text editor - you may style the content with the supplied controls in the toolbar<br /></div>
				</fieldset>
		";	
	}
	
	private function imageField($field,$set,$value,$multiple) {
		
		return "
				<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']." (URL)</label>
				<div class='input-append'>
					<input type='text' class='input-xlarge' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' value='".$value."' />
					<a class='btn choose' href='#'><img src='/libs/icons/application_view_tile.png' /> Choose</a>
					<a href='#' class='btn btn-danger clear'><i class='icon-white icon-remove'></i> Clear</a>
				</div>
				<div class='descr help-block'>
					<div class='image-preview'><img src='".$value."' style='width:350px;' /></div>
				</div>
		";
		
	}
	/*
	private function rssFeedInput($field,$set,$value,$multiple) {
		
		list($value,$data) = explode("|",$value);
		
		return "
				<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']." (URL)</label>
				<input type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' value='".$value."' /><a class='btn btn-append get-feed' href='#'><img src='/libs/icons/rss_add.png' /> Get Feed</a> or <a class='btn build-feed' href='#'><img src='/libs/icons/rss_add.png' /> Build Feed URL</a>
				<div class='feed-descr descr'>
					Enter the URL of an RSS feed (eg your blog's feed) and click 'Get Feed'.<br />
					Alternatively click 'Build Feed URL' to search for relevant news items.<br />
					Either way, you will then have the opportunity to select and fine tune content to include in your newsletter.
					<div class='feed-preview'></div>
					<a href='#' class='btn btn-small clear-feed'><img src='/libs/icons/cancel.png'> Clear</a>
				</div>
				<script type='text/javascript'>
					FEED = ".($data ? $data : 'false').";
				</script>
		";		
	}
	//*/
	private function rssFeedInput($field,$set,$value,$multiple) {
		
		list($value,$data) = explode("|",$value);
		
		return "
				<label class='bold' for='".$this->HTMLise($set."--".$field['label'])."'>".$field['label']." (URL or Search Term)</label>
				<div class='input-append'>
					<input class='input-xlarge' type='text' name='".$this->HTMLise($set."--".$field['label']).($multiple ? "[]" : '')."' value='".$value."' />
					<div class='btn-group'>
						<a class='btn dropdown-toggle' data-toggle='dropdown'>Choose Feed Action <span class='caret'></span></a>
						<ul class='dropdown-menu'>
							<li><a class='get-feed' href='#'><img src='/libs/icons/rss_add.png' /> Load a feed from this URL</a></li>
							<li><a class='build-feed' href='#'><img src='/libs/icons/rss_add.png' /> Build a Feed from this Search Term</a></li>
						</ul>
					</div>
					<a href='#' class='btn btn-danger clear-feed'><i class='icon-white icon-remove'></i> Clear</a>
					<a class='btn btn-success help' data-content='An RSS feed URL might be your own blogs news feed. Search terms are queried against Google (UK) News' data-title='RSS Feed help'><i class='icon-white icon-question-sign'></i> Help</a>
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