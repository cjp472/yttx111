<?php 
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
if(empty($in['selectid'])) $in['selectid'] = '';

if(!empty($in['cid']))
{
	$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['cid'])." limit 0,1");
}else{
	exit('请先选择药店!');
}
if(!empty($in['kw']))
{	 
	$oinfo = $db->get_results("SELECT c.ContentID,c.ContentColor,c.ContentSpecification,c.ContentName,c.ContentNumber,c.ContentSend,c.ContentPercent,c.ContentPrice FROM ".DATATABLE."_order_orderinfo o inner join ".DATATABLE."_order_cart c on o.OrderID=c.OrderID inner join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and o.OrderUserID=".intval($in['cid'])." and (o.OrderSendStatus=3 or o.OrderSendStatus=4) and (c.ContentName like '%".$in['kw']."%' OR CONCAT(i.Coding,i.Barcode,i.Pinyi) like '%".$in['kw']."%') and i.FlagID=0 order by c.ID asc limit 0,2000");
}

if(!empty($oinfo))
{	
	$idarr = null;
	foreach($oinfo as $ovar)
	{
		if(!@in_array($ovar['ContentID'],$idarr))
		{
			$idarr[] = $ovar['ContentID'];
		}
		$kid = make_kid($ovar['ContentID'], $ovar['ContentColor'], $ovar['ContentSpecification']);
		
		if(empty($cartarr[$kid]))
		{
			$cartarr[$kid] = $ovar;
			$cartarr[$kid]['onumber'] = $ovar['ContentSend'];
		}else{
			$cartarr[$kid]['onumber'] = $cartarr[$kid]['onumber']+$ovar['ContentSend'];
		}
	}

	$cidmsg = implode(",", $idarr);
	if(strpos($cidmsg, ",")) $insqlmsg = " and r.ContentID in (".$cidmsg.") "; else $insqlmsg = " and r.ContentID = ".intval($cidmsg)." ";

	$sql_cr = "select r.ContentID,r.ContentName,r.ContentColor,r.ContentSpecification,r.ContentNumber from ".DATATABLE."_order_cart_return r left join ".DATATABLE."_order_returninfo i on r.ReturnID=i.ReturnID where i.ReturnCompany=".$_SESSION['uinfo']['ucompany']." and i.ReturnClient=".$in['cid']." and i.ReturnStatus!=1 and i.ReturnStatus!=8 and i.ReturnStatus!=9 ".$insqlmsg." order by r.ID desc";
	$returncart	= $db->get_results($sql_cr);

	$returnarr  = null;
	if(!empty($returncart))
	{
		foreach($returncart as $rc)
		{
			$kid = make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
			if(empty($returnarr[$kid]))
			{
				$returnarr[$kid] = $rc['ContentNumber'];
			}else{
				$returnarr[$kid] = $returnarr[$kid]+$rc['ContentNumber'];
			}
		}
	}
	
	foreach($cartarr as $ckey=>$cvar)
	{
		if(!empty($returnarr[$ckey]))
		{
			$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'] - $returnarr[$ckey];
		}else{
			$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'];
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
-->
</style>
</head>

<body>
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
          <tr>
            <form id="forms" name="forms" method="post" action="">
			
			<td width="10%" nowrap="nowrap"><strong>&nbsp;快速查询：</strong></td>
            <td width="18%" height="24" nowrap="nowrap">
              <label>
                <input type="text" name="kw" id="kw" size="20" value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();" />
              </label>           
            </td>
            <td width="15%"><label>
              <input name="button3" type="submit" class="bluebtn" id="button3" value="查 询" />
            </label></td> 
            </form>
			<td align="center"><strong><? if(!empty($cinfo['ClientCompanyName'])) echo $cinfo['ClientCompanyName'];?></strong></td>
          </tr>
        </table>

<form id="MainForm" name="MainForm" method="post" action="" target="" >

	<div style="width:100%; height:350px; overflow:auto;">
<? if(empty($oinfo)){?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
                 	<td height="50"  align="center" bgcolor="#FFFFFF">无符合条件的商品,请输入商品关键字查询!</td>
     			 </tr>
          </table>
<? }else{?>
          
			  <input type="hidden" name="selectid" id="selectid" value="<? if(!empty($in['selectid'])) echo $in['selectid'];?>" />
        	  <table width="96%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
                  <td width="5%" bgcolor="#efefef" align="center" title="全选/取消"><input type="checkbox" name="chkall" id="chkall" value="" onclick="CheckAll(this.form);" /></td>
                  <td bgcolor="#efefef" >商品名称</td>
                  <td width="14%" bgcolor="#efefef" >颜色</td>				  
                  <td width="14%"  bgcolor="#efefef" >规格</td>
                  <td width="10%" align="right" bgcolor="#efefef" >可退数</td> 
				  <td width="14%" align="right" bgcolor="#efefef" >订购价</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
if(!empty($cartarr))
{
	$n=1;
	foreach($cartarr as $ckey=>$cvar)
	{
		if(!empty($cvar['rnumber']) && $cvar['rnumber'] > 0)
		{
			$cvar['Price_End'] = $cvar['ContentPrice']*$cvar['ContentPercent']*0.1;
			$datamsg = urlencode(serialize($cvar));
?>
                <tr id="line_<? echo $ckey;?>"  >
                  <td height="22" align="center" bgcolor="#FFFFFF" ><input type="checkbox" name="cartkid[]" id="selectp_<? echo $ckey;?>" value="<? echo $ckey;?>" /><input type="hidden" value="<? echo $datamsg;?>" name="cartdata_<? echo $ckey;?>"  id="cartdata_<? echo $ckey;?>" /></td>
                  <td bgcolor="#FFFFFF" ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank" title="<? echo $cvar['ContentName'];?>" > <? echo $cvar['ContentName'];?></a></td>
                  <td bgcolor="#FFFFFF" >&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?></td>
                  <td align="right" bgcolor="#FFFFFF" ><? echo $cvar['rnumber'];?>&nbsp;</td>
				  <td align="right" bgcolor="#FFFFFF">¥ <? echo $cvar['Price_End'];?>&nbsp;</td>
              </tr>
<? } }}else{?>
     		  <tr>
       				 <td height="30" colspan="8" align="center" bgcolor="#FFFFFF">无符合条件的商品!</td>
   			  </tr>
<? }?>
 				</tbody>                
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              

			  <? }?>
       	  </div>

<? if(!empty($cartarr)){?>
          <table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">
     			 <tr>
                 	 <td height="35"><label>
                 	   <input type="button" name="button" id="buttonselected" value=" 选 中 " class="redbtn" onclick="add_select_product();" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value=" 取 消 " class="bluebtn"  onclick="parent.closewindowui()" />
               	     </label></td>

     			 </tr>
          </table> 
<? }?>
</form>
</body>
</html>
<?
	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(!empty($product_color))
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(!empty($product_spec))
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}
		return $kid;
	}
?>