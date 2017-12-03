<?php
	/* Note: This thumbnail creation script requires the GD PHP Extension.  
		If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
	 */

	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
//	if (isset($_POST["PHPSESSID"])) {
//		session_id($_POST["PHPSESSID"]);
//	}
	include_once ("../../common.php");
	include_once (SITE_ROOT_PATH."/class/upfile.class.php");

//	if(empty($_SESSION['uc']['CompanyID'])) exit('Not login!');
        
        
	$companyidmsg = $_SESSION['ucc']['CompanyID'];
	$resPath  = setuppath($companyidmsg);
	$srcpath  = RESOURCE_NAME.$companyidmsg."/".$resPath."/";
	$backpath = $companyidmsg."/".$resPath."/";
	ini_set("html_errors", "0");
	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "警告:无效的文件";
		exit(0);
	}

	// 验证图片格式
	$sExtName = strtolower(end(explode('.', $_FILES["Filedata"]['name'])));
	if(!in_array($sExtName,array('jpg','jpeg'))){
		echo "警告:图片只能是JPG或JPEG格式！ ";
		exit(0);
	}

	$arrTryImgsize = getimagesize($_FILES["Filedata"]["tmp_name"]);
	if($arrTryImgsize===false || !in_array($arrTryImgsize['mime'],array('image/jpeg','image/pjpeg'))){
		echo "警告:请上传正确的JPG或JPEG格式的图片！ ";
		exit(0);
	}

	// Get the image and create a thumbnail
	$img = imagecreatefromjpeg($_FILES["Filedata"]["tmp_name"]);
	if (!$img) {
		echo "警告:无法创建图片 ";
		exit(0);
	}

	$width  = imageSX($img);
	$height = imageSY($img);

	if (!$width || !$height) {
		echo "警告:错误的图片信息";
		exit(0);
	}

	// Build the thumbnail
	/*$target_width  = 100;
	$target_height = 100;
	$target_ratio = $target_width / $target_height;

	$img_ratio = $width / $height;

	if ($target_ratio > $img_ratio) {
		$new_height = $target_height;
		$new_width = $img_ratio * $target_height;
	} else {
		$new_height = $target_width / $img_ratio;
		$new_width = $target_width;
	}

	if ($new_height > $target_height) {
		$new_height = $target_height;
	}
	if ($new_width > $target_width) {
		$new_height = $target_width;
	}

	$new_img = ImageCreateTrueColor(100, 100);
	if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, 0)) {	// Fill the image black
		echo "警告:不能创建新文件";
		exit(0);
	}

	if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
		echo "警告:不能改变图像大小";
		exit(0);
	}*/

	//if (!isset($_SESSION["file_info"])) {
		//$_SESSION["file_info"] = array();
	//}

	// Use a output buffering to load the image into a variable
	//ob_start();
	//imagejpeg($new_img);
	//$imagevariable = ob_get_contents();
	//ob_end_clean();

	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	//$_SESSION["file_info"][$file_id] = $imagevariable;


	$sFileName  = basename($_FILES['Filedata']['name']);
	$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) );
	$sExtension = strtolower( $sExtension );
	$currenttime = date("d_His")."_".currentTimeMillis()."".rand(10,99);
	$sFileName   = $currenttime.".".$sExtension;
        
	$f	= new upfile();
	$f->makeThumb($_FILES['Filedata']['tmp_name'], $srcpath."file_".$sFileName,140,120);  //上传第三方合同
	@chmod($srcpath."thumb_".$sFileName, 0777);
	$f->makeThumb($_FILES['Filedata']['tmp_name'], $srcpath."files_".$sFileName,680,6000);
	@chmod($srcpath."img_".$sFileName, 0777);
	@unlink($_FILES['Filedata']['tmp_name']);
        
	$filearr = null;
	$filearr['oldname']  = $_FILES['Filedata']['name'];
	$filearr['filesize']     = $_FILES['Filedata']['size'];
	$filearr['filename']  = $sFileName;
	$filearr['filepath']   = $backpath;
	$filearr['realpath']   =RESOURCE_PATH.$backpath."file_".$sFileName;
	//$_SESSION['file_upinfo'][$file_id] = $filearr;
//        print_r($filearr);die;
	//echo "FILEID:" . $file_id;	// Return the file id to the script
//        echo $filearr['filepath'];
	echo "FILEID:".json_encode($filearr);

?>