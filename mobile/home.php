<?php
/**
 * List
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
echo '<br /><br /><br /><br /><br /><br />
<p align="center"><a href="http://sj.dhb.hk/download.php">下载新版APP</a></p>
'
exit;
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");

$input	   =	new Input;
$in		   =	$input->parse_incoming();
$urlmsg    =   "";
$location  =   null;
$page_title = '首 页';

$shcart  = new ShoppingCart();

include template("home");
?>