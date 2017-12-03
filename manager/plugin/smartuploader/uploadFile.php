<?php
include_once ("../../common.php");
include_once (SITE_ROOT_PATH."/class/upfile.class.php");

if(empty($_SESSION['uc']['CompanyID'])) exit('Not login!');

$companyidmsg = $_SESSION['uc']['CompanyID'];
$resPath  = setuppath($companyidmsg);
$srcpath  = RESOURCE_PATH.$companyidmsg."/".$resPath."/";
$backpath = $companyidmsg."/".$resPath."/";

$sFileName  = basename($_FILES['Filedata']['name']);
$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) );
$sExtension = strtolower( $sExtension );
$currenttime = date("d_His")."_".currentTimeMillis()."".rand(10,99);
$sFileName   = $currenttime.".".$sExtension;

$f		 = new upfile();
$f->makeThumb($_FILES['Filedata']['tmp_name'], $srcpath."thumb_".$sFileName,160,120);
@chmod($srcpath."thumb_".$sFileName, 0777);
$f->makeThumb($_FILES['Filedata']['tmp_name'], $srcpath."img_".$sFileName,680,1200);
@chmod($srcpath."img_".$sFileName, 0777);
@unlink($_FILES['Filedata']['tmp_name']);

$filearr = null;
$filearr['oldname']  = $_FILES['Filedata']['name'];
$filearr['filesize'] = $_FILES['Filedata']['size'];
$filearr['filename'] = $sFileName;
$filearr['filepath'] = $backpath;

$_SESSION['up_file'][] = $filearr;
?>