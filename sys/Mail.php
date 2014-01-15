<?php
namespace CMS;

class MailObject{

	public $email_to;
	public $email_subject;
	public $email_message;
	public $headers;

	public function __construct($email_to, $email_subject, $email_message, array $headers){
		$this->mail_to = $mail_to;
		$this->email_subject = $email_subject;
		$this->email_message = $email_message;
		$this->headers = $headers;
	}
	public function send(){
		$selfmail = 'teo@teo.themaxx.linuxpl.info';
		$this->headers[] = 'MIME-Version: 1.0';
		$this->headers[] = 'Content-type: text/html; charset=utf-8';
		$this->headers[] = 'To: '.$this->email_to;
		$this->headers[] = 'From: ' . $selfmail;
		$this->headers[] = 'Reply-to: ' . $selfmail;
		$this->headers[] = 'X-Mailer: '.CMS::CMS_ID.' '.CMS::CMS_VER;
		$headers = implode ("\r\n", $this->headers);
		mail($this->email_to, $this->email_subject, $this->email_message, $headers);
	}	
}

class Mail {
	private static $cache = array();
	public static function create($email_to, $email_subject = NULL, $email_message = NULL, array $headers = array()) {
		if ($email_to instanceof MailObject)
			self::$cache[] = $email_to;
		elseif (isset($email_subject, $email_message))
			self::$cache[] = new MailObject($email_to, $email_subject, $email_message, $headers);

	}
	public static function flush(){
		foreach (self::$cache as $v)
			$v->send();
		self::$cache = array();
	}
}