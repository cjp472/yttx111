<?php 
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!empty($in['ID']))
{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnID=".intval($in['ID'])." limit 0,1");
}elseif(!empty($in['SN'])){
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnSN='".$in['SN']."' limit 0,1");
}else{
	exit('错误参数!');
}

$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['ReturnClient']." limit 0,1");

$cartdata = $db->get_results("select c.*,i.Coding,i.Units from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ReturnID=".$oinfo['ReturnID']." order by ID asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
				<div style="margin-top:4px; margin-left:12px;"><input type="button" name="newbutton" id="newbutton" value=" 退货申请 " class="redbtn" onclick="javascript:window.location.href='return_add.php'" /> </div> 
   	        </div>            
			<div class="location"><strong>当前位置：</strong><a href="return.php">退货单管理</a> </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	
			<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">单号：<? echo $oinfo['ReturnSN'];?> <? if($oinfo['ReturnType']=="M") echo "(管理端代下单)";?>&nbsp;&nbsp;&nbsp;&nbsp;   状态：<? echo $return_status_arr[$oinfo['ReturnStatus']];?></div>
					<div class="rightdiv">申请时间：<? echo date("Y-m-d H:i",$oinfo['ReturnDate']);?></div>
				</div>

				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">退货单信息：</div>
					<div class="line bgw">
						<div class="line22 font12">客户信息</div>
<div class="line22"><strong>经 销 商：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
<div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
<div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>						
						
					</div>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">货物信息</div>
<div class="line22"><strong>相关订单：</strong><a href="order_manager.php?SN=<? echo $oinfo['ReturnOrder'];?>"><? echo $oinfo['ReturnOrder'];?></a></div>
<div class="line45"><strong>货运方式：</strong><? echo $oinfo['ReturnSendType'];?></div>
<div class="line45"><strong>货运说明：</strong><? echo $oinfo['ReturnSendAbout'];?></div>
<div class="line45"><strong>产品外观：</strong><? echo $oinfo['ReturnProductW'];?></div>
<div class="line45"><strong>产品包装：</strong><? echo $oinfo['ReturnProductB'];?></div>
					   </div>
<?php if(!empty($oinfo['ReturnPicture'])){?>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">图片：</div>
						<div class="line22">
							<img src="<?php echo RESOURCE_URL.$oinfo['ReturnPicture'];?>" />					
						</div>
					</div>
<?php }?>


					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">说明原因：</div>
						<div class="line22">
							<? echo nl2br($oinfo['ReturnAbout']);?>						
						</div>
					</div>
				</div>

					<br class="clearfloat" />
					<div class="border_line">
					<div class="line bgw">
						<div class="line22 font12">商品清单</div>
						<div class="line22">
						
  <table width="98%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="5%" height="28">&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
	<td width="10%">&nbsp;编号</td>
    <td width="10%">&nbsp;颜色</td>
    <td width="10%">&nbsp;规格</td>
    <td width="8%" align="right">数量</td>
	<td width="5%" align="right">单位</td>
    <td width="12%" align="right">单价</td>
    <td width="14%" align="right">价格(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td><div  title="<? echo $cvar['ContentName'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></div></td>
	<td>&nbsp;<? echo $cvar['Coding'];?> </td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
	<td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
		<td align="right" ><? echo $cvar['Units'];?>	</td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?> </td>
    <td class="font12" align="right">¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
   <? }?> 
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td>
<td class="font14">&nbsp;</td>

    <td class="font12" align="right">¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;</td>
  </tr>
   </tbody>
</table>
			<div class="line22" align="right">			
			<? if($oinfo['ReturnStatus'] < 3){ ?>
			<input type="button" value="修改退单商品" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='return_product_edit.php?ID=<? echo $oinfo['ReturnID'];?>'" />
			<? }?>
			&nbsp;&nbsp;
			<input type="button" value="打印退单" class="bluebtn" name="printbtn" id="print_confirmbtn" onclick="javascript:window.open( 'print.php?u=print_return&ID=<? echo $oinfo['ReturnID'];?>','_blank');" />&nbsp;&nbsp;
			<input type="button" value="导出退单" class="greenbtn" name="excelprintbtn" id="excel_confirmbtn" onclick="javascript:window.open( 'return_content_excel.php?ID=<? echo $oinfo['ReturnID'];?>','exe_iframe');" />&nbsp;&nbsp;
			</div>

				          </div>
						</div>
					</div>

			<?
			$cartbakinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_return_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".$oinfo['ReturnID']."' limit 0,1");
			if(!empty($cartbakinfo['allrow']))
			{
			?>
			<br class="clearfloat" />
			<div class="border_line">
			<div class="line bgw">
				<div class="line"><div class="leftdiv font14">原始退单：</div><div class="leftdiv" id="show_return_img" style="padding-left:24px; padding-top:2px; color:#277DB7; cursor: pointer;" ><img src="img/jia.gif" border="0" class="img" onclick="contral_list_return('show','<? echo $oinfo['ReturnID']; ?>');" /><span onclick="contral_list_return('show','<? echo $oinfo['ReturnID']; ?>');"> 展开 </span></div></div>
				<div class="line" style="display:none;" id="show_old_return_list"></div>				          
				</div>
			</div>
			<? }?>


				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">退单跟踪：</div>
					<div class="line bgw">
<div class="line22">
					<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
  <thead>
  <tr>
    <td width="20%" >&nbsp;时间</td>
    <td width="24%">&nbsp;用户</td>
    <td width="20%">&nbsp;动作</td>
    <td >说明</td>
  </tr>
   </thead>
   <tbody>
<?
	$submitdata = $db->get_results("select * from ".DATATABLE."_order_returnsubmit where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['ReturnID']." order by ID DESC");
	if($submitdata){
	foreach($submitdata as $ckey=>$cvar)
	{
?>	
  <tr id="linesub_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="28">&nbsp;<? echo date("Y-m-d H:i",$cvar['Date']);?> </td>
	<td>&nbsp;<? echo $cvar['AdminUser']." / ".$cvar['Name'];?></td>
    <td class="font12"><? echo $cvar['Status'];?>	</td>
	<td > <? echo $cvar['Content'];?> </td>
  </tr>
  <? }}?>
  </tbody>
  </table>
	</div>			</div>

					<br class="clearfloat" />
					<div class="line bgw">
					<div class="line22">
						<div class="line font12">操作(说明/原因)</div>
						<div class="line">
						<textarea name="data_OrderContent" rows="5"  id="data_OrderContent" style="width:80%; height:48px;"></textarea>
          				</div>
						<div class="line">
						<?
						echo OrderStatus($oinfo['ReturnStatus'],$oinfo['ReturnID']);		
						?>
						</div>
					</div>
					</div>
			</div>
					</form>
				</div>

        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>
<?
function OrderStatus($ostatus,$oid)
{
	 $ext = "";
 
	 switch($ostatus)
	 {
		case 0:
		{
			$ext = '
					<input type="button" value="审核通过" class="redbtn" name="confirmbtn1" id="confirmbtn1" onclick="do_return_status(\'Audit\',\''.$oid.'\')" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="不通过" class="bluebtn" name="confirmbtn2" id="confirmbtn2" id="confirmbtn" onclick="do_return_status(\'UnAudit\',\''.$oid.'\')" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="取消" class="greenbtn" name="confirmbtn2" id="confirmbtn3" id="confirmbtn" onclick="do_return_status(\'Cancel\',\''.$oid.'\')" />';
			break;
		}
		case 1:
		{
			$ext = '
					<input type="button" value="取消" class="greenbtn" name="confirmbtn2" id="confirmbtn3" id="confirmbtn" onclick="do_return_status(\'Cancel\',\''.$oid.'\')" />';
			break;
		}
		case 2:
		{
			$ext = '
					<input type="button" value="已收货" class="greenbtn" name="confirmbtn5" id="confirmbtn5" onclick="do_return_status(\'Incept\',\''.$oid.'\')" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="已完成" class="bluebtn" name="confirmbtn6" id="confirmbtn6" onclick="do_return_status(\'Over\',\''.$oid.'\')" />';
			break;
		}
		case 3:
		{
			$ext = '
					<input type="button" value="已完成" class="bluebtn" name="confirmbtn6" id="confirmbtn6" onclick="do_return_status(\'Over\',\''.$oid.'\')" />';
			break;
		}
		case 8:
		{
			$ext = '
					<input type="button" value="删除" class="redbtn" name="confirmbtn1" id="confirmbtn1" onclick="do_return_status(\'Delete\',\''.$oid.'\')"  />&nbsp;&nbsp;&nbsp;&nbsp;';
			break;
		}
		case 9:
		{
			$ext = '
					<input type="button" value="删除" class="redbtn" name="confirmbtn2" id="confirmbtn2" onclick="do_return_status(\'Delete\',\''.$oid.'\')"  />&nbsp;&nbsp;&nbsp;&nbsp;';
			break;
		}
		default: 
			$ext = "";
			break;
	}
	return $ext;
}
?>