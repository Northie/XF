<?php

namespace libs\forms;

class Renderer {

	/*
	used by form/submission manager to show form
	
	get form id
	read fields and options
	
	output either html or extjs
	
	*/
	
	const HTML = 1;
	const EXT = 2;
	
	const NEW_FORM	=	4;
	const FAILED	=	8;
	const PASSED	=	16;
	
	private $outputtype;
	private $content;
	
	//public function __construct($data,$action,$method='POST',$st='') {
	public function __construct($form) {
		
		$this->data = $form->definition;
		$this->caption = $form->caption;
		$this->action = $form->action;
		$this->method = $form->method;
		$this->form_name = $form->form_name;
		$this->securityToken = $_SESSION['security_token'];
		
		$this->html_wrappers['form']['start'] = "<table>";
		$this->html_wrappers['form']['end'] = "</table>";
		
		$this->html_wrappers['row']['start'] = "<tr>";
		$this->html_wrappers['row']['end'] = "</tr>";

		$this->html_wrappers['label']['start'] = "<td>";
		$this->html_wrappers['label']['end'] = "</td>";

		$this->html_wrappers['input']['start'] = "<td>";
		$this->html_wrappers['input']['end'] = "</td>";
		
		$this->html_wrappers['required']['start'] = "<span class='error msg'>";
		$this->html_wrappers['required']['end'] = "</span>";
		
		$this->submit_label = "Submit";
		
		$this->required_text = " *This is a required field";
		$this->valid_text = ' *This field has not been validated';
		
		$this->show_submit_label = false;
	}
	
	public function setOutput($type) {
		$this->outputtype = $type;
	}
	
	public function getHtml() {
		$this->toHTML();
		return $this->content;
	}
	
	public function setStage($stage) {
		switch($stage) {
			case 'NEW':
				$this->stage = self::NEW_FORM;
				break;
			case 'FAILED':
				$this->stage = self::FAILED;
				break;
			case 'PASSED':
				$this->stage = self::PASSED;
				break;
		}
	}
	
	public function getContent() {
		switch($this->outputtype) {
			case self::HTML:
				$this->toHTML();
				break;
			case self::EXT:
				$this->toExtJS();
				break;
		}
		
		return $this->content;
	}

	public function SetWrappers($item,$pos,$html) {
		$this->html_wrappers[$item][$pos] = $html;
	}
	
	public function setSubmitText($text) {
		$this->submit_label = $text;
	}
	
	public function setRequiredText($text) {
		$this->required_text = $text;
	}
	
	public function UseForm($form_name='') {
		$this->form = $this->getFormDefinition($form_name);
		$this->form_name = $form_name;
	}
	
	public function hideSubmitLabel() {
		$this->show_submit_label = false;
	}
	
