<?php
define('CURSCRIPT','desktop');
if(empty($_GET['c']))
{
$Shortcut = '
[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2
[InternetShortcut]
URL=http://'.$_SERVER['HTTP_HOST'].'
IDList=
IconFile=http://'.$_SERVER['HTTP_HOST'].'/favicon.ico
IconIndex=1
'; 
}else{
$Shortcut = '
[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2
[InternetShortcut]
URL=http://'.$_SERVER['HTTP_HOST'].'/'.$_GET['c'].'/
IDList=
IconFile=http://'.$_SERVER['HTTP_HOST'].'/favicon.ico
IconIndex=1
'; 
}
if(!empty($_GET['n'])){
	$name = $_GET['n'].'(订货系统入口)';
}else{
	//$name = urlencode('订货宝-订货管理系统(订货系统入口)');
    $name = '订货宝-订货管理系统(订货系统入口)';
}

$name .= '.url';

$ua = $_SERVER["HTTP_USER_AGENT"];

header("Content-type: application/octet-stream"); 
//header("Content-Disposition: attachment; filename=".$name.".url;");
if (preg_match("/MSIE/", $ua)) {
    header('Content-Disposition: attachment; filename="' . urlencode($name) . '"');
} else if (preg_match("/Firefox/", $ua)) {
    header('Content-Disposition: attachment; filename*="utf8\'\'' . $name . '"');
} else {
    header('Content-Disposition: attachment; filename="' . $name . '"');
}
echo $Shortcut;

$Shortcut = NULL;
?>