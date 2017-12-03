<?php
/**
 * List
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/listdata.php");
$input		=	new Input;
$in			=	$input->parse_incoming();
$urlmsg		=   "";
$location	=   null;
setcookie("backurl", $_SERVER['REQUEST_URI']);
$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );

if(empty($in['s']))
{
	$SiteID   = 0;
	$in['s']  = 0;
	$SiteInfo['ParentID'] = 0;
}else{
	$SiteInfo = listdata::getsiteinfo($in['s']);
	$SiteID   = $SiteInfo['SiteID'];
	$urlmsg  .= "&s=".$SiteID;
	$location = listdata::getlocationinfo($SiteInfo);
}

$setarr   = commondata::getproductset();
if(empty($in['t']))
{
	if(!empty($_SESSION['showtype']))
	{
		$in['t']  = $_SESSION['showtype'];
	}
	elseif(!empty($setarr['producttype']))
	{
		$in['t']  = $setarr['producttype'];
	}else{
		$in['t']  = 'imglist';
	}
}else{
	$urlmsg  .= "&t=".$in['t'];
}
$_SESSION['showtype'] = $in['t'];

if(!empty($setarr['product_number']))
{
	$pn  = $setarr['product_number'];
}else{
	$pn  = 'off';
}
if(!empty($setarr['product_negative']))
{
	$png  = $setarr['product_negative'];
}else{
	$png  = 'off';
}

if(!empty($setarr['product_number_show']))
{
	$pns  = $setarr['product_number_show'];
}else{
	$pns  = 'off';
}
if($pn=="off" && $pns == "off") $iss = "off"; else $iss = "on";

if(empty($in['o']))
{
	$in['o']  = "";
}else{
	$urlmsg  .= "&o=".$in['o'];
}

if($sv=="default")
{
	$listsite     = listdata::listsite($SiteInfo['ParentID'],$SiteID);
}else{
	$listallsite  = listdata::listallsite($SiteInfo['ParentID'],$SiteID);
}

if(!empty($in['kw'])){
	
	if($in['json']=="json"){
		$brandlist  = commondata::getbrandlist(10,'brand.php',$in['kw']);
		header('Content-type: application/json');
		echo json_encode($brandlist);exit;
	}else{
		$brandlist  = commondata::getbrandlist(18,'brand.php',$in['kw']);
	}
}else{
	$brandlist  = commondata::getbrandlist();
}

include template("unicom+brand");
	
?>