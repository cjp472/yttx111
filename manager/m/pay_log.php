<?php
$menu_flag = "system";
include_once ("header.php");
require_once("../alipay/alipay_config.php");
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
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">交费记录</a></div>
   	        </div>     
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>
                  <td width="15%" height="30" ><a href="sys_pay.php" >- 系统续费</a></td>
				  <td width="15%" ><a href="sys_pay.php?pt=num">- 增加用户</a></td>
				  <td width="15%" ><a href="sms_pay.php">- 短信充值</a></td>
				  <td  >&nbsp;</td>
				</tr>
			 </table>


			<FORM name="alipayment" onSubmit="return CheckForm();" action="../alipay/alipayto.php" method="post" target="_blank">
			<INPUT  name="paysn" id="paysn"  type="hidden" value="<? echo $paysn; ?>"/>
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">ID</td>
                  <td width="16%" class="bottomlinebold">交费内容</td>
				  <td width="12%" class="bottomlinebold">金额(元)</td>                  
                  <td width="10%" class="bottomlinebold" >方式</td>                 
                  <td width="16%" class="bottomlinebold" >帐号/交易号</td>				  
				  <td width="10%" class="bottomlinebold" >时间</td>
				  <td class="bottomlinebold" >备注</td>
				  <td width="5%" align="center" class="bottomlinebold" >状态</td>
                </tr>
     		 </thead>      		
      		 <tbody>
<?
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_pay where PayCompany = ".$_SESSION['uinfo']['ucompany']." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_pay where PayCompany = ".$_SESSION['uinfo']['ucompany']." Order by PayID Desc";
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
                  <td >【<? if($lsv['PayType']=="system") echo '系统交费'; else echo '短信充值'; ?>】<br /><? echo $lsv['PayOrder'];?></td>		  <td class="title_green_w">¥ <? echo $lsv['PayMoney'];?></td>                  
                  <td ><? if($lsv['PayType']=="transter") echo '转帐汇款'; else echo '在线支付'; ?></td>
				  <td ><? echo $lsv['PayBuyer'].'<br />'.$lsv['PayTradeNO'];?>&nbsp;</td>
				  <td ><? echo date("Y-m-d H:i",$lsv['PayDate']);?></td>
				  <td ><? echo $lsv['PayBody'];?>&nbsp;</td>				  
				  <td align="center"><? if($lsv['PayStatus']=="TRADE_FINISHED" || $lsv['PayStatus']=="TRADE_SUCCESS") echo '<span class="title_green_w" title="交易成功" >√</span>'; else echo '<span class="font12h" title="交易失败" >X</span>';?>&nbsp;</td>
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
       			     <td align="right"><? echo $page->ShowLink('pay_log.php');?></td>
     			 </tr>
			</table>

				<INPUT TYPE="hidden" name="referer" value ="" >
				</form>
        	</div>              
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>