	private function toHTML() {
	
		$c = "<form class='form-horizontal' action='".$this->action."' method='".$this->method."' enctype='multipart/form-data'>";
		
		if($this->caption['message']) {
			$c.="
				<!--div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12' -->
							<div class='alert alert-".($this->caption['alert'] == '' ? "info" : $this->caption['alert'])."'>
								".$this->caption['message']."
								<a class='close' href='#' data-dismiss='alert'>×</a>
							</div>
						<!-- /div>
					</div>
				</div -->
			";
		}
		
		/*
		$c.="
			".$this->html_wrappers['form']['start']."
		";
		//*/
	
		//$c.="<pre>".print_r($this->data,1)."</pre>";
						
		for($i=0;$i<count($this->data);$i++) {
			//$value = $this->data[$i]['value'];
			
			$value = $_SESSION['form_data'][$this->form_name][$i]['value'];
			
			if($this->data[$i]['input_type'] == 'hidden') {
				$c.="<input type='hidden' name='".$this->data[$i]['name']."' value='".$value."' />\n\n";			
			} else {

				//*
				if($this->data[$i]['fieldset'] !== $this->data[$i-1]['fieldset']) {
					//start fieldset
					//start table
					$c.="<fieldset><legend>".$this->data[$i]['fieldset']."</legend>\n";
					//$c.=$this->html_wrappers['form']['start'];
				}
				//*/

				//$c.=$this->html_wrappers['input']['start'];
				
				$this->data[$i]['input_type'] = $this->data[$i]['input_type'] == '' ? 'text' : $this->data[$i]['input_type'];
				
				$disabled = '';
				
				if($this->data[$i]['disabled']) {
					$disabled = "disabled='disabled'";
				}

				switch($this->data[$i]['input_type']) {
					case 'password':
						$input = "<input ".$disabled." class='-password' type='password' name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."' value = '".$value."' />";
						break;
					case 'textarea':
						$input = "<textarea ".$disabled." name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."'>".$value."</textarea>";
						break;
					case 'richtext':
						$input = "<textarea ".$disabled." name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."' class='richtext'>".$value."</textarea>";
						break;
					case 'select':
						$input = "<select ".$disabled." class='-select' name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."'>\n";
						for($j=0;$j<count($this->data[$i]['option_data']);$j++) {
							$input.="<option value='".$this->data[$i]['option_data'][$j]['post']."' ".($this->data[$i]['option_data'][$j]['post'] == $value ? "selected='selected'" : "").">".$this->data[$i]['option_data'][$j]['display']."</option>\n";
						}
						$input.="</select>";
						break;
					case 'radio':
						$input = '';
						for($j=0;$j<count($this->data[$i]['option_data']);$j++) {
							$input.="<span>".$this->data[$i]['option_data'][$j]['display']."</span><input ".$disabled." class='-radio' type='radio' name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name'].".".$j."' value='".$this->data[$i]['option_data'][$j]['post']."' ".($this->data[$i]['option_data'][$j]['selected'] == 'selected' ? "checked='checked'" : "")." />\n";
						}
						if($j == 0) {
							$input = "<span class='msg'>There are no options for this input</span>";
						}
						break;
					case 'checkbox':
						$input = '';
						for($j=0;$j<count($this->data[$i]['option_data']);$j++) {
							$input.="<span>".$this->data[$i]['option_data'][$j]['display']."</span><input class='-checkbox' type='checkbox' name='".$this->data[$i]['name']."[]' id='_".$this->data[$i]['name'].".".$j."' value='".$this->data[$i]['option_data'][$j]['post']."' ".($this->data[$i]['option_data'][$j]['selected'] == 'selected' ? "checked='checked'" : "")." />\n";
						}
						if($j == 0) {
							$input = "<span class='msg'>There are no options for this input</span>";
						}
						break;
					case 'file':
						$input = "<input ".$disabled." class='text' name='".\core\System_Settings::Load()->getSettings('ZEST_UPLOAD_FIELD_NAME')."' type='file' />";
						break;
					case 'image':
					case 'file2':
						$input = "<input ".$disabled." class='file2' name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."' value='".$value."' type='hidden' /><a href='#' class='btn choose-file ".($disabled != '' ? "disabled" : "")."'>Choose...</a> <a href='#' class='btn clear-file btn-warning ".($disabled != '' ? "disabled" : "")."' data-clear='_".$this->data[$i]['name']."'>Clear</a><br /><div id='_preview_".$this->data[$i]['name']."' class='image-preview'></div>";
						break;
					case 'recaptcha':
						$publickey = \core\System_Settings::Load()->getSettings('recaptcha','publickey');
						$input = recaptcha_get_html($publickey);
						break;
					default:
						$input = "<input ".$disabled." class='-text".($this->data[$i]['auto_suggest'] != "" ? " autosuggest" : "")."' ".($this->data[$i]['auto_suggest'] != "" ? "data-autosuggest-source='".$this->data[$i]['auto_suggest']."'" : "")." type='".$this->data[$i]['input_type']."' name='".$this->data[$i]['name']."' id='_".$this->data[$i]['name']."' value='".$value."' />";
						break;

				}

				$req = "";

				if($this->stage != self::NEW_FORM) {

					if($this->data[$i]['required'] && !$value) {
						$req.=$this->required_text;
					}

					if($this->data[$i]['validate'] && $this->stage == self::FAILED && !$this->data[$i]['valid']) {

						$valid = !!$this->data[$i]['valid'];
						$req.=$this->valid_text;

					}
				}
				
				$label = "<label class='control-label' for='_".$this->data[$i]['name']."'>".$this->data[$i]['label']." ".($this->data[$i]['required'] ? "*" : "")."</label>";
				
				$row = "
					<div class='control-group ".($req == '' ? ($this->stage != self::NEW_FORM ? "success" : "") : "error")."'>
						".$label."
						<div class='controls'>
							".$input."
							<span class='help-inline'>".$req."</span>
							<span class='help-block'>".$this->data[$i]['notes']."</span>
						</div>
						
					</div>
				";
				
				$c.=$row;
				
				//*
				if($this->data[$i]['fieldset'] !== $this->data[$i+1]['fieldset']) {
					//end table
					$c.=$this->html_wrappers['form']['end'];
					//end fieldset
					$c.="</fieldset>\n";
				}
				//*/
			}
		}
		
		$submitLabel = '';
		
		if($this->show_submit_label) {
			$submitLabel = "<label class='control-label' for='".strtolower(str_replace(" ","_",$this->submit_label))."'>".$this->submit_label."</label>";
		}
		/*
		$c.="
			<fieldset>
				".$this->html_wrappers['form']['start']."
			
				".$this->html_wrappers['row']['start']."
					".$this->html_wrappers['label']['start'].$submitLabel.$this->html_wrappers['label']['end']."
					".$this->html_wrappers['input']['start']."<input type='submit' name='".strtolower(str_replace(" ","_",$this->submit_label))."' value='".$this->submit_label."' />".$this->html_wrappers['input']['end']."
				".$this->html_wrappers['row']['end']."
			
			
				".$this->html_wrappers['form']['end']."
			</fieldset>
			<input type='hidden' name='submitted' value='1' />
		";
		//*/
		
		$actions = "
			<div class='form-actions'>
				".$submitLabel ."
				<div class=''>
					<input class='btn btn-primary' type='submit' name='".strtolower(str_replace(" ","_",$this->submit_label))."' value='".$this->submit_label."' />
				</div>
			</div>
		";
		
		$c.=$actions;
		
		if($this->securityToken != '') {
			$c.="<input type='hidden' name='security_token' value='".$this->securityToken."' />";
		}
		
		$c.="
			<input type='hidden' name='submitted' value='1' />
			<input type='hidden' name='_form_name' value='".$this->form_name."' />
			<input type='hidden' name='".sha1($this->form_name.$_SESSION['security_token'])."' value='1' />
			
			
		</form>
		";
		
		$this->content = $c;
	
	}
	
	private function toExtJS() {
	
	}
}