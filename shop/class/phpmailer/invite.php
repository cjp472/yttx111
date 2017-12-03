<?php
//include_once ("config.inc.php"); 


send_mail("seekfor@gmail.com","seekfor","易贴备份:seek/", "内容");




function send_mail($to_address, $to_name ,$subject, $body, $attach = "")
{
	//使用phpmailer发送邮件
	require_once("./class.phpmailer.php");
	$mail = new PHPMailer();
	$mail->IsSMTP(); // set mailer to use SMTP
	//$mail->SMTPDebug = true;
	$mail->CharSet = 'utf-8';
	$mail->Encoding = 'base64';
	$mail->From = 'x_fly@126.com';
	$mail->FromName = '1tie';

	$mail->Host = 'smtp.126.com';
	$mail->Port = 25; //default is 25, gmail is 465 or 587
	$mail->SMTPAuth = true;
	$mail->Username = "x_fly@126.com";
	$mail->Password = "12090386";

	$mail->AddAddress($to_address, $to_name);

	$mail->WordWrap = 50;
	if (!empty($attach)) $mail->AddAttachment($attach);
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $body;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	if(!$mail->Send())
	{
		echo "备份失败: " . $mail->ErrorInfo . "";
		return false;
	}
	else
	{
		echo("已备份到你指定的邮箱!");
		return true;
	}
//echo "Message has been sent";
}

?>