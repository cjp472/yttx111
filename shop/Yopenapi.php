<?php

/**
 * 易支付
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */

include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/client.php");

$input	=	new Input;
$in		=	$input->parse_incoming();

$tpl	= empty($in['m']) ? 'Yopenapi' : 'openpay_status';


include template($tpl);
	
// if(empty($in['m'])){
// 	include template('Yopenapi');
// }else{
	
// 	include template('openpay_status');
// }


?>