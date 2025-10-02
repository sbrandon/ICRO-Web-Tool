<?php

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
	var $app_password;
	var $host;
	var $port;
	var $user_email;
	var $user_name;

	function __construct()
	{
	    require('../icro-config/config-live.php');

	    // Initialise the login details
	    $this->app_password = $EMAIL_APP_PASSWORD;
	    $this->host = $EMAIL_HOST;
	    $this->port = $EMAIL_PORT;
	    $this->user_email = $EMAIL_USER;
	    $this->user_name = $EMAIL_NAME;
	 
	    // no errors yet
	    $this->last_error = '';
	}

	function send($to, $subject, $htmlBody, $textBody) 
	{
		//TODO ADD TRY/CATCH with error logging.
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host       = $this->host;
		$mail->Port       = $this->port;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

		$mail->Username   = $this->user_email;
		$mail->Password   = $this->app_password;

		$mail->setFrom($this->user_email, $this->user_name);
		$mail->addAddress($to);
		$mail->addReplyTo($this->user_email, $this->user_name);

		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $htmlBody;
		$mail->AltBody = $textBody;

		$mail->MessageID = '<'.bin2hex(random_bytes(16)).'@icro.ie>';

		$mail->send();
	}
}

?>