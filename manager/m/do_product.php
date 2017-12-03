<?php
$menu_flag = "product";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("../class/letter.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');


if($in['m'] == 'check_coding') {
    //检测商品编码是否已存在
    $is_exists = empty($in['coding']) ? false : check_coding($db,$in['coding'],$in['id']);
    exit($is_exists ? ':-(' : 'ok');
}

if($in['m'] == 'search_branch'){

	$key = strval($in['key']);
	$sql = "select Logo from ".DATATABLE."_order_brand where Logo AND BrandName like '%".$key."%'";
	$result = $db->get_results($sql);
	echo json_encode($result);
	exit;
}

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法操作!');
	$in['ID'] = intval($in['ID']);
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_content_index set FlagID=1 where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="delarr")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$in['referer'] = html_entity_decode($in['referer']);
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "update ".DATATABLE."_order_content_index set FlagID=1 where ID IN ( ".$deleteidmsg." ) and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{		
		Error::outAdmin('成功下架!',$in['referer']);
	}else{
		Error::AlertJs('下架不成功!');
	}
}


//*****recycle************/
if($in['m']=="restore")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_content_index set FlagID=0 where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{	
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}

if($in['m']=="quite_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	if(DHB_RUNTIME_MODE === 'experience'){
		// 禁止删除系统商品 | 2015/07/20 by 小牛New
		$arrContentIndex = $db->get_row("select ID,IsSystem from ".DATATABLE."_order_content_index where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if(empty($arrContentIndex['ID'])){
			exit('待删除的数据不存在!');
		}
		
		if($arrContentIndex['IsSystem']=='1'){
			exit('体验版禁止删除系统预置商品，正式版无此功能限制!');
		}
	}

	$pguid = $db->get_row("SELECT GUID,ERP FROM ".DATATABLE."_order_content_index WHERE CompanyID = ".$_SESSION['uinfo']['ucompany']." AND ID=".$in['ID']." limit 0,1");
	if(!empty($pguid['GUID']) && ($pguid['ERP'] == 'T')) exit('该档案已与ERP软件关联，不能删除！');
	
	$cnum = $db->get_row("SELECT count(*) as pnum FROM ".DATATABLE."_order_cart where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['ID']." limit 0,1");
	if(!empty($cnum['pnum'])) exit('此商品已在使用，不能删除！');

	$nnum = $db->get_row("SELECT count(*) as pnum FROM ".DATATABLE."_order_storage_number where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['ID']." limit 0,1");
	if(!empty($nnum['pnum'])) exit('此商品已在使用，不能删除！');

	$upsql =  "delete from ".DATATABLE."_order_content_index where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany'];
	
	if($db->query($upsql))
	{
		$db->query("delete from ".DATATABLE."_order_content_1 where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentIndexID = ".$in['ID']." ");
		$db->query("delete from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID=".$in['ID']."");
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="restorearr")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$deleteidmsg = implode(",", $in['selectedID']);
	$in['referer'] = html_entity_decode($in['referer']);
	$upsql =  "update ".DATATABLE."_order_content_index set FlagID=0 where ID IN ( ".$deleteidmsg." ) and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		Error::outAdmin('操作成功!',$in['referer']);
	}else{
		Error::AlertJs('操作不成功!');
	}
}

if($in['m']=="quite_delete_arr")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$deleteidmsg = implode(",", $in['selectedID']);
	$status = false;
	$in['referer'] = html_entity_decode($in['referer']);
	foreach($in['selectedID'] as $svar)
	{
		$cnum = $db->get_row("SELECT count(*) as pnum FROM ".DATATABLE."_order_cart where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentID=".$svar." limit 0,1");
		$nnum = $db->get_row("SELECT count(*) as pnum FROM ".DATATABLE."_order_storage_number where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentID=".$svar." limit 0,1");
		$guid = $db->get_row("SELECT GUID,ERP FROM ".DATATABLE."_order_content_index WHERE CompanyID = ".$_SESSION['uinfo']['ucompany']." AND ID=".$svar." limit 0,1");
		
		if(!empty($guid['GUID']) && ($guid['ERP']) == 'T')
		    continue;
		
		if(empty($nnum['pnum']) && empty($cnum['pnum']))
		{
			$upsql =  "delete from ".DATATABLE."_order_content_index where ID = ".$svar." and CompanyID=".$_SESSION['uinfo']['ucompany'];
			if($db->query($upsql))
			{
				$db->query("delete from ".DATATABLE."_order_content_1 where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentIndexID = ".$svar." ");
				$db->query("delete from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID=".$svar." ");
				$status = true;				
			}			
		}
	}
	if($status)
	{
		Error::outAdmin('操作成功!',$in['referer']);
	}else{
		Error::AlertJs('商品已在使用或已与ERP软件关联，不能删除!');
	}
}

/***********save**************/
if($in['m']=="content_add_save")
{

	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
    $company_id = $_SESSION['uinfo']['ucompany'];
    if(!empty($in['data_Coding']) && check_coding($db,$in['data_Coding'],null)) {
        Error::AlertJs('当前商品编码 ‘ '.$in['data_Coding'].' ’ 已被使用!');
    }

	$in['data_Color'] = str_replace("，",",",$in['data_Color']);
	if(substr($in['data_Color'],-1,1)==",") $in['data_Color'] = substr($in['data_Color'],0,strlen($in['data_Color'])-1);
	$color_arr = explode(",",$in['data_Color']);
	$c_arr = array_unique($color_arr);
	$in['data_Color'] = implode(",",$c_arr);

	$in['data_Specification'] = str_replace("，",",",$in['data_Specification']);
	if(substr($in['data_Specification'],-1,1)==",") $in['data_Specification'] = substr($in['data_Specification'],0,strlen($in['data_Specification'])-1);
	$spec_arr = explode(",",$in['data_Specification']);
	$s_arr = array_unique($spec_arr);
	$in['data_Specification'] = implode(",",$s_arr);

	if($_SESSION['uinfo']['ucompany'] != "32" && $_SESSION['uinfo']['ucompany'] != "115")
	{
		if(empty($in['data_Color']) || empty($in['data_Specification']))
		{
			if(!empty($in['data_Color']) && !strpos($in['data_Color'],",")) Error::AlertJs('<<可选属性-可选颜色>>为同一商品有”多个可选颜色”时填写，单个颜色可直接写在商品名称上！');
			if(!empty($in['data_Specification']) && !strpos($in['data_Specification'],",")) Error::AlertJs('<<可选属性-可选规格>>为同一商品有”多个可选规格“时填写，单个规格可直接写在商品名称上！');
		}
	}
	
	if(empty($in['data_Price1'])) $in['data_Price1'] = $in['data_Price2'];
	if(empty($in['data_Price2'])) $in['data_Price2'] = $in['data_Price1'];
	$in['data_Price1'] = abs(floatval($in['data_Price1']));
	$in['data_Price2'] = abs(floatval($in['data_Price2']));

	$in['data_LibraryDown'] = abs(intval($in['data_LibraryDown']));
	$in['data_LibraryUp'] = abs(intval($in['data_LibraryUp']));

	$p3 = null;
	if(!empty($in['set_Price3']))
	{
		$p3 = unserialize(urldecode($in['set_Price3']));
	}
	if(!empty($in['set_Price4']))
	{
		$p3['clientprice'] = unserialize(urldecode($in['set_Price4']));
	}
	if(!empty($p3)) $in['data_Price3'] = urlencode(serialize($p3)); else $in['data_Price3'] = '';

	if(empty($in['FlagID'])) $in['data_FlagID'] = 0; else $in['data_FlagID'] = 1; //下架

	$arrDataThumb = $in['data_Thumb'];
	unset($in['data_Thumb']);

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('CompanyID', $_SESSION['uinfo']['ucompany']);
	$pinyima = '';
	if(!empty($in['data_Name']))
	{
	    $letter  = new letter();
        $pinyima = $letter->C($in['data_Name']);
		$data_->addData('Pinyi', $pinyima);
	}	
	$insert_id = $data_->dataInsert ("_order_content_index");

	if(!empty($insert_id))
	{
        //将商品ID写入GUID
        $db->query("UPDATE ".DATATABLE."_order_content_index SET GUID=ID WHERE ID=" . $insert_id . " AND CompanyID=" . $company_id . " AND GUID=''");

		/** 改进一下图片附件的问题 | 2015/06/29 by 小牛New */
		$arrFileupinfo = array();
		if(!empty($arrDataThumb)){
			$arrFileupinfoTemp = json_decode(htmlspecialchars_decode($arrDataThumb),true);
			if(is_array($arrFileupinfoTemp)){
				$arrFileupinfo = $arrFileupinfoTemp;
			}
		}
		
		$kk = 0;
		$defaultPicture = '';
		if(!empty($arrFileupinfo))
		{
			foreach($arrFileupinfo as $upkey=>$upvar)
			{
				if(empty($upvar['filename'])) continue;
				if($kk==0) $defaultPicture = $upvar['filepath'].'thumb_'.$upvar['filename'];
				$kk++;
				$db->query("insert into ".DATATABLE."_order_resource(CompanyID,IndexID,Name,OldName,Path,Size,OrderID) values(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",'".$upvar['filename']."','".$upvar['oldname']."','".$upvar['filepath']."','".$upvar['filesize']."',".$kk.")");
			}
			if(!empty($arrFileupinfo[$in['DefautlImg']]['filename'])) $defaultPicture = $arrFileupinfo[$in['DefautlImg']]['filepath'].'thumb_'.$arrFileupinfo[$in['DefautlImg']]['filename'];
		}		
		if(!empty($defaultPicture)) $db->query("update ".DATATABLE."_order_content_index set Picture='".$defaultPicture."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$insert_id);
		
		// 2015/06/30 重新更新一下图片的顺序
		if(!empty($arrFileupinfo))
		{	
			$kk = 0;

			foreach($arrFileupinfo as $upkey=>$upvar)
			{
				if(empty($upvar['filename'])) continue;
				$sFilename = $upvar['filepath'].$upvar['filename'];
				$kk++;

				$db->query("update ".DATATABLE."_order_resource set OrderID = ".($kk+10000)." where  CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexId = ".$insert_id." and concat(Path,Name)='{$sFilename}' ");
			}

			$db->query("update ".DATATABLE."_order_resource set OrderID = OrderID-10000 where  CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexId = ".$insert_id);
		}
			
		$FieldContent = '';
		$fcmsg = '';
		$valuearr = get_set_arr('field');
		if(!empty($valuearr))
		{
			foreach($valuearr as $k=>$v)
			{
				if(!empty($in[$k]))
				{
					$Filedarr[$k] = $in[$k];
					$fcmsg .= $in[$k]." ";
				}
			}
			$FieldContent = serialize($Filedarr);
		}
		$in['Deduct'] = floatval($in['Deduct']);
		$upsql = "Replace into ".DATATABLE."_order_content_1(ContentIndexID,CompanyID,ContentCreateDate,ContentEditDate,ContentCreateUser,ContentEditUser,ContentKeywords,Content,ContentPoint,Package,Deduct,FieldContent) values(".$insert_id.", ".$_SESSION['uinfo']['ucompany'].", ".time().", ".time().", '".$_SESSION['uinfo']['username']."','".$_SESSION['uinfo']['username']."','".$in['ContentKeywords']."','".$in['editor1']."', ".intval($in['ContentPoint']).", ".intval($in['Package']).",'".$in['Deduct']."','".$FieldContent."')";
		$db->query($upsql);

		//屏蔽药店
		if(!empty($in['Shield']))
		{
			$shieldarr1 = explode(",",$in['Shield']);
			$shieldarr  = array_unique($shieldarr1);
			$insql = "insert into ".DATATABLE."_order_shield(CompanyID,ClientID,ContentID) values ";
			foreach($shieldarr as $key=>$var)
			{				
				$insql .= "(".$_SESSION['uinfo']['ucompany'].",".intval($var).",".$insert_id."),";
			}
			if(substr($insql,-1,1)==',')
			{
				$insql = substr($insql,0,(strlen($insql)-1));
				$db->query($insql);
			}
		}
		//相关商品
		if(!empty($in['Relation']))
		{
			$rarr1 = explode(",",$in['Relation']);
			$rarr  = array_unique($rarr1);
			$insql = "insert into ".DATATABLE."_order_relation(CompanyID,FID,SID) values ";
			foreach($rarr as $key=>$var)
			{
				$insql .= "(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",".intval($var)."),";
			}
			if(substr($insql,-1,1)==',')
			{
				$insql = substr($insql,0,(strlen($insql)-1));
				$db->query($insql);
			}
		}
		
		//库存
		$db->query("insert into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",0,0)");
		if(!empty($in['data_Color']) || !empty($in['data_Specification']))
		{
			if(empty($in['data_Color']))
			{
				$colorarr[] = str_replace($fp,$rp,base64_encode("统一"));	
			}else{
				if(strpos($in['data_Color'], ","))
				{
					$in_color_arr = explode(",", $in['data_Color']);
					foreach($in_color_arr as $cvar)
					{
						$colorarr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
					}
				}else{
					$colorarr[]   = str_replace($fp,$rp,base64_encode($in['data_Color'])); 
				}
			}

			if(empty($in['data_Specification']))
			{
				$specarr[] = str_replace($fp,$rp,base64_encode("统一"));	
			}else{
				if(strpos($in['data_Specification'], ","))
				{
					$in_spec_arr = explode(",", $in['data_Specification']);
					foreach($in_spec_arr as $cvar)
					{
						$specarr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
					}
				}else{
					$specarr[]   = str_replace($fp,$rp,base64_encode($in['data_Specification'])); 
				}
			}
			$insql = "insert into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values";
			foreach($colorarr as $cv)
			{
				foreach($specarr as $sv)
				{
					$insql .= "(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",'".$cv."','".$sv."',0,0),";
				}
			}
			if(substr($insql,-1,1)==',')
			{
				$insql = substr($insql,0,(strlen($insql)-1));
				$db->query($insql);
			}
		}

		Error::AlertSet("parent.setinputok('ok')");
	}else{
		Error::AlertSet("parent.setinputok('error')");
	}

}

/***********editsave**************/
if($in['m']=="content_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
    $company_id = $_SESSION['uinfo']['ucompany'];
    $product_id = intval($in['update_id']);
    $product_info = $db->get_row("SELECT ID,GUID,ERP FROM ".DATATABLE."_order_content_index WHERE ID=" . $product_id . " AND CompanyID={$company_id} LIMIT 1");

    if(!empty($in['data_Coding']) && check_coding($db,$in['data_Coding'],$in['update_id'])) {
        Error::AlertJs('当前商品编码 ‘ '.$in['data_Coding'].' ’ 已被使用!');
    }


    $in['update_id'] = intval($in['update_id']);
	
	$in['data_Color'] = '';
	$color_arr = array();
	if(!empty($in['Color']))
	{
		$in['Color'] = str_replace("，",",",$in['Color']);
        $in['Color'] = rtrim($in['Color'],',');
		$color_arr = explode(",",$in['Color']);
	}

    $in['data_Color'] = implode(",",$color_arr);

	$in['data_Specification'] = '';
	$spec_arr = array();
	if(!empty($in['Specification']))
	{
		$in['Specification'] = str_replace("，",",",$in['Specification']);
        $in['Specification'] = rtrim($in['Specification'],",");
		$spec_arr = explode(",",$in['Specification']);	
	}

    $in['data_Specification'] = implode(",",$spec_arr);
	
	if(empty($in['data_Price1'])) $in['data_Price1'] = $in['data_Price2'];
	if(empty($in['data_Price2'])) $in['data_Price2'] = $in['data_Price1'];
	$in['data_Price1'] = abs(floatval($in['data_Price1']));
	$in['data_Price2'] = abs(floatval($in['data_Price2']));
	$p3 = null;
	$p4 = null;
	if(!empty($in['set_Price3']))
	{
		$p3 = unserialize(urldecode($in['set_Price3']));
	}
	if(!empty($in['set_Price4']))
	{
		$p3['clientprice'] = unserialize(urldecode($in['set_Price4']));
	}
	if(!empty($p3)) $in['data_Price3'] = urlencode(serialize($p3)); else $in['data_Price3'] = '';
	if(empty($in['FlagID'])) $in['data_FlagID'] = 0; else $in['data_FlagID'] = 1; //下架

	$arrDataThumb = $in['data_Thumb'];
	unset($in['data_Thumb']);

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$pinyima = '';
	if(!empty($in['data_Name']))
	{
	    $letter  = new letter();
        $pinyima = $letter->C($in['data_Name']);
		$data_->addData('Pinyi', $pinyima);
	}

    if($product_info["ERP"] == 'F' && empty($product_info['GUID'])) {
        $data_->addData("GUID",$product_id);
    }
	$where = " WHERE ID=".$in['update_id']." and CompanyID=".$_SESSION['uinfo']['ucompany'];


	/** 改进一下图片附件的问题 | 2015/06/29 by 小牛New */
	$arrFileupinfo = array();
	if(!empty($arrDataThumb)){
		$arrFileupinfoTemp = json_decode(htmlspecialchars_decode($arrDataThumb),true);
		if(is_array($arrFileupinfoTemp)){
			$arrFileupinfo = $arrFileupinfoTemp;
		}
	}

	$kk = 0;
	$defaultPicture = '';

	$arrDefaultImage1 = $arrDefaultImage2 = array();

	$arrDefaultImages = $db->get_results("select concat(Path,Name) as filefull from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID=".$in['update_id']."");
	if(is_array($arrDefaultImages)){
		foreach($arrDefaultImages as $infotem){
			$arrDefaultImage1[] = $infotem['filefull'];
		}
	}

	if(!empty($arrFileupinfo))
	{		

		foreach($arrFileupinfo as $upkey=>$upvar)
		{
			if(empty($upvar['filename'])) continue;
			if($upkey==0) $defaultPicture = $upvar['filepath'].'thumb_'.$upvar['filename'];

			$arrDefaultImage2[] = $upvar['filepath'].$upvar['filename'];
			if(in_array($upvar['filepath'].$upvar['filename'],$arrDefaultImage1)){
				continue;
			}

			$kk++;
			$db->query("insert into ".DATATABLE."_order_resource(CompanyID,IndexID,Name,OldName,Path,Size,OrderID) values(".$_SESSION['uinfo']['ucompany'].",".$in['update_id'].",'".$upvar['filename']."','".$upvar['oldname']."','".$upvar['filepath']."','".$upvar['filesize']."',".$kk.")");
		}

		if(!empty($arrFileupinfo[$in['DefautlImg']-1]['filename'])) $defaultPicture = $arrFileupinfo[$in['DefautlImg']-1]['filepath'].'thumb_'.$arrFileupinfo[$in['DefautlImg']-1]['filename'];
	}

	// 删除多余的图片
	$strDeleteSql = '';
	if(!empty($arrDefaultImage2)){
		$deleteWhereSql = array();
		foreach($arrDefaultImage2 as $val){
			$deleteWhereSql[] = " concat(Path,Name)!='{$val}' ";
		}
		if($deleteWhereSql){
			$strDeleteSql = " and ( ".implode(' and ',$deleteWhereSql)." ) ";
		}
	}

	$db->query("delete from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID=".$in['update_id']." ".$strDeleteSql);

	if(!empty($defaultPicture)) $db->query("update ".DATATABLE."_order_content_index set Picture='".$defaultPicture."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$in['update_id']);

	// 2015/06/30 重新更新一下图片的顺序
	if(!empty($arrFileupinfo))
	{	
		$kk = 0;

		foreach($arrFileupinfo as $upkey=>$upvar)
		{
			if(empty($upvar['filename'])) continue;
			$sFilename = $upvar['filepath'].$upvar['filename'];
			$kk++;

			$db->query("update ".DATATABLE."_order_resource set OrderID = ".($kk+10000)." where  CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexId = ".$in['update_id']." and concat(Path,Name)='{$sFilename}' ");
		}

		$db->query("update ".DATATABLE."_order_resource set OrderID = OrderID-10000 where  CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexId = ".$in['update_id']);
	}

	$data_->addData('Picture', $defaultPicture);
	$isindex = $data_->dataUpdate("_order_content_index", $where);

		$FieldContent = '';
		$fcmsg = '';
		$valuearr = get_set_arr('field');
		if(!empty($valuearr))
		{
			foreach($valuearr as $k=>$v)
			{
				if(!empty($in[$k]))
				{
					$Filedarr[$k] = $in[$k];
					$fcmsg .= $in[$k]." ";
				}
			}
			$FieldContent = serialize($Filedarr);
		}
		$in['Deduct'] = floatval($in['Deduct']);
		
		//以下SQL采用了Replace指令，需要先查询出创建时间 by wanjun
		$pcsql = "select ContentCreateDate from  ".DATATABLE."_order_content_1 where ContentIndexID=".intval($in['update_id'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
		$productInfo = $db->get_row($pcsql);
        if(empty($productInfo['ContentCreateDate'])) {
            $productInfo['ContentCreateDate'] = time();
        }
		
		$upsql = "Replace into ".DATATABLE."_order_content_1(ContentCreateDate,ContentIndexID,CompanyID,ContentEditDate,ContentEditUser,ContentKeywords,Content,ContentPoint,Package,Deduct,FieldContent) values(".$productInfo['ContentCreateDate'].", ".$in['update_id'].", ".$_SESSION['uinfo']['ucompany'].", ".time().",'".$_SESSION['uinfo']['username']."','".$in['ContentKeywords']."','".$in['editor1']."',".intval($in['ContentPoint']).", ".intval($in['Package']).",'".$in['Deduct']."','".$FieldContent."')";

		$iscontent = $db->query($upsql);
		
		$iscontent = true;
		if($isindex || $iscontent)
		{		
			//屏蔽药店
			$db->query("delete from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']."");
			if(!empty($in['Shield']))
			{
				$shieldarr1 = explode(",",$in['Shield']);
				$shieldarr  = array_unique($shieldarr1);
				$insql	=	"insert into ".DATATABLE."_order_shield(CompanyID,ClientID,ContentID) values";
				foreach($shieldarr as $key=>$var)
				{
					$insql .= "(".$_SESSION['uinfo']['ucompany'].",".intval($var).",".$in['update_id']."),";
				}
				if(substr($insql,-1,1)==',')
				{
					$insql = substr($insql,0,(strlen($insql)-1));
					$db->query($insql);
				}			
			}			
				
			//相关商品
			$db->query("delete from ".DATATABLE."_order_relation where CompanyID=".$_SESSION['uinfo']['ucompany']." and (FID=".$in['update_id']." OR SID=".$in['update_id'].")");
			if(!empty($in['Relation']))
			{
				$rarr1 = explode(",",$in['Relation']);
				$rarr  = array_unique($rarr1);
				$insql = "insert into ".DATATABLE."_order_relation(CompanyID,FID,SID) values ";
				foreach($rarr as $key=>$var)
				{
					if($in['update_id'] == $var)  continue;
					$insql .= "(".$_SESSION['uinfo']['ucompany'].",".$in['update_id'].",".intval($var)."),";
				}
				if(substr($insql,-1,1)==',')
				{
					$insql = substr($insql,0,(strlen($insql)-1));
					$db->query($insql);
				}
			}			

// 		//库存
// 		if(!empty($in['data_Color']) || !empty($in['data_Specification']))
// 		{
// 			if(empty($in['data_Color']))
// 			{
// 				$colorarr[] = str_replace($fp,$rp,base64_encode("统一"));	
// 			}else{
// 				if(strpos($in['data_Color'], ","))
// 				{
// 					$in_color_arr = explode(",", $in['data_Color']);
// 					foreach($in_color_arr as $cvar)
// 					{
// 						$colorarr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
// 					}
// 				}else{
// 					$colorarr[]   = str_replace($fp,$rp,base64_encode($in['data_Color'])); 
// 				}
// 			}

// 			if(empty($in['data_Specification']))
// 			{
// 				$specarr[] = str_replace($fp,$rp,base64_encode("统一"));	
// 			}else{
// 				if(strpos($in['data_Specification'], ","))
// 				{
// 					$in_spec_arr = explode(",", $in['data_Specification']);
// 					foreach($in_spec_arr as $cvar)
// 					{
// 						$specarr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
// 					}
// 				}else{
// 					$specarr[]   = str_replace($fp,$rp,base64_encode($in['data_Specification'])); 
// 				}
// 			}

// 			$numarr = $db->get_results("SELECT ContentColor,ContentSpec FROM ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']." ");
// 			foreach($numarr as $nv)
// 			{
// 				$colory[] = $nv['ContentColor'];
// 				$specy[]  = $nv['ContentSpec'];
// 				if (!in_array($nv['ContentColor'], $colorarr))
// 				{
// 					$db->query("delete from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']." and ContentColor='".$nv['ContentColor']."'");
// 				}
// 				if (!in_array($nv['ContentSpec'], $specarr))
// 				{
// 					$db->query("delete from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']." and ContentSpec='".$nv['ContentSpec']."'");
// 				}
// 			}
// 			foreach($colorarr as $cv)
// 			{
// 				if (!in_array($cv, $colory))
// 				{					
// 					foreach($specarr as $sv)
// 					{
// 						$db->query("insert into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$in['update_id'].",'".$cv."','".$sv."',0,0)");
// 					}
// 				}
// 			}
// 			foreach($specarr as $sv)
// 			{
// 				if (!in_array($sv, $specy))
// 				{					
// 					foreach($colorarr as $cv)
// 					{
// 						$db->query("insert into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$in['update_id'].",'".$cv."','".$sv."',0,0)");
// 					}
// 				}
// 			}
// 			//重新统计总库存		
// 			$sqlsum = "select sum(OrderNumber) as ototal,sum(ContentNumber) as ctotal from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']."";
// 			$totalrow = $db->get_row($sqlsum);
// 			if(!empty($totalrow))
// 			{
// 				$db->query("update ".DATATABLE."_order_number set OrderNumber=".$totalrow['ototal'].", ContentNumber=".$totalrow['ctotal']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']." limit 1");
// 			}
// 		}else{
// 			$db->query("delete from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['update_id']." ");
// 		}
					
		if(!empty($_COOKIE['backurl'])) $backurl = $_COOKIE['backurl']; else $backurl = "product.php?sid=".$in['data_SiteID'];
		Error::AlertSet("parent.setinputeditok('ok','".$backurl."')");
	}else{
		Error::AlertSet("parent.setinputeditok('error','')");
	}
	exit();
}

if($in['m']=="update_order")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::Alert('对不起，您没有此项操作权限！');
	if(!empty($in['ID']))
	{
		$upsql = "update ".DATATABLE."_order_content_index set OrderID=".$in['orderid']." where ID = ".$in['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']."";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('error');
		}
	}
}

/***********move**************/
if($in['m']=="moveContent")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::Alert('对不起，您没有此项操作权限！');
	$in['referer'] = html_entity_decode($in['referer']);
	if(!empty($in['selectedID']) && !empty($in['targetNodeID']))
	{		
		$moveidmsg = implode(",", $in['selectedID']);
		$upsql = "update ".DATATABLE."_order_content_index set SiteID=".$in['targetNodeID']." where ID in (".$moveidmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany']."";
		if($db->query($upsql))
		{
			Error::outAdmin('移动成功!',$in['referer']);
		}else{
			Error::AlertJs('移成不成功!');
		}
	}else{
		Error::AlertJs('请选择您要移动的商品和目标分类!');
	}
}

//批量修改
if($in['m']=="muleditContent")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::Alert('对不起，您没有此项操作权限！');
	$in['referer'] = html_entity_decode($in['referer']);
	if(!empty($in['selectedID']) && !empty($in['targetNodeID']))
	{		
		$moveidmsg = implode(",", $in['selectedID']);
		$indata    = explode("^^",$in['targetNodeID']);
		$fieldarr  = array('BrandID','SiteID','Casing','CommendID','OrderID','Units','LibraryDown','LibraryUp');
		$fieldarr2 = array('Package','ContentPoint','Deduct');

		if($indata[1] != '' || !empty($indata[1]))
		{
			if($indata[0] == 'CommendID' || $indata[0] == 'OrderID' || $indata[0] == 'Package' || $indata[0] == 'ContentPoint' || $indata[0] == 'LibraryDown' || $indata[0] == 'LibraryUp') $indata[1] = intval($indata[1]);
			if(@in_array($indata[0],$fieldarr))
			{
				$upsql = "update ".DATATABLE."_order_content_index set ".$indata[0]." = '".$indata[1]."' where ID in (".$moveidmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			}
			if(@in_array($indata[0],$fieldarr2))
			{
				$iniddata = $db->get_col("select ContentIndexID from ".DATATABLE."_order_content_1 where ContentIndexID in (".$moveidmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany']." ");
				$chaarr = array_diff($in['selectedID'], $iniddata);
				foreach($chaarr as $v)
				{
					$db->query("Replace into ".DATATABLE."_order_content_1(ContentIndexID,CompanyID,ContentEditDate,ContentEditUser,ContentKeywords,Content,ContentPoint,Package,Deduct,FieldContent) values(".$v.", ".$_SESSION['uinfo']['ucompany'].", ".time().",'".$_SESSION['uinfo']['username']."','','',0, 0,'0','')");
				}
				$upsql = "update ".DATATABLE."_order_content_1 set ".$indata[0]." = '".$indata[1]."' where ContentIndexID in (".$moveidmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			}
			if($db->query($upsql)) Error::outAdmin('执行成功!',$in['referer']);
		}
		Error::AlertJs('执行不成功!'.$indata[1]);
	}else{
		Error::AlertJs('请选择您要操作的对像!');
	}
}


if($in['m']=="update_commend")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::Alert('对不起，您没有此项操作权限！');
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$in['referer'] = html_entity_decode($in['referer']);
	$deleteidmsg = implode(",", $in['selectedID']);
	if(empty($in['set_commend'])) $in['set_commend']= 0;

	if($in['set_commend'] != "9")
	{
		$data9 = $db->get_results("SELECT i.ID,i.Name,n.ID as nID,n.ClientID,n.Email,n.Mobile FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_notice n on i.ID=n.ProductID where i.CompanyID=n.CompanyID and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CommendID=9 and i.ID in (".$deleteidmsg .") ");
		
		if(!empty($data9))
		{
			$valuearr = get_set_arr('sms');
			if(empty($valuearr))  $valuearr = array("0");

			$idmsg = "0";
			foreach($data9 as $dvar)
			{
				if(!empty($dvar['Mobile']))
				{
					$message = "【".$_SESSION['uc']['CompanySigned']."】商品:".$dvar['Name']."已到货,请登陆<<".$_SESSION['uc']['CompanyName'].">>订货平台订购.";
					if(!empty($valuearr) && in_array("5", $valuearr))
					{	
						sms::send_sms($dvar['Mobile'],$message,$dvar['ClientID']);
						$db->query("update ".DATATABLE."_order_notice set Flag='1' where ID=".$dvar['nID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1");
					}					
				}
			}
		}
	}

	$upsql =  "update ".DATATABLE."_order_content_index set CommendID=".$in['set_commend']." where ID IN ( ".$deleteidmsg." ) and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		Error::outAdmin('执行成功!',$in['referer']);
	}else{
		Error::AlertJs('执行不成功!');
	}
}


/***********save_sort**************/
if($in['m']=="save_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SiteName']))
	{
		if(!is_numeric($in['SiteOrder'])) $in['SiteOrder'] = 0;

	    $letter  = new letter();
        $pinyima = $letter->C($in['SiteName']);

		$upsql = "insert into ".DATATABLE."_order_site(CompanyID,ParentID,SiteOrder,SiteName,SitePinyi,SiteAdmin,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ParentID'].", ".$in['SiteOrder'].", '".$in['SiteName']."','".$pinyima."','".$_SESSION['uinfo']['username']."','".$in['Content']."')";
		if($db->query($upsql))
		{
			$insiteid = mysql_insert_id();
			if(!empty($in['ParentID']))
			{
				$pinfo = $db->get_row("select SiteID,ParentID,SiteNO FROM ".DATATABLE."_order_site where SiteID=".$in['ParentID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
				if(!empty($pinfo['SiteNO'])) $sno = $pinfo['SiteNO'].$insiteid.".";
			}else{
				$sno = "0.".$insiteid.".";
			}
			$db->query("update ".DATATABLE."_order_site set SiteNO='".$sno."' where SiteID=".$insiteid." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1");
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SiteID']))
	{
		if($in['SiteID'] == $in['ParentID']) exit('您不能选上级分类为本分类的子类！');
		if(!is_numeric($in['SiteOrder'])) $in['SiteOrder'] = 0;

	    $letter  = new letter();
        $pinyima = $letter->C($in['SiteName']);

		$pinfo = $db->get_row("select SiteID,ParentID,SiteNO FROM ".DATATABLE."_order_site where SiteID=".$in['ParentID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$sinfo = $db->get_row("select SiteID,ParentID,SiteNO FROM ".DATATABLE."_order_site where SiteID=".$in['SiteID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");

		if(strpos($pinfo['SiteNO'],$sinfo['SiteNO']) !== false) exit('您不能选择上级分类作他的下级分类的子分类！');
		if(empty($pinfo['SiteNO'])) $pinfo['SiteNO'] = '0.';
		$sno = $pinfo['SiteNO'].$sinfo['SiteID'].".";

		$upsql = "update ".DATATABLE."_order_site set ParentID=".$in['ParentID'].",SiteNO='".$sno."', SiteOrder=".$in['SiteOrder'].", SiteName='".$in['SiteName']."', SitePinyi='".$pinyima."',SiteAdmin='".$_SESSION['uinfo']['username']."',Content='".$in['Content']."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and SiteID=".$sinfo['SiteID']." limit 1";
		$soninfo = $db->get_results("select SiteID,ParentID,SiteNO FROM ".DATATABLE."_order_site where SiteNO like '".$sinfo['SiteNO']."%' and CompanyID=".$_SESSION['uinfo']['ucompany']." ");

		if($db->query($upsql))
		{
			if(!empty($soninfo))
			{
				foreach($soninfo as $var)
				{
					if($sinfo['SiteID'] == $var['SiteID']) continue;
					$sonno = str_replace($sinfo['SiteNO'],$sno,$var['SiteNO']);
					$db->query("update ".DATATABLE."_order_site set SiteNO='".$sonno."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and SiteID=".$var['SiteID']." limit 1");
				}
			}
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}
}

if($in['m']=="delete_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['SiteID'] = intval(trim($in['SiteID']));
	if(!empty($in['SiteID']))
	{
		$sortcount = $db->get_row("SELECT count(*) as countsite FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and ParentID=".$in['SiteID']." limit 0,1");
		if(!empty($sortcount['countsite'])) exit("请先删除下级分类!(逐级删除)");

		$procount = $db->get_row("SELECT count(*) as countpro FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".$in['SiteID']." limit 0,1");
		if(!empty($procount['countpro'])) exit("该分类已在使用，请先删除该分类下的商品!(包含已下架的商品)");

		$upsql = "delete from ".DATATABLE."_order_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and SiteID=".$in['SiteID']." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}else{
		exit('请指定你要删除的分类!');
	}
}


/********************************/
if($in['m']=="notice_message")
{
	if(!intval($in['ID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
		
	$data9 = $db->get_row("SELECT i.ID,i.Name,n.ID as nID,n.ClientID,n.Email,n.Mobile FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_notice n on i.ID=n.ProductID where i.ID=".$in['ID']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and n.Flag='0' limit 0,1");
	
	if(!empty($data9))
	{
		$valuearr = get_set_arr('sms');
		if(empty($valuearr))  $valuearr = array("0");

		if(!empty($valuearr) && in_array("5", $valuearr))
		{
			if(!empty($data9['Mobile']))
			{
				$message = "【".$_SESSION['uc']['CompanySigned']."】“".$data9['Name']."”已到货,请登录<<".$_SESSION['uc']['CompanyName'].">>订货平台订购.";
				sms::send_sms($data9['Mobile'],$message,$data9['ClientID']);
			}
			$upsql =  "update ".DATATABLE."_order_notice set Flag='1' where ID = ".$data9['nID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
			$db->query($upsql);
			exit('ok');
		}else{
			exit('您未开启到货短信通知，请先在系统设置里开启!');		
		}
	}else{
		exit('操作不成功!');
	}
}

if($in['m']=="quite_notice_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "delete from ".DATATABLE."_order_notice where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="delete_notice_arr")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$deleteidmsg = implode(",", $in['selectedID']);
	$status = false;
	$in['referer'] = html_entity_decode($in['referer']);

	if(!empty($deleteidmsg))
	{
		$upsql  =  "delete from ".DATATABLE."_order_notice where ID in (".$deleteidmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany'];
		$status = $db->query($upsql);
	}
	if($status)
	{
		Error::outAdmin('操作成功!',$in['referer']);
	}else{
		Error::AlertJs('操作不成功!');
	}
}

if($in['m']=="notice_message_arr")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$in['referer'] = html_entity_decode($in['referer']);
	$deleteidmsg = implode(",", $in['selectedID']);
	$status = false;

	$data9 = $db->get_results("SELECT i.ID,i.Name,n.ID as nID,n.ClientID,n.Email,n.Mobile FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_notice n on i.ID=n.ProductID where i.CompanyID=n.CompanyID and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and n.ID in (".$deleteidmsg.") ");
		
	if(!empty($data9))
	{
		$valuearr = get_set_arr('sms');
		if(empty($valuearr))  $valuearr = array("0");
		if(!empty($valuearr) && in_array("5", $valuearr))
		{
			$idmsg = "0";
			foreach($data9 as $dvar)
			{
				if(!empty($dvar['Mobile']))
				{
					$idmsg .=",".$dvar['nID'];
					$message = "【".$_SESSION['uc']['CompanySigned']."】商品:".$dvar['Name']."已到货,请登陆<<".$_SESSION['uc']['CompanyName'].">>订货平台订购.";
					if(!empty($setinfo['SetValue']) && in_array("5", $valuearr))
					{	
						sms::send_sms($dvar['Mobile'],$message,$dvar['ClientID']);
					}
				}
			}
			$db->query("update ".DATATABLE."_order_notice set Flag='1' where ID in (".$idmsg.") and CompanyID=".$_SESSION['uinfo']['ucompany']."");
			Error::outAdmin('操作成功!',$in['referer']);
		}else{
			Error::AlertJs('您未开启到货短信通知，请先在系统设置里开启!');
		}
	}else{
		Error::AlertJs('操作不成功!');
	}
}


if($in['m']=="set_save_level_price")
{
	$valuearr = null;
	$setarr = null;
	$setmsg = '';
	$valuearr = get_set_arr('clientlevel');

	if(!empty($valuearr))
	{
		foreach($in as $k=>$v)
		{
			if(substr($k,0,5) == "level") 
			{
				$linevar = abs(floatval($v));
				if(!empty($linevar))  $setarr[$k] = sprintf("%01.2f", round($linevar,2));
			}
		}

		if(!empty($setarr))
		{
			$setarr['typeid'] =  $in['typeid'];
			$setmsg = urlencode(serialize($setarr));
		}
		echo $setmsg;
	}
	exit('');
}

//设置药店价
if($in['m']=="do_set_save_client_price")
{
	$setarr = null;
	$setmsg = '';
	if(!empty($in['vmsg']))
	{
		$setarr =  unserialize(urldecode($in['vmsg']));
	}
	
	if(!empty($in['cid']))
	{
		if(empty($in['clientprice']))
		{
			unset($setarr[$in['cid']]);
		}else{
			$in['clientprice']   = abs(floatval($in['clientprice']));
			$setarr[$in['cid']]  = sprintf("%01.2f", round($in['clientprice'],2));
		}
	}
	$setmsg = urlencode(serialize($setarr));
	echo $setmsg;
	exit;
}

if($in['m']=="update_price")
{
	if(!intval($in['pid'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['nprice'] = abs(floatval($in['nprice']));
	if($in['pricen']=="Price1")
	{
		$upsql =  "update ".DATATABLE."_order_content_index set Price1='".$in['nprice']."' where ID = ".$in['pid']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
	}else{
		$upsql =  "update ".DATATABLE."_order_content_index set Price2='".$in['nprice']."' where ID = ".$in['pid']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
	}
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('价格无变化!');
	}
}

if($in['m']=="do_save_change_level_price")
{
	if(empty($in['pid'])) exit('参数错误!');
	$valuearr = null;
	$setarr = null;
	$setmsg = '';

	$valuearr = get_set_arr('clientlevel');
	if(!empty($valuearr))
	{
		foreach($in as $k=>$v)
		{
			if(substr($k,0,5) == "level") 
			{
				$linevar = abs(floatval($v));
				if(!empty($linevar))  $setarr[$k] = sprintf("%01.2f", round($linevar,2));
			}
		}
		$productinfo = $db->get_row("SELECT ID,Price3 FROM ".DATATABLE."_order_content_index  where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['pid'])." limit 0,1");
		$parr = unserialize(urldecode($productinfo['Price3']));

		if(!empty($setarr))
		{
			$setarr['clientprice'] = $parr['clientprice'];
			$setarr['typeid'] =  $in['typeid'];
			$setmsg = urlencode(serialize($setarr));			
		}
		$upsql =  "update ".DATATABLE."_order_content_index set Price3='".$setmsg."' where ID = ".intval($in['pid'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('价格无变化');
		}
	}
	exit('价格无变化');
}

//指定药店价
if($in['m']=="do_save_change_client_price")
{
	if(empty($in['cid']) || empty($in['pid'])) exit('参数错误!');

	$in['clientprice'] = abs(floatval($in['clientprice']));
	$in['clientprice'] = sprintf("%01.2f", round($in['clientprice'],2));
	$parr = null;

	$productinfo = $db->get_row("SELECT ID,Price3 FROM ".DATATABLE."_order_content_index  where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['pid'])." limit 0,1");
	$parr = unserialize(urldecode($productinfo['Price3']));

	if(empty($in['clientprice']) || $in['clientprice'] <=0)
	{
		unset($parr['clientprice'][$in['cid']]);
	}else{
		$parr['clientprice'][$in['cid']] = $in['clientprice'];
	}

	if(!empty($parr))
	{
		$setmsg = urlencode(serialize($parr));			

		$upsql =  "update ".DATATABLE."_order_content_index set Price3='".$setmsg."' where ID = ".intval($in['pid'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('价格无变化');
		}
	}
	exit('价格无变化');
}


if($in['m']=="sub_add_client")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectclient']))
	{	
		$comma_separated = implode(",", $in['selectclient']);
		$clientdata = $db->get_results("SELECT ClientID,ClientCompanyName,ClientCompanyPinyi FROM ".DATATABLE."_order_client where ClientID in (".$comma_separated.") and ClientCompany = ".$_SESSION['uinfo']['ucompany']."  and ClientFlag=0  ");
		if(!empty($clientdata))
		{
			foreach($clientdata as $cvar)
			{
				$cvar['ClientCompanyName'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['ClientCompanyName']);
				$cvar['ClientCompanyName'] = str_replace('"',"“",$cvar['ClientCompanyName']);
				if(!empty($cvar['ClientCompanyName'])) $jsmsg .= '<option value=\"'.$cvar['ClientID'].'\">'.$cvar['ClientCompanyName'].'</option>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
		}else{
			$omsg .= '{"backtype":"请先选择您要屏蔽的药店!"}';
		}
	}else{
		$omsg .= '{"backtype":"请先选择您要屏蔽的药店!"}';
	}
	echo $omsg;
	exit();
}

if($in['m']=="sub_add_level")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectlevel']))
	{	
		foreach($in['selectlevel'] as $lv)
		{
			if(substr($lv,0,1)=="A")
			{
				$sqlmsg = " and (ClientLevel like '%%".$lv."%%' or ClientLevel = '".substr($lv,2)."')";
			}else{
				$sqlmsg = " and ClientLevel like '%%".$lv."%%' ";
			}
			$clientdata = $db->get_results("SELECT ClientID,ClientCompanyName,ClientCompanyPinyi FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  and ClientFlag=0  ");
			if(!empty($clientdata))
			{
				foreach($clientdata as $cvar)
				{
					$cvar['ClientCompanyName'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['ClientCompanyName']);
					$cvar['ClientCompanyName'] = str_replace('"',"“",$cvar['ClientCompanyName']);
					if(!empty($cvar['ClientCompanyName'])) $jsmsg .= '<option value=\"'.$cvar['ClientID'].'\">'.$cvar['ClientCompanyName'].'</option>';
				}
			}
		}
		$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
	}else{
		$omsg .= '{"backtype":"请先选择您要屏蔽的药店!"}';
	}
	echo $omsg;
	exit();
}

if($in['m'] == "remove_mul_img")
{
	if(empty($in['rkey']))	exit("参数错误 ！");
	$iid = substr($in['rkey'],0,strpos($in['rkey'],"_"));
	$oid = substr($in['rkey'],strpos($in['rkey'],"_")+1);
	$ddata = $db->get_row("select IndexID,Path,Name from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = ".$iid." and OrderID = ".$oid." limit 0,1");
	if(!empty($ddata))
	{		
		//@unlink(RESOURCE_PATH.$ddata['Path'].'thumb_'.$ddata['Name']);
		//@unlink(RESOURCE_PATH.$ddata['Path'].'img_'.$ddata['Name']);
		$st    = $db->query("delete from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = ".$iid." and OrderID = ".$oid." limit 1");
		$idata = $db->get_row("select ID,Picture from ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID = ".$iid." limit 0,1");
		if($idata['Picture'] == $ddata['Path'].'thumb_'.$ddata['Name'])
		{
			$dpic = $db->get_row("select IndexID,Path,Name from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = ".$iid." order by OrderID asc limit 0,1 ");
			if(!empty($dpic['Name'])) $pictue = $dpic['Path'].'thumb_'.$dpic['Name']; else $pictue = '';
			$setd = $db->query("update ".DATATABLE."_order_content_index set Picture='".$pictue."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID = ".$iid." limit 1");
		}
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m'] == "setdefault_mul_img")
{
	if(empty($in['rkey']))	exit("参数错误 ！");
	$iid = substr($in['rkey'],0,strpos($in['rkey'],"_"));
	$oid = substr($in['rkey'],strpos($in['rkey'],"_")+1);
	$ddata = $db->get_row("select IndexID,Path,Name from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = ".$iid." and OrderID = ".$oid." limit 0,1");
	if(!empty($ddata))
	{	
		$pictue = $ddata['Path'].'thumb_'.$ddata['Name'];
		$setd   = $db->query("update ".DATATABLE."_order_content_index set Picture='".$pictue."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID = ".$iid." limit 1");
		exit('设置成功！');
	}else{
		exit('设置不成功！');
	}
}

if($in['m'] == "set_list_mu_img")
{
	$out_thumb_msg = '';
	$iid = substr($in['idname'],strpos($in['idname'],"_")+1);
	$iid = intval($iid);

	$arrFileupinfo = array();
	if(!empty($in['updata'])){
		$arrFileupinfoTemp = json_decode(htmlspecialchars_decode($in['updata']),true);
		if(is_array($arrFileupinfoTemp)){
			$arrFileupinfo = $arrFileupinfoTemp;
		}
	}

	if($arrFileupinfo){
		$kk = 0;

		$tryTemp = $db->get_row("select OrderID from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = {$iid} order by OrderID DESC limit 0,1");

		if(!empty($tryTemp['OrderID'])) $kk = $tryTemp['OrderID'];

		foreach($arrFileupinfo as $upkey=>$upvar)
		{
			if(empty($upvar['filename'])) continue;
			$kk++;
			$result = $db->query("insert into ".DATATABLE."_order_resource(CompanyID,IndexID,Name,OldName,Path,Size,OrderID) values(".$_SESSION['uinfo']['ucompany'].",".$iid.",'".$upvar['filename']."','".$upvar['oldname']."','".$upvar['filepath']."','".$upvar['filesize']."',".$kk.")");

			$upkey = $iid.'_'.$kk;
			$smsg = '';
			$isdefault = "N";
			$pv  = $upvar['filepath']."thumb_".$upvar['filename'];
			if($kk == "1")
			{
				$setd = $db->query("update ".DATATABLE."_order_content_index set Picture='".$pv."' where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID = ".$iid." and Picture='' limit 1");
				$smsg = ' checked="checked" ';	
			}
			$out_thumb_msg .= '<li id="mu_img_'.$upkey.'"><a href_="'.RESOURCE_URL.str_replace("thumb_","img_",$pv).'" target="_blank"><img src="'.RESOURCE_URL.$pv.'"  width="70" height="70" border="0" /></a><br /><div class="checkbox thumbimg_dd_left" title="设为列表页默认图片"><input name="DefautlImg_'.$iid.'" type="radio" value="'.$upkey.'" '.$smsg.' onclick="setdefault_mul_img(\''.$upkey.'\',\''.$isdefault.'\')" />默认</div><div class="thumbimg_dd_div" onclick="remove_mul_img(\''.$upkey.'\',\''.$isdefault.'\')" title="删除图片">X</div></li>';
		}

		echo $out_thumb_msg;
	}
	exit();
}

/** 图片排序功能 | 2015/06/30 by 小牛New */
if($in['m'] == "set_list_img_order")
{
	$nProductId = intval($in['idname']);
	$sSortdata = trim($in['sortdata']);

	if(empty($nProductId) || empty($sSortdata)){
		exit("参数错误");
	}

	$arrSortTemp = explode('|',$sSortdata);
	$nLength = count($arrSortTemp);
	foreach($arrSortTemp as $nkey=>$sTemp){
		$sTemp = explode('_',$sTemp);
		$nOrder = array_pop($sTemp);
		$db->query("update ".DATATABLE."_order_resource set OrderID = ".($nkey+1+10000)." where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = {$nProductId} and OrderID = {$nOrder} ");
	}
	
	$db->query("update ".DATATABLE."_order_resource set OrderID = OrderID-10000 where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID = {$nProductId}");

	exit('排序保存成功');
}

//品牌
if($in['m']=="brand_add_save")
{
	$setinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BrandNO='".$in['data_BrandNO']."' limit 0,1");
	if(!empty($setinfo['allrow'])) exit('编号不能重复！');
	$letter  = new letter();
    $pinyima = $letter->C($in['data_BrandName']);
	$isin = $db->query("insert into ".DATATABLE."_order_brand(BrandNO,BrandName,BrandPinYin,CompanyID) values('".$in['data_BrandNO']."','".$in['data_BrandName']."','".$pinyima."',".$_SESSION['uinfo']['ucompany'].")");
	if($isin)
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="brand_edit_save")
{
	if(empty($in['update_id'])) exit('参数错误 ，请指定您要修改的内容！');
	$setinfo = $db->get_row("SELECT 1 FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BrandNO='".$in['edit_BrandNO']."' and BrandID != ".$in['update_id']." limit 0,1");
	if(!empty($setinfo)) exit('编号不能重复！');
	$letter  = new letter();
    $pinyima = $letter->C($in['edit_BrandName']);
    
    //是否推荐
    $IsIndex = (int)(isset($in['IsIndex']) && intval($in['IsIndex']));
	$isin = $db->query("replace into ".DATATABLE."_order_brand(BrandID,BrandNO,BrandName,BrandPinYin,CompanyID,IsIndex,Logo) values(".$in['update_id'].",'".$in['edit_BrandNO']."','".$in['edit_BrandName']."','".$pinyima."',".$_SESSION['uinfo']['ucompany'].",".$IsIndex.", '".$in['brand_logo']."')");
	if($isin)
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="delete_brand")
{
	if(empty($in['ID'])) exit('参数错误 ，请指定您要删除的内容！');
	$in['ID'] = intval($in['ID']);
	$setinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BrandID='".$in['ID']."' limit 0,1");
	if(!empty($setinfo['allrow'])) exit('此品牌下面有商品，不能删除！');
	
	$isin = $db->query("delete from ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BrandID = ".$in['ID']." limit 1");
	if($isin)
	{
		exit("ok");
	}else{
		exit("删除不成功!");
	}
}

if($in['m']=="sub_add_relation")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectrelation']))
	{	
		$comma_separated = implode(",", $in['selectrelation']);
		$clientdata = $db->get_results("SELECT ID,Coding,Name FROM ".DATATABLE."_order_content_index where ID in (".$comma_separated.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." ");
		if(!empty($clientdata))
		{
			foreach($clientdata as $cvar)
			{
				$cvar['ClientCompanyName'] = $cvar['Name'];
				$cvar['ClientCompanyName'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['ClientCompanyName']);
				$cvar['ClientCompanyName'] = str_replace('"',"“",$cvar['ClientCompanyName']);
				if(!empty($cvar['ClientCompanyName'])) $jsmsg .= '<option value=\"'.$cvar['ID'].'\">'.$cvar['ClientCompanyName'].'</option>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
		}else{
			$omsg .= '{"backtype":"请先选择您要关联的商品!"}';
		}
	}else{
		$omsg .= '{"backtype":"请先选择您要关联的商品!"}';
	}
	echo $omsg;
	exit();
}

if($in['m'] == 'content_import_save')
{

    $product_flag = array(
        '0' => '默认',
        '1' => '推荐',
        '2' => '特价',
        '3' => '新款',
        '4' => '热销',
        '8' => '赠品',
        '9' => '缺货',
    );
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$sid = intval($in['sid']);
	if(empty($in['linedata'])) Error::AlertJs('没有符合条件商品数据可导入！');
	//if(empty($sid)) Error::AlertJs('请先选择商品分类！');
	$letter  = new letter();
	$k = 0;
    
	$sqlheader = "insert into `".DATATABLE."_order_content_index`(`CompanyID`,`SiteID`,`BrandID`,`OrderID`,`Name`,`Pinyi`,`Coding`,`Barcode`,`Price1`,`Price2`,`Units`,`Casing`,`Picture`,`Color`,`Specification`,`Model`,`GUID`,`CommendID`) values ";
    $sqlheader1 = "insert into ".DATATABLE."_order_content_1 (CompanyID,ContentIndexID,ContentKeywords,FieldContent,Package) VALUES ";
	$sqlbody = '';
    $bodyArr = array();
    $bodyContentArr = array();//商品辅助表
    $codingArr = array();
    $appendCoding = array();//导入商品的所有编号
    $dataAsoc = array();
    $spec_color = "";
    $spec_specification = "";
    $company_id = $_SESSION['uinfo']['ucompany'];
    
    /*start 自定义字段入库 add by lxc 2015-08-13*/
    $fields   = get_set_arr('field');
    $fieldarr = array_keys($fields);
    $filesname = array('P','Q','R','S','T','U','V','W','X','Y');
    /* end */
    
	foreach($in['linedata'] as $v){
		$lvs = json_decode(urldecode($v));
		$lv = (array)$lvs;
        if(empty($lv['A']) && $in['model']!='append'){
            //编号不能为空
            continue;
        }

        foreach($lv as $ads_k => $ads_v) {
            $lv[$ads_k] = htmlentities(($ads_v),ENT_QUOTES ,"UTF-8");
        }

		if(!empty($lv['B']) && !empty($lv['F'])){
			$lv['D'] = abs(floatval($lv['D']));
			$lv['E'] = abs(floatval($lv['E']));
            //if(empty($lv['K'])) $lv['K'] = 500; //K为分类ID
            if(empty($lv['L'])) $lv['L'] = 500;
			if(empty($lv['E'])) $lv['E'] = $lv['D'];
			if(empty($lv['D'])) $lv['D'] = $lv['E'];

			$lv['H'] = str_replace("，",",",$lv['H']);
			$lv['I'] = str_replace("，",",",$lv['I']);
            $lv['K'] = intval($lv['K']);

            $spec_color .= ",".$lv['H'];
            $spec_specification .= ",".$lv['I'];


            $codingArr[$lv['A']] = "'".$lv['A']."'";

            $lv['M'] = $brand_id = parse_brand($db,$lv['M']);
            $lv['O'] = $commend_id = (int)array_search($lv['O'],$product_flag);

            $lv['N'] = $tag = $lv['N'];
            
            /*start 自定义字段入库 add by lxc 2015-08-13*/
            if(!empty($fieldarr)){
                $customfiled=array();
                foreach($fieldarr as $key => $item){
                    $customfiled[$item] = $lv[$filesname[$key]];
                }
                $lv['P'] = serialize($customfiled);
            }
            /* end */
            
            /*start 整包装出货数 add by lxc 2015-12-02*/
            $lv['Z'] = abs(intval($lv['Z']));
            /* end */

			$pinyima = $letter->C($lv['B']);
			//$sqltempbody = "(".$_SESSION['uinfo']['ucompany'].",{$sid},0,{$lv['K']},'{$lv['B']}','{$pinyima}','{$lv['A']}','{$lv['C']}','{$lv['D']}','{$lv['E']}','{$lv['F']}','{$lv['G']}','','{$lv['H']}','{$lv['I']}','{$lv['J']}')";
			$sqltempbody = "(".$_SESSION['uinfo']['ucompany'].",{$lv['K']},{$brand_id},{$lv['L']},'{$lv['B']}','{$pinyima}','{$lv['A']}','{$lv['C']}','{$lv['D']}','{$lv['E']}','{$lv['F']}','{$lv['G']}','','{$lv['H']}','{$lv['I']}','{$lv['J']}','{$lv['A']}',{$commend_id})";
            $bodyArr[$k] = $sqltempbody;
            $bodyContentArr[$k] = "({$company_id},'{ContentIndexID}', '{$tag}','{$lv['P']}',{$lv['Z']})";
            $appendCoding[$k] = $lv['A'];
            $dataAsoc[$k] = $lv;
			$k++;			
		}
	}

    specification($spec_color,'Color');
    specification($spec_specification,'Specification');

    $ipt_msg = "追加模式";
    $updateArr = array();
    if($in['model']=='cover'){
        $ipt_msg = "覆盖模式";
        //覆盖模式
        //获取是更新的数据
    	$codingmsg = implode(',',$codingArr);
    	$codingmsg = implode(',',$appendCoding);
    	$codingmsg = ','.$codingmsg.',';
        $product_list = $db->get_results("SELECT Coding,ID FROM ".DATATABLE."_order_content_index WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." and instr('{$codingmsg}', concat(',',Coding,',')) > 0 ");
        //$updateArr = $db->get_col("SELECT Coding FROM ".DATATABLE."_order_content_index WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." and instr(concat(',',Coding,','), '{$codingmsg}') > 0 ");
        $product_list = $product_list ? $product_list : array();

        $updateArr = array_column($product_list,'Coding',null);
        $updateArr = $updateArr ? $updateArr : array();

        $updateKey = array();
        foreach($updateArr as $v){
            $updateKey[$idx] = $idx = array_search($v,$appendCoding);
            if($idx!==false){
                unset($bodyArr[$idx]);
            }
        }
        if(is_array($updateArr) && count($updateArr)>0){
            //导入的更新 XXX: 未更新颜色规格
            $c_r_i = array_column($product_list,'ID','Coding');
            $content_ids = $db->get_col("SELECT ContentIndexID FROM ".DATATABLE."_order_content_1 WHERE CompanyID={$company_id}");
            $content_ids = $content_ids ? $content_ids : array();
            foreach($updateKey as $v){
                $lv = $dataAsoc[$v];
                $updateSql = "UPDATE ".DATATABLE."_order_content_index SET `SiteID`={$lv['K']},`CommendID`={$lv['O']},`BrandID`={$lv['M']}, `Pinyi`='".$letter->C($lv['B'])."',`Name`='".$lv['B']."',`Barcode`='".$lv['C']."',`Price1`=".$lv['D'].",`Price2`=".$lv['E'].",`Units`='".$lv['F']."',`Casing`='".$lv['G']."',`Model`='".$lv['J']."',`OrderID`=".$lv['L']." WHERE Coding='".$lv['A']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']." AND ID=".$c_r_i[$lv['A']];
                $db->query($updateSql);
                if(isset($c_r_i[$lv['A']]) && in_array($c_r_i[$lv['A']],$content_ids)) {
                    //更新
                    $db->query("UPDATE ".DATATABLE."_order_content_1 SET ContentKeywords='{$lv['N']}',FieldContent='{$lv['P']}',Package={$lv['Z']} WHERE ContentIndexID=".$c_r_i[$lv['A']]." LIMIT 1");
                } else {
                    //插入
                    $db->query("INSERT INTO ".DATATABLE."_order_content_1 (CompanyID,ContentIndexID,ContentKeywords,FieldContent,Package) VALUES ({$company_id},".$c_r_i[$lv['A']].",'{$lv['N']}','{$lv['P']}',{$lv['Z']})");
                }     
            }
        }
    }

    if(count($bodyArr) > 0){
        foreach($bodyArr as $bk=>$bv) {
            $db->query($sqlheader . $bv);
            $db->query($sqlheader1 . str_replace('{ContentIndexID}',$db->insert_id,$bodyContentArr[$bk]));
            echo $db->last_query . '\n';
        }
    }

    /*
    $sqlbody = implode(',',$bodyArr);
	if(!empty($sqlbody)){
        $db->query($sqlheader.$sqlbody);
    }
    */
    $sql = "UPDATE ".DATATABLE."_order_import_log SET Status='T',ImportTime=".time().",Remark=concat(Remark,'=>','".$ipt_msg."') WHERE ID=" . $in['log_id'];
    $db->query($sql);

	exit('ok');
}

/**
 * @desc 添加颜色&规格
 * @param string $str (黑,红,绿)
 * @param string $specType
 */
function specification($str,$specType='Color'){
    global $db;
    $spec = explode(',',$str);
    $spec = array_filter($spec);
    $spec = array_unique($spec);
    $specApos = array_map("addApos",$spec);
    $exists = $db->get_results("SELECT SpecName FROM ".DATATABLE."_order_specification WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND SpecType='".$specType."' AND SpecName IN (".implode(',',$specApos).")");
    $exists = $exists ? $exists : array();
    $spec = array_diff($spec,$exists);
    if(count($spec)>0){
        $sqlheader = "INSERT INTO ".DATATABLE."_order_specification (`SpecName`,`SpecType`,`CompanyID`) VALUES";
        $sqlbody = array();
        foreach($spec as $k=>$v){
            $sqlbody[] = "('".$v."','".$specType."',".$_SESSION['uinfo']['ucompany'].")";
        }
        $db->query($sqlheader.implode(',',$sqlbody));
    }
    unset($specApos,$exists,$list);
}

/**
 * @desc 为字符串添加单引号
 * @param string $item 操作的字符串
 * @return string 添加单引号的字符串
 */
function addApos($item){
    return "'".$item."'";
}

function parse_brand($db,$brand_name) {
    $company_id = $_SESSION['uinfo']['ucompany'];
    $brand_id = $db->get_var("SELECT BrandID FROM ".DATATABLE."_order_brand WHERE CompanyID={$company_id} AND BrandName='{$brand_name}' LIMIT 1");
    return (int)$brand_id;
}

function cli_log($title,$msg) {
    if(is_array($msg)) {
        $str = json_encode($msg);
    } else {
        $str = $msg;
    }
    $str = str_replace(array("'",'"'),array("’",'”'),$str);
    echo "<script>",'console.log("'.$title . ':' . $str .'");',"</script>";
}

/**
 * @desc 检查商品编码是否存在
 * @param $db
 * @param $coding
 * @param null $id
 * @return bool
 * @author hxtgirq
 * @since 2015-09-08
 */
function check_coding($db,$coding,$id = null) {
    $company_id = $_SESSION['uinfo']['ucompany'];
    $where = " WHERE CompanyID=" . $company_id . " AND Coding='{$coding}'";
    if($id) {
        $where .= " AND ID <> " . (int)$id;
    }
    $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATATABLE."_order_content_index " . $where . " LIMIT 1");
    return $cnt > 0;
}

exit('非法操作');
?>