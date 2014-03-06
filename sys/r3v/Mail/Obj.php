<?php ///r3vCMS /sys/Mail.php
namespace r3v\Mail;

/**
 * Mailer: Object
 */
class Object {
	///Receiver mails
	public $email_to;
	///Mail subject
	public $email_subject;
	///Mail content
	public $email_message;
	///Array of headers
	public $headers;

	///Construct
	public function __construct($email_to, $email_subject, $email_message, array $headers) {
		$this->email_to = $email_to;
		$this->email_subject = $email_subject;
		$this->email_message = $email_message;
		$this->headers = $headers;
	}

	///Send current object through mail()
	public function send() {
		$selfmail = 'teo@teo.themaxx.linuxpl.info'; //FIXME
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
