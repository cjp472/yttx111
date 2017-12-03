<?php
define('CURSCRIPT','desktop');

$Shortcut = '
[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2
[InternetShortcut]
URL=http://'.$_SERVER['SERVER_NAME'].'
IDList=
IconFile=http://'.$_SERVER['SERVER_NAME'].'/favicon.ico
IconIndex=1
'; 
$name = urlencode('订货宝-订货管理系统(管理入口)');

header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=".$name.".url;"); 
echo $Shortcut;

$Shortcut = NULL;
?>