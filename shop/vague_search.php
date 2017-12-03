<?
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
$urlmsg     =   "";

$in['kw']   = urlencode($in['kw']);
$enkw       = urldecode($in['kw']);
$urlmsg    .= "&kw=".$in['kw'];

$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );

$setarr   = commondata::getproductset();
if(empty($in['t']))
{
	if(!empty($setarr['producttype']))
	{
		$in['t']  = $setarr['producttype'];
	}else{
		$in['t']  = 'imglist';
	}
}else{
	$urlmsg  .= "&t=".$in['t'];
}

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
	$listsite     = listdata::listsite(0.0);
}else{
	$listallsite  = listdata::listallsite(0,0);
}

$goodslist  = listdata::list_vague_search($in['kw'],$in['o'],$in['t'],10,$iss);
header('Content-type: application/json');
echo json_encode($goodslist);
?>