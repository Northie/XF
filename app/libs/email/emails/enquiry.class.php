<?php
namespace libs\email\emails;

class enquiry extends email {
	public function __construct($to) {
		parent::__construct($to);
	}
	
	public function send() {
		
		$this->subject = "Enquiry from ".$_SERVER['SERVER_NAME']." Website";
		
		$this->from_email = $this->data['email'];
		$this->from_name = $this->data['name'];
		
		$fields = [
		    "title"=>1,
		    "name"=>1,
		    "email"=>1,
		    "address"=>1,
		    "telephone"=>1,
		    "enquiry"=>1,
		];
		
		$msg = [];
		
		foreach($this->data as $key => $val) {
			if($fields[$key]) {
				
				$v = (trim($val) == '') ? 'not supplied' : \libs\misc\Tools::html_escape($val);
				
				$msg[] = $key.": ".$v."\n";
			}
		}
		
		$this->message = "Hi,

You have received a new enquiry from your website. The enquiry has been saved and can be viewed in your admin area.

For now, you can see the details here:

".implode("\n",$msg)."

Kind regards,

Your Website
";
		
		parent::sendNow(false);
	}
}