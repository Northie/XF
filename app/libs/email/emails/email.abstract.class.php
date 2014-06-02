<?php
namespace libs\email\emails;

abstract class email implements iEmail {
	
	protected $data = array();
	private $opts = array();

	public function __construct($to) {
		$this->to = $to;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public function __set($key,$val) {
		$this->opts[$key] = $val;
	}
	
	public function __get($key) {
		return $this->opts[$key];
	}

	public function sendNow($html) {
		$this->mailer = new \libs\PHPMailer\PHPMailer;
		
		$this->mailer->AddReplyTo($this->opts['from_email'],$this->opts['from_name']);
		$this->mailer->SetFrom($this->opts['from_email'],$this->opts['from_name']);
		$this->mailer->AddAddress($this->to['email'], $this->to['name']); 
		$this->mailer->Subject = $this->subject;
		
		$this->mailer->CharSet = 'UTF-8';

		if($html) {
			$this->mailer->MsgHTML($message);
			$this->mailer->AltBody = strip_tags($alt_message);
		} else {
			$this->mailer->ContentType = 'text/plain'; 
			$this->mailer->isHtml(false);
			$this->mailer->Body = $this->message;
		}

		if($this->mailer->Send()) {
			return true;
		}
		
		return false;
	}
}