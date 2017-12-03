<?php
/**
 * my
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/listdata.php");
	 
$input		=	new Input;
$in			=	$input->parse_incoming();

if($in['m']=="addtowishlist")
{
	if(empty($in['pid'])) exit('请指定要添加的商品!');
	if(empty($_SESSION['cc']['cid'])) exit('error');
	
	$tr = listdata::addtowishlist($in['pid']);
	if($tr)
	{
		exit('ok');
	}else{
		exit('此商品已在您的收蒧夹中, 点此可以 <a href="wishlist.php">查看</a> 我的收蒧的商品......');
	}	

}
elseif($in['m']=="removewishlist")
{
	if(empty($in['pid'])) exit('请指定要移除的商品!');
	
	$tr = listdata::removetowishlist($in['pid']);
	if($tr)
	{
		exit('ok');
	}else{
		exit('移除失败......');
	}
}
else
{
	$iswish = '1';
	$urlmsg     =   "";

	$setarr   = commondata::getproductset();
	if(empty($in['t']))
	{
		$in['t']  = 'textlist';
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
	if($pn=="off") $pns = "off";

	if(empty($in['o']))
	{
		$in['o']  = "";
	}else{
		$urlmsg  .= "&o=".$in['o'];
	}
		
	if($sv=="default")
	{
		$listsite    = listdata::listsite(0,0);
	}else{
		$listallsite    = listdata::listallsite(0,0);
	}
	$goodslist = listdata::wishlist($in['o'],$in['t'],$in['ps'],$pn);

	include template("wishlist");
}

?>