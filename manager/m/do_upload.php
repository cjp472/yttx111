<?php
include_once ("header.php");
include_once ("../class/upfile.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="uploadimg")
{
	$companyidmsg = $_SESSION['uinfo']['ucompany'];
	$resPath   = setuppath($companyidmsg);

	$tmppath   = RESOURCE_PATH.$companyidmsg."/";
	$srcpath   = RESOURCE_PATH.$companyidmsg."/".$resPath."/";	
	$backpath  = $companyidmsg."/".$resPath."/";

	if(!empty($_SESSION['upfilename']))
	{

		if($_SESSION['upfilename']=='ext_error'){
			Error::Jump('警告:图片只能是gif、jpg、jpeg、png格式！',"../plugin/jqUploader/uploadfile.php");
		}else if($_SESSION['upfilename']=='type_error'){
			Error::Jump('警告:请上传正确的图片类型！',"../plugin/jqUploader/uploadfile.php");
		}else{
			$f		 = new upfile($tmppath);
			@chmod ($tmppath.$_SESSION['upfilename'], 0777);
			
			if($_SESSION['image_width'] < 2000){//最大的banner是2400，常规最大是1920
				$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."thumb_".$_SESSION['upfilename'],140,120);
				$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."img_".$_SESSION['upfilename'],1920,1920);
			}else{
				$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."thumb_".$_SESSION['upfilename'],140,120);
				$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."img_".$_SESSION['upfilename'],2400, 650);
			}
			@unlink($tmppath.$_SESSION['upfilename']);//删除临时文件
			Error::AlertSet("parent.setinputimg('".$backpath."thumb_".$_SESSION['upfilename']."')");
			exit('上传成功!');
		}
		
	}else{

		Error::Jump('上传失败!',"../plugin/jqUploader/uploadfile.php");
	}
} else if($in['m'] == 'upload_certify') {

    $certify = 'certify';
    $companyidmsg = $_SESSION['uinfo']['ucompany'];
    $companyidmsg = 'certify';
    $resPath   = setuppath($companyidmsg);
    $certify_resPath = setuppath($certify);

    $tmppath   = RESOURCE_PATH.$companyidmsg."/";

    $tmppath = RESOURCE_PATH.$_SESSION['uinfo']['ucompany']."/";

    $srcpath   = RESOURCE_PATH.$companyidmsg."/".$resPath."/";
    $backpath  = $companyidmsg."/".$resPath."/";
    if(!(file_exists (RESOURCE_PATH.$certify)))
    {
        _mkdir(RESOURCE_PATH,$certify);
        if(!(file_exists(RESOURCE_PATH.$certify.'/'.$certify_resPath))) {
            _mkdir(RESOURCE_PATH,$certify.'/'.$certify_resPath);
        }
    }

    if(!empty($_SESSION['upfilename']))
    {
        
		if($_SESSION['upfilename']=='ext_error'){
			Error::Jump('警告:图片只能是gif、jpg、jpeg、png格式！',"../plugin/jqUploader/upload_certify.php");
		}else if($_SESSION['upfilename']=='type_error'){
			Error::Jump('警告:您的信息已被记录，请勿上传恶意图片！',"../plugin/jqUploader/upload_certify.php");
		}else{
			$f		 = new upfile($tmppath);
			@chmod ($tmppath.$_SESSION['upfilename'], 0777);
			$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."thumb_".$_SESSION['upfilename'],140,120);
			$f->makeThumb($tmppath.$_SESSION['upfilename'], $srcpath."img_".$_SESSION['upfilename'],720,720);
			//@unlink($tmppath.$_SESSION['upfilename']);//删除临时文件
			//rename($srcpath."thumb_".$_SESSION['upfilename'],RESOURCE_PATH.$certify.'/'.$certify_resPath."/thumb_".$_SESSION['upfilename']);
			//copy($srcpath."thumb_".$_SESSION['upfilename'],RESOURCE_PATH.$certify.'/'.$certify_resPath."/thumb_".$_SESSION['upfilename']);
			//rename($srcpath."img_".$_SESSION['upfilename'],RESOURCE_PATH.$certify.'/'.$certify_resPath."/img_".$_SESSION['upfilename']);
			//copy($srcpath."img_".$_SESSION['upfilename'],RESOURCE_PATH.$certify.'/'.$certify_resPath."/img_".$_SESSION['upfilename']);
			$backpath = $certify.'/'.$certify_resPath.'/';
			Error::AlertSet("parent.setinputimg('".$backpath."thumb_".$_SESSION['upfilename']."')");
			exit('上传成功!');
		}

    }else{

        Error::Jump('上传失败!',"../plugin/jqUploader/upload_certify.php");
    }
}
elseif($in['m']=="uploadfile")
{
	//if(empty($_SESSION['upfilename'])) exit('非法操作!');
	$companyidmsg = $_SESSION['uinfo']['ucompany'];
	$resPath   = setuppath($companyidmsg);

	$tmppath   = RESOURCE_PATH.$companyidmsg."/";
	$srcpath   = RESOURCE_PATH.$companyidmsg."/".$resPath."/";	
	$backpath  = $companyidmsg."/".$resPath."/";
	
	if(!empty($_SESSION['upfilename']))
	{
		$f		 = new upfile($tmppath,$Site_Config['upfile']['annex']);
		@chmod ($tmppath.$_SESSION['upfilename'], 0777);
		$altmsg = $f->copytofile($tmppath.$_SESSION['upfilename'], $srcpath."file_".$_SESSION['upfilename']);
		@unlink($tmppath.$_SESSION['upfilename']);//删除临时文件
		if($altmsg=="ok")
		{
			Error::AlertSet("parent.setinputfile('".$backpath."file_".$_SESSION['upfilename']."')");
			exit('上传成功!');
		}else{
			Error::JumpJs($altmsg,"../plugin/jqUploader/uploadfileall.php");
		}
	}else{
		Error::JumpJs('上传失败!',"../plugin/jqUploader/uploadfileall.php");
	}
}
elseif($in['m']=="set_upload_mu_img")
{
	
	$arrFileupinfo = array();
	if(!empty($in['updata'])){
		$arrFileupinfoTemp = json_decode(htmlspecialchars_decode($in['updata']),true);
		if(is_array($arrFileupinfoTemp)){
			$arrFileupinfo = $arrFileupinfoTemp;
		}
	}

	$nMaxnum = 1;
	if($in['maxnum']){
		$nMaxnum = $in['maxnum'] + 1;
	}
	
	$upimgmsg = '';
	$out_thumb_msg = '';
	if(!empty($arrFileupinfo))
	{
		foreach($arrFileupinfo as $upkey=>$upvar)
		{
			if(!empty($upvar['filename']))
			{
				if(!empty($upimgmsg)) $upimgmsg .= "|";
				$upimgmsg .= $upvar['filepath']."thumb_".$upvar['filename'];
				$out_thumb_msg .= '<li id="mu_img_id_'.$nMaxnum.'" _filename="'.$upvar['filename'].'" _filepath="'.$upvar['filepath'].'" _filesize="'.$upvar['filesize'].'" _oldname="'.$upvar['oldname'].'"><a href_="'.RESOURCE_URL.$upvar['filepath'].'img_'.$upvar['filename'].'" target="_blank"><img src="'.RESOURCE_URL.$upvar['filepath'].'thumb_'.$upvar['filename'].'" title="'.$upvar['oldname'].'" width="70" height="70" border="0" /></a><br /><div class="checkbox thumbimg_dd_left" title="设为列表页默认图片"><input name="DefautlImg" type="radio" value="'.$nMaxnum.'"  />默认</div><div class="thumbimg_dd_div" onclick="remove_up_img(\''.$nMaxnum.'\')" title="删除图片">X</div></li>';

				$nMaxnum++;
			}
		}
		echo $out_thumb_msg;
	}
	exit();
}
/*elseif($in['m']=="remove_upload_mu_img")
{
	if(!empty($_SESSION['file_upinfo'][$in['rkey']]))
	{
		//@unlink(RESOURCE_PATH.$_SESSION['file_upinfo'][$in['rkey']]['filepath'].'thumb_'.$_SESSION['file_upinfo'][$in['rkey']]['filename']);
		//@unlink(RESOURCE_PATH.$_SESSION['file_upinfo'][$in['rkey']]['filepath'].'img_'.$_SESSION['file_upinfo'][$in['rkey']]['filename']);
		$_SESSION['file_upinfo'][$in['rkey']] = '';
		unset($_SESSION['file_upinfo'][$in['rkey']]);
	}
	exit();
}*/
elseif($in['m']=="uploadcontentexcel")
{
    //ini_set('memory_limit','128M');
    //ini_set('display_errors',1);
    //error_reporting(E_ALL^E_NOTICE);
    $companyidmsg = $_SESSION['uinfo']['ucompany'];
	//if(empty($in['data_SiteID']))  Error::AlertJs('请选择您要导入商品的分类！');

	require_once '../class/PHPExcel/IOFactory.php';
	$db	   = dbconnect::dataconnect()->getdb();

	$tmppath   = RESOURCE_PATH.$companyidmsg."/";
	
	$sFileName   = basename($_FILES['import_file']['name']);
	$sExtension  = 'xls';
	$currenttime = md5(date("Ymd_His")."_".time()."_".rand(100,999));
	$sFileName   = $currenttime.".".$sExtension;
	$uploadFile  = $tmppath.$sFileName;

	if(empty($_FILES['import_file']['name'])) Error::AlertJs('请先选择您要导入的数据文件!');
	if($sExtension!="xls") Error::AlertJs('只能导入EXCEL文件(扩展名：xls)');

	if ($_FILES['import_file']['name']) {
		if (move_uploaded_file ($_FILES['import_file']['tmp_name'], $uploadFile)) {
			@unlink($_FILES['import_file']["tmp_name"]);//删除临时文件
		}
	} else {
		if ($_FILES['import_file']['error']) {
		   Error::AlertJs($_FILES['import_file']['error']);
		   exit();
		}
	}
	$isok = false;
	$htmlmsg = '';
	$k = 1;
	if(file_exists($uploadFile))
	{

        //当前系统中已存在的商品编码
        if($in['model'] == 'append') {
            //追加模式保存excel中编码唯一且数据库中编码唯一
            $goods_codes = $db->get_col("SELECT DISTINCT Coding FROM ".DATATABLE."_order_content_index WHERE CompanyID={$companyidmsg}");
            $goods_codes = $goods_codes ? array_unique(array_filter($goods_codes)) : array();
        } else {
            $goods_codes = array();//覆盖模式保证excel中的编码唯一
        }


		$objReader	 = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($uploadFile);

		$casingmsg   = '';
        $site = array();//所有分类数组
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			foreach ($worksheet->getRowIterator() as $row)
			{
				if($row->getRowIndex() > 501 ) Error::AlertJs('一次最多只能导入500条记录！' . $row->getRowIndex());
				if($row->getRowIndex() > 1)
				{			
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					$linearr = null;
					foreach ($cellIterator as $cell) {
						if (!is_null($cell)) {
							$firstchar = substr($cell->getCoordinate(),0,1);
							$fieldvar  = trim($cell->getCalculatedValue());
							$linearr[$firstchar] = str_replace("'","",$fieldvar);
                            $linearr[$firstchar] = str_replace(array("\r\n","\r","\n"),'',$linearr[$firstchar]);
						}
					}
                    //echo "<script>" , "console.log('".$row->getRowIndex()." : ".$linearr['B']."');","</script>";
					if(empty($linearr['B'])) {
                        break;
                        //continue;
                    }


                    $linearr['K_SHOW'] = $linearr['K'];
                    //验证品类
                    if(!isset($site[md5($linearr['K'])])) {
                        //查询品类
                        $siteName = $linearr['K'];
                        $info = getSite($linearr['K']);
                        if($info) {
                            $linearr['K'] = $info['SiteID'];
                            $site[md5($linearr['K'])] = $info;
                        } else {
                            $linearr['K'] = 0;
                        }
                    } else {
                        $linearr['K'] = $site[md5($linearr['K'])]['SiteID'];
                    }
                    if(empty($linearr['K_SHOW'])) {
                        $linearr['K'] = '00';
                    }

                    if(empty($linearr['L'])) {
                        $linearr['L'] = 500;
                    }

                    $linedata = urlencode(json_encode($linearr));
                    if(empty($linearr['A']) && $in['model']!='append'){
                        $alert = '<font color="red" title="编号不能为空" > X </font>';
                        $linedata = '';
                    } else if($in['model'] == 'append' && !empty($linearr['A']) && in_array($linearr['A'],$goods_codes)) {
                        $alert = '<font color="red" title="编码已存在"> X </font>';
                        $linedata = '';
                    }else if($linearr['K'] === 0){
                        $alert = '<font color="red" title="未找到相应品类" > X </font>';
                        $linedata = '';
                    }

                    /*elseif(empty($linearr['D']) && empty($linearr['E'])){
						$alert = '<font color="red" title="价格不能为空" > X </font>';
						$linedata = '';
					}*/
                    elseif($linearr['D'] < 0 || $linearr['E'] < 0){
						$alert = '<font color="red" title="价格不能少于0" > X </font>';
						$linedata = '';
					}elseif(empty($linearr['F'])){
						$alert = '<font color="red" title="单位不能为空" > X </font>';
						$linedata = '';
					}else{
						$alert = '<font color=title_green_w>√</font>';
					}

                    $goods_codes[] = $linearr['A'];

					//$htmlmsg .= '<tr id="line_'.$k.'"><td align="center"><input name="kid[]" type="hidden" value="'.$k.'" />'.$k.'<input name="linedata[]" type="hidden" value="'.$linedata.'" /></td><td>'.$linearr['A'].'</td><td>'.$linearr['B'].'</td><td>'.$linearr['C'].'</td><td>'.$linearr['D'].'</td><td>'.$linearr['E'].'</td><td>'.$linearr['F'].'</td><td>'.$linearr['G'].'</td><td>'.$linearr['H'].'</td><td>'.$linearr['I'].'</td><td>'.$linearr['J'].'</td><td>'.$linearr['K'].'</td><td align="center">'.$alert.'</td><td align="center"><a href="javascript:void(0);" onclick="remove_content_line('.$k.');"><img src="img/icon_delete.gif" border="0" /></a></td></tr>';
					$htmlmsg .= '<tr id="line_'.$k.'">' .
					                '<td align="center">' .
					                    '<input name="kid[]" type="hidden" value="'.$k.'" />'.$k.'' .
					                    '<input name="linedata[]" type="hidden" value="'.$linedata.'" />' .
                                    '</td>' .
                                    '<td align="center">'.$alert.'</td>' .
                                    '<td align="center">' .
                                        '<a href="javascript:void(0);" onclick="remove_content_line('.$k.');">' .
                                            '<img src="img/icon_delete.gif" border="0" />' .
                                        '</a>' .
                                    '</td>' .
                                    '<td>'.$linearr['A'].'</td>' .
                                    '<td>'.$linearr['B'].'</td>' .
                                    '<td>'.$linearr['C'].'</td>' .
                                    '<td>'.$linearr['D'].'</td>' .
                                    '<td>'.$linearr['E'].'</td>' .
                                    '<td>'.$linearr['F'].'</td>' .
                                    '<td>'.$linearr['G'].'</td>' .
                                    '<td>'.$linearr['H'].'</td>' .
                                    '<td>'.$linearr['I'].'</td>' .
                                    '<td>'.$linearr['J'].'</td>' .
                                    '<td>'.$linearr['K_SHOW'].'</td>' .
                                    '<td>'.$linearr['L'].'</td>' .
                                    '<td>'.$linearr['M'].'</td>' .
                                    '<td>'.$linearr['N'].'</td>' .
                                    '<td>'.$linearr['O'].'</td>' .
									'<td>'.$linearr['P'].'</td>' .
									'<td>'.$linearr['Q'].'</td>' .
									'<td>'.$linearr['R'].'</td>' .
									'<td>'.$linearr['S'].'</td>' .
									'<td>'.$linearr['T'].'</td>' .
									'<td>'.$linearr['U'].'</td>' .
									'<td>'.$linearr['V'].'</td>' .
									'<td>'.$linearr['W'].'</td>' .
									'<td>'.$linearr['X'].'</td>' .
									'<td>'.$linearr['Y'].'</td>' . 
									'<td>'.$linearr['Z'].'</td>' .
                                '</tr>';
					$k++;

					$isok = true;
				}
			}
		}
	}

    $log_id = 0;//导入日志主键ID
	if(!$isok)
	{
		$htmlmsg = '<tr><td colspan="15">导入格式不正确，或者没有符合条件的数据！</td></tr>';
	} else {
        //有允许导入的数据　记录导入日志 , 不删xls文件
        $company_id = $_SESSION['uinfo']['ucompany'];
        $admin_user = $_SESSION['uinfo']['username'];
        $remark = "导入商品";
        $sql = "INSERT INTO ".DATATABLE."_order_import_log (CompanyID,File,Path,Status,Remark,AdminUser,UploadTime,ImportTime) VALUES ({$company_id},'{$sFileName}','{$tmppath}','F','{$remark}','{$admin_user}',".time().",0)";
        //TODO:: CODING
        $db->query($sql);
        $log_id = $db->insert_id;
    }
	//echo $htmlsmg;
	@unlink($uploadFile);//删除临时文件
	Error::AlertSet("parent.setimprotmsg('".$htmlmsg."','{$log_id}');");

} else if($in['m']=='uploadclientexcel') {
    //实现上传药店xls

    $companyidmsg = $_SESSION['uinfo']['ucompany'];
    require_once '../class/PHPExcel/IOFactory.php';
    $db	   = dbconnect::dataconnect()->getdb();

    $tmppath   = RESOURCE_PATH.$companyidmsg."/";

    $sFileName   = basename($_FILES['import_file']['name']);
    $sExtension  = 'xls';
    $currenttime = md5(date("Ymd_His")."_".time()."_".rand(100,999));
    $sFileName   = $currenttime.".".$sExtension;
    $uploadFile  = $tmppath.$sFileName;

    if(empty($_FILES['import_file']['name'])) Error::AlertJs('请先选择您要导入的数据文件!');
    if($sExtension!="xls") Error::AlertJs('只能导入EXCEL文件(扩展名：xls)');

    if ($_FILES['import_file']['name']) {
        if (move_uploaded_file ($_FILES['import_file']['tmp_name'], $uploadFile)) {
            @unlink($_FILES['import_file']["tmp_name"]);//删除临时文件
        }
    } else {
        if ($_FILES['import_file']['error']) {
            Error::AlertJs($_FILES['import_file']['error']);
            exit();
        }
    }
    $isok = false;
    $htmlmsg = '';
    $k = 1;
    if(file_exists($uploadFile))
    {

        //当前系统中已存在的药店编码
        $client_nos = $db->get_col("SELECT ClientNO FROM ".DATATABLE."_order_client WHERE ClientCompany=$companyidmsg");
        $client_nos = $client_nos ? array_unique(array_filter($client_nos)) : array();

        //当前系统中已存在的药店名称
        $client_names = $db->get_col("SELECT ClientCompanyName FROM ".DATATABLE."_order_client WHERE ClientCompany=" . $companyidmsg);
        $client_names = $client_names ? array_unique(array_filter($client_names)) : array();

        $objReader	 = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($uploadFile);

        $casingmsg   = '';
        $site = array();//所有分类数组
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            foreach ($worksheet->getRowIterator() as $row)
            {
                if($row->getRowIndex() > 510 ) Error::AlertJs('一次最多只能导入500条记录！');
                if($row->getRowIndex() > 1)
                {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $linearr = array();
                    foreach ($cellIterator as $cell) {
                        if (!is_null($cell)) {
                            $firstchar = substr($cell->getCoordinate(),0,1);
                            if($firstchar > 'T') {
                                break;
                            }
                            $fieldvar  = trim($cell->getCalculatedValue());
                            $linearr[$firstchar] = str_replace(array("'","\n"),"",$fieldvar);

                        }
                    }
                    if(empty($linearr['B']) && empty($linearr['C'])) {
                        continue;
                    }

                    $prefix = $_SESSION['uc']['CompanyPrefix'];
                    $linearr['C'] = str_replace($prefix . '-' , '',$linearr['C']);
                    $linedata = urlencode(json_encode($linearr));
                    $cnt = $db->get_var("SELECT count(*) as total FROM ".DATATABLE."_order_client WHERE ClientCompany=".$_SESSION['uinfo']['ucompany']." AND ClientName='".$prefix . '-' . $linearr['C']."' LIMIT 0,1");
                    if(empty($linearr['B'])) {
                        $alert = '<font color="red" title="药店名称不能为空!"> X </font>';
                        $linedata = '';
                    } else if(in_array($linearr['B'],$client_names)) {
                        $alert = '<font color="red" title="药店名称已存在!"> X </font>';
                        $linedata = '';
                    } else if(empty($linearr['C'])) {
                        $alert = '<font color="red" title="登录账号不能为空!"> X </font>';
                        $linedata = '';
                    } else if($cnt > 0) {
                        $alert = '<font color="red" title="药店已存在!"> X </font>';
                        $linedata = '';
                    } else if(!empty($linearr['A']) && in_array($linearr['A'],$client_nos)) {
                        $alert = '<font color="red" title="药店编号已存在!"> X </font>';
                        $linedata = '';
                    } //fixme 增加更多验证项
                    else {
                        $alert = '<font color=title_green_w> √ </font>';
                    }

                    $client_nos[] = $linearr['A'];
                    $client_names[] = $linearr['B'];

                    $htmlmsg .= '<tr id="line_'.$k.'">' .
                                    '<td align="center">' .
                                        '<input name="kid[]" type="hidden" value="'.$k.'" />'.$k.'' .
                                        '<input name="linedata[]" type="hidden" value="'.$linedata.'" />' .
                                    '</td>' .
                                    '<td align="center">' .
                                        '<a href="javascript:void(0);" onclick="remove_content_line('.$k.');">' .
                                            '<img src="img/icon_delete.gif" border="0" />' .
                                        '</a>' .
                                    '</td>' .
                                    '<td align="center">'.$alert.'</td>' .

                                    '<td>'.$linearr['A'].'</td>' .
                                    '<td>'.$linearr['B'].'</td>' .
                                    '<td>'.$linearr['C'].'</td>' .
                                    '<td>'.$linearr['D'].'</td>' .
                                    '<td>'.$linearr['E'].'</td>' .
                                    '<td>'.$linearr['F'].'</td>' .
                                    '<td>'.$linearr['G'].'</td>' .
                                    '<td>'.$linearr['H'].'</td>' .
                                    '<td>'.$linearr['I'].'</td>' .
                                    '<td>'.$linearr['J'].'</td>' .
                                    '<td>'.$linearr['K'].'</td>' .
                                    '<td>'.$linearr['L'].'</td>' .
                                    '<td>'.$linearr['M'].'</td>' .
                                    '<td>'.$linearr['N'].'</td>' .
                                    '<td>'.$linearr['O'].'</td>' .
                                    '<td>'.$linearr['P'].'</td>' .
                                    '<td>'.$linearr['Q'].'</td>' .
                                    '<td>'.$linearr['R'].'</td>' .
                                    '<td>'.$linearr['S'].'</td>' .
                                    '<td>'.$linearr['T'].'</td>' .
                                '</tr>';
                    $k++;
                    $isok = true;
                }
            }
        }
    }

    $log_id = 0;
    if(!$isok)
    {
        $htmlmsg = '<tr><td colspan="11">导入格式不正确，或者没有符合条件的数据！</td></tr>';
        @unlink($uploadFile);//删除临时文件
    } else {
        //有允许导入的数据　记录导入日志 , 不删xls文件
        $company_id = $_SESSION['uinfo']['ucompany'];
        $admin_user = $_SESSION['uinfo']['username'];
        $remark = "导入药店";
        $sql = "INSERT INTO ".DATATABLE."_order_import_log (CompanyID,File,Path,Status,Remark,AdminUser,UploadTime,ImportTime) VALUES ({$company_id},'{$sFileName}','{$tmppath}','F','{$remark}','{$admin_user}',".time().",0)";

        $db->query($sql);
        $log_id = $db->insert_id;
    }
    //echo $htmlsmg;

    Error::AlertSet("parent.setimprotmsg('".$htmlmsg."','".$log_id."')");


}

function getSite($name) {
    $db = dbconnect::dataconnect()->getdb();
    $companyID = $_SESSION['uinfo']['ucompany'];
    $info = $db->get_row("SELECT SiteID,SiteName FROM rsung_order_site WHERE CompanyID={$companyID} AND SiteName='{$name}' LIMIT 0,1");
    return $info ? $info : false;
}

exit('非法操作');
?>