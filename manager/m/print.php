<?php 
include_once ("../header.inc.php");
$menu_flag = "home";
$url1 = "print_header.php?u=".$_GET['u'];
$_GET['ID'] = intval($_GET['ID']);
if(substr($_GET['u'],0,5) != 'print') exit('error!');
if(strlen($_GET['u']) > 20) exit('error!');
if(!empty($_GET['u']))
{
	$url2 = trim($_GET['u']).".php";
}else{
	$url2 = "";
}
if(!empty($_GET['ID'])) $url2 = $url2."?ID=".$_GET['ID'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
  <head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>医统天下 - 管理平台</title>
    <meta name="robots" content="noindex">
  </head>
  <frameset rows="40,*" frameborder="0" framespacing="0"  >
    <frame name="printHeader" scrolling="no" noresize="true" src="<?=$url1?>">
    <frame name="printMain" frameborder="0" src="<?=$url2?>" scrolling="yes" >
  </frameset>
  <noframes>Your Browser not support frame mode. </noframes>
</html>