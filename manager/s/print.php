<?php 
$menu_flag = "home";
//$pope	   = "pope_view";
include_once ("header.php");
$url1 = "print_header.php?u=".$in['u'];

if(!empty($in['u']))
{
	$url2 = trim($in['u']).".php";
}else{
	$url2 = "";
}
if(!empty($in['ID'])) $url2 = $url2."?ID=".$in['ID'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
  <head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <meta name="robots" content="noindex">
  </head>
  <frameset rows="40,*" frameborder="0" framespacing="0"  >
    <frame name="printHeader" scrolling="no" noresize="true" src="<?=$url1?>">
    <frame name="printMain" frameborder="0"   src="<?=$url2?>" scrolling="yes" >
  </frameset>
  <noframes>Your Browser not support frame mode. </noframes>
</html>