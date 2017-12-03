<?php
$menu_flag = "manager";
include_once ("header.php");
if($_SESSION['uinfo']['userid'] != "1" && $_SESSION['uinfo']['userid'] != "3") exit('非法路径!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link href="css/showpage.css" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="leftdiv" style=" padding:4px 0 0 0;">
        	 <div class=""><input name="addfinancelog" type="button" class="bluebtn" id="addfinancelog" value="添加收款记录" onclick="javascript:window.location.href='finance_log_add.php';" /></div>
   	        </div>  
			<div class="rightdiv" style=" padding:4px 0 0 0;">短信通道余额:&nbsp;&nbsp;<a href="#" onclick="show_sms_number1();">[1]</a>&nbsp;&nbsp;<a href="#" onclick="show_sms_number();">[2]</a>&nbsp;&nbsp;</div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

			<FORM name="alipayment" onSubmit="return CheckForm();" action="../alipay/alipayto.php" method="post" target="_blank">
			<INPUT  name="paysn" id="paysn"  type="hidden" value="<? echo $paysn; ?>"/>

             <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold">ID</td>
				  <td width="20%" class="bottomlinebold" >客户</td> 
                  <td width="16%" class="bottomlinebold">交费内容</td>
				  <td width="12%" class="bottomlinebold">金额(元)</td>                                
                  <td width="15%" class="bottomlinebold" >交易号/帐号</td>
				  <td width="10%" class="bottomlinebold" >时间</td>
					<td  class="bottomlinebold" >备注</td>
				  <td width="4%" align="center" class="bottomlinebold" >状态</td>
				  <td width="6%" align="center" class="bottomlinebold" >管理</td>
                </tr>
     		 </thead>      		
      		<tbody>

<?php
	$sortarr = $db->get_results("SELECT CompanyID,CompanyName,CompanySigned FROM ".DATABASEU.DATATABLE."_order_company where CompanyFlag='0' ORDER BY CompanyID DESC");
	foreach($sortarr as $areavar)
	{
		$companyarr[$areavar['CompanyID']] =  $areavar['CompanyName'];
	}	
	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_pay ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_pay  Order by PayID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
		$n=0;
		foreach($list_data as $lsv)
		{
			$n++;
?>
                <tr id="line_<? echo $lsv['PayID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n;?></td>
				  <td ><a href="manager_company.php?ID=<? echo $lsv['PayCompany'];?>" target="_blank"><? echo $companyarr[$lsv['PayCompany']];?></a></td>
                  <td >【<? if($lsv['PayType']=="system") echo '系统交费'; else echo '短信充值'; ?>】<br /><? echo $lsv['PayOrder'];?></td>
				  <td class="title_green_w">¥ <? echo $lsv['PayMoney'];?></td>                
				  <td ><? echo $lsv['PayTradeNO'].'<br />'.$lsv['PayBuyer'];?></td>
				  <td ><? echo date("Y-m-d H:i",$lsv['PayDate']);?></td>
				  <td ><? echo $lsv['PayBody'];?>&nbsp;</td>
				  <td align="center"><? if($lsv['PayStatus']=="TRADE_FINISHED" || $lsv['PayStatus']=="TRADE_SUCCESS") echo '<span class="title_green_w" title="交易成功" >√</span>'; else echo '<span class="font12h" title="交易失败" >X</span><br />[<a href="javascript:void(0);" onclick="del_finance_field('.$lsv['PayID'].')">删除</a>]';?></td>   
				  <td align="center">
				  <? if(empty($lsv['PayFlag'])){?>
				  <a href="javascript:void(0);" onclick="confirm_finance('<? echo $lsv['PayID'];?>');">确认到帐</a>
				  <? }else{?>
				  <font color="gray">确认到帐</font>
				  <? }?>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('finance_log.php');?></td>
     			 </tr>
			  </table>

				<INPUT TYPE="hidden" name="referer" value ="" >
				</form>
        	</div>              
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">到帐确认</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">

        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>