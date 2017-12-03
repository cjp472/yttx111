<?php
include_once ('common.php');

ini_set('display_errors', 0);
	 error_reporting(E_ALL);
	 
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/class/upfile.class.php");
$ip = $_SERVER["REMOTE_ADDR"];
$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

if($in['m']=="uploadimg")
{
	if(empty($_SESSION['upfilename'])) exit('非法操作!');
	$companyidmsg = $_SESSION['cc']['ccompany'];

	$resPath = setuppath($companyidmsg);
	$tmppath = RESOURCE_NAME.$companyidmsg."/";
	$srcpath = RESOURCE_NAME.$companyidmsg."/".$resPath."/";
	
	$backpath     = $companyidmsg."/".$resPath."/";
	
	if(!empty($_SESSION['upfilename']))
	{
		if($_SESSION['upfilename']=='ext_error'){
			Error::Jump('警告:图片只能是gif、jpg、jpeg、png格式！',"plugin/jqUploader/uploadfile.php");
		}else if($_SESSION['upfilename']=='type_error'){
			Error::Jump('警告:您的信息已被记录，请勿上传恶意图片！',"plugin/jqUploader/uploadfile.php");
		}else{
			$f		 = new upfile($tmppath);
			@chmod ($tmppath.$_SESSION['upfilename'], 0777);
			$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."img_".$_SESSION['upfilename'],600,600);
			@unlink($tmppath.$_SESSION['upfilename']);//删除临时文件

			Error::AlertSet("parent.setinputfile('".$backpath."img_".$_SESSION['upfilename']."')");
			exit('成功上传!');
		}

	}
}elseif($in['m']=="qualification"){
    
    if(empty($_SESSION['upfilename'])) exit('非法操作!');
	$companyidmsg = $_SESSION['cc']['ccompany'];
	$resPath = setuppath($companyidmsg);
	$tmppath = RESOURCE_NAME.$companyidmsg."/";
	$srcpath = RESOURCE_NAME.$companyidmsg."/".$resPath."/";
	$backpath     = $companyidmsg."/".$resPath."/"; 
       
//        print_r($tmppath.$_SESSION['upfilename']);die;
	if(!empty($_SESSION['upfilename']))
	{
		if($_SESSION['upfilename']=='ext_error'){
			Error::Jump('警告:图片只能是gif、jpg、jpeg、png格式！',"plugin/jqUploaderM/upload_certify.php");
		}else if($_SESSION['upfilename']=='type_error'){
			Error::Jump('警告:您的信息已被记录，请勿上传恶意图片！',"plugin/jqUploaderM/upload_certify.php");
		}else{
                    
                     
			$f= new upfile($tmppath);
			@chmod ($tmppath.$_SESSION['upfilename'], 0777);                       
			$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."img_".$_SESSION['upfilename'],600,600);
			@unlink($tmppath.$_SESSION['upfilename']);//删除临时文件                      
			Error::AlertSet("parent.setinputfile('".$backpath."img_".$_SESSION['upfilename']."')");  
//                        Error::AlertSet("parseInt.setinputfile('".$backpath."img_".$_SESSION['upfilename']."')");
			exit('成功上传!');
                        
		}

	}
}



exit('非法操作');
?>