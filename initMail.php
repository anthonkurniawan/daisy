<?php

function sendMail($subject='',$body,$to=array(),$cc=array(),$bcc=array()){
	require_once "class.phpmailer.php";
	$mail = new PHPMailer();
	//$mail->IsMail();
	$mail->IsSMTP();
	$mail->IsHTML(true);
	//$mail->From		= $from;
	//$mail->FromName	= $fromName;
	//$mail->Sender	= $sender; 
	foreach($to as $name=>$email)
			$mail->AddAddress($email, $name);
	if(is_array($cc) && !empty($cc)){
	foreach($cc as $ccname=>$ccemail)
			$mail->AddAddress($ccemail, $ccname);
	}
	if(is_array($bcc) && !empty($bcc)){
	foreach($bcc as $bccname=>$bccemail)
			$mail->AddAddress($bccemail, $bccname);	
	}
	$mail->Subject 	= $subject;
	$mail->Body 	= $body;
	//$mail->AltBody 	= ;
	if(!$mail->Send())
	{
	   return "Error sending: " . $mail->ErrorInfo;;
	}else return true;
}
?>