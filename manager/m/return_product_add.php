<?php 
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$valuearr = get_set_arr('product');
if(!empty($valuearr['return_type']) && $valuearr['return_type']=="order")
{
	header("Location: return_add.php");
}

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

if(empty($in['cid'])) $in['cid'] = $clientdata[0]['ClientID'];	

$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['cid'])." limit 0,1");

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
			<div class="location"><strong>当前位置：</strong><a href="return.php">退货单管理</a> &#8250;&#8250; <a href="#">退货单申请</a></div>
		</div>
    	
        <div class="line2"></div>
        <div class="bline">

				<br class="clearfloat" />
				<div class="border_line">
					<table width="98%" border="0" cellspacing="0" cellpadding="0" >
					<form id="search_return_2" name="search_return_1" method="post" action="" onsubmit="return false;" >
					<tr>
						<td width="80"><span class="font14">&nbsp;商&nbsp;&nbsp;品：</span></td>
						<td width="140"><input name="kw" id="kw" type="text" class="inputsearch" onfocus="this.select();"  onKeyDown="javascript: if(window.event.keyCode == 13) select_return_product();"  /></td>
						<td width="240"><select id="cid" name="cid"  style="width:220px;"  onchange="javascript:window.location.href='return_product_add.php?cid='+this.options[this.selectedIndex].value+'';" >
	<?php 
		$n = 0;
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];

			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['ClientCompanyPinyi']).'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select></td>
						<td><input name="searchbutton2" value="查 询" type="button" class="bluebtn" onclick="select_return_product();" /></td>
						</tr>
					</form>
					</table>
					
				<font color=red>（根据订购商品退货，请输入商品，选择药店查询。）</font>
				</div>
			<form id="MainForm" name="MainForm" method="post" action="">
			<input name="cid" id="cid" type="hidden" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"  />
			<input name="selectid_return" id="selectid_return" type="hidden" value=""  />
				<br class="clearfloat" />
				<div class="border_line">

					<div class="line bgw">
						<div class="line22 font12">客户信息</div>
<div class="line22"><strong>经 销 商：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
<div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
<div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>					
					</div>
					<br class="clearfloat" />

					<div class="line bgw">
						<div class="line22 font12">商品清单</div>
						<div class="line22">
						
  <table width="98%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="8%" height="28">&nbsp;ID</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">颜色</td>
	<td width="12%">规格</td>
    <td width="8%" align="right">可退数</td>
	<td width="10%" align="right">退货数</td>
    <td width="14%" align="right">订购价</td>
	<td width="8%" align="right">移除</td>
  </tr>
   </thead>
   <tbody id="come_add_sel_pro">

   </tbody>
</table>
			</div>
		</div>
	</div>

				<br class="clear" />
				<div class="border_line">
					<div class="line font14">退货信息</div>
					<div class="line bgw">
					
  <table width="98%" border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td width="12%">&nbsp;<strong>货运方式:</strong></td>
	<td>	
	<span id="rblQuery"><span ><input id="ReturnSendType1" type="radio" name="ReturnSendType" value="送货" style="border:0;"  /><label for="rblQuery_0">送货 （直接到公司退货）</label></span><br />
	<span ><input id="ReturnSendType2" type="radio" name="ReturnSendType" value="发货" checked="checked" style="border:0;"  /><label for="rblQuery_1">发货 （通过快递，货运把商品寄公司库房）</label></span><br />
	</td>
  </tr>
  <tr>
    <td >&nbsp;<strong>货运说明:</strong></td>
	<td><textarea name="ReturnSendAbout" rows="3" id="ReturnSendAbout" style="width:80%; height:48px;"></textarea></td>
  </tr>
  <tr>
    <td >&nbsp;<strong>外观包装:</strong></td>
	<td>产品外观：<select name="ReturnProductW" id="ReturnProductW" style="width:100px;">
	<option value="">---请选择---</option>
	<option value="良好">良好</option>
	<option value="有划痕">有划痕</option>
	<option value="外观有破损">外观有破损</option> 
</select>&nbsp;<font color=red>*</font>
	&nbsp;&nbsp; 包装情况：<select name="ReturnProductB" id="ReturnProductB" style="width:100px;">
	<option value="">---请选择---</option>
	<option value="无包装">无包装</option>
	<option value="包装破损">包装破损</option>
	<option value="包装完整">包装完整</option> 
</select>&nbsp;<font color=red>*</font>
</td>
  </tr>
  <tr>
    <td >&nbsp;<strong>退货原因:</strong></td>
	<td><textarea name="ReturnAbout" rows="5"  id="ReturnAbout" style="width:80%;"></textarea>&nbsp;<font color=red>*</font></td>
  </tr>
</table>					
					</div>
				</div>

			<br class="clear" />
			<div class="border_line">					
				<div class="line22 bgw" align="left"><font color=red>注：订单，商品只能在已收货的状态下才能退货!</font>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" 提 交 " class="bluebtn" name="returnsubbutton" id="returnsubbutton" onclick="do_save_new_return_product();"  />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" 重 置 " class="greenbtn" name="resetbutton" id="resetbutton" onclick="window.location.href='return_product_add.php?cid=<? echo $in['cid'];?>'"  /></div>	
			</div>



	<br class="clearfloat" />
			</form>				
		</div>
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">请选择退货商品</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>