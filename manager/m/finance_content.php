<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceID=".intval($in['ID'])." limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>    
<?php include_once ("top.php");?>    
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="FinanceID" id="FinanceID" value="<? echo $cinfo['FinanceID'];?>" />
		
		<div id="searchline">
        	<div class="leftdiv width300" style="margin-top:5px;">
        	 <div class="locationl"><strong>当前位置：</strong><a href="finance.php">收款</a> &#8250;&#8250;  <a href="#">收款单详细</a></div>
   	        </div>  
   	        <div class="rightdiv sublink" style="padding-right:20px;margin-top:9px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>          
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

            <fieldset class="fieldsetstyle">
			<legend>转账信息</legend>
              <table width="98%" border="0" cellpadding="8" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">状态：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <? if($cinfo['FinanceFlag']=="2") echo '已确认到账'; else echo '在途';?>&nbsp;
                  </label>
                 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（转款人）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? 
					$clientarr = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".$cinfo['FinanceClient']." and ClientFLag=0 ORDER BY ClientID ASC");
					echo $clientarr['ClientTrueName']." - ".$clientarr['ClientCompanyName'];
					?>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;</td>
                </tr>
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">相关订单 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <?
				   $sqlarr = '';
				   if(!empty($cinfo['FinanceOrder']))
				   {
					   $foarr = explode(",",$cinfo['FinanceOrder']);
					   foreach($foarr as $fvar)
					   {
							if(empty($sqlarr)) $sqlarr .= " OrderSN = '".$fvar."' "; else $sqlarr .= " or OrderSN = '".$fvar."' ";	
					   }
					    if(!empty($sqlarr))
						{
							$sqlarr = " and (".$sqlarr.") ";
					 		$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlarr." order by OrderID asc ";
							$olist  =  $db->get_results($sql_l);
						
							foreach($olist as $ov)
							{
								echo '<li><a href="order_manager.php?SN='.$ov['OrderSN'].'" >'.$ov['OrderSN'].'&nbsp;&nbsp;(¥ '.$ov['OrderTotal'].')</a></li>';
							}
						}
				   }else{
						echo '预付款';
				   }
				   ?>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">日期 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    </label>
				  <label>转账日期：<? echo $cinfo['FinanceToDate'];?></label><br />
				  <label>到账日期：<? if(!empty($cinfo['FinanceUpDate'])) echo date("Y-m-d",$cinfo['FinanceUpDate']);?></label><br />
				  <label>填写日期：<? echo date("Y-m-d H:i",$cinfo['FinanceDate']);?></label><br />
					</td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款账户：</div></td>
                  <td bgcolor="#FFFFFF">
                    <?
					$accarr = $db->get_row("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany=".$_SESSION['uinfo']['ucompany']." and AccountsID=".$cinfo['FinanceAccounts']." ORDER BY AccountsID ASC");
	
					echo '开户行：'.$accarr['AccountsBank'].'<br />账&nbsp;&nbsp;&nbsp;号：'.$accarr['AccountsNO'].'<br />开户名称： '.$accarr['AccountsName'].'';
					?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">转款金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    ¥ <? echo $cinfo['FinanceTotal'];?>&nbsp;元
                  </label>
                 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">付款凭证：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2"><div>
                   <? 
					if(!empty($cinfo['FinancePicture'])) echo '<img src="'.RESOURCE_URL.''.$cinfo['FinancePicture'].'" />';
				   ?>
                  </div>
                 </td>

                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2" ><label>
                    <? echo nl2br($cinfo['FinanceAbout']);?>
                  </label></td>
                </tr>
				<?				
				if($cinfo['FinanceFrom'] == 'alipay')
				{
					$sql_a = "select * from ".DATABASEU.DATATABLE."_order_alipay where PayCompany=".$_SESSION['uinfo']['ucompany']." and PaySN='".$cinfo['FinancePaysn']."' order by PayID desc limit 0,1";
					$alipaycontent = $db->get_row($sql_a);

					if($cinfo['FinanceFlag']!="2" || $alipaycontent['PayStatus'] =="TRADE_SUCCESS" || $alipaycontent['PayStatus'] =="TRADE_SUCCESS")
					{
				?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">支付宝支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
							<strong>支付宝交易号：</strong><? echo $alipaycontent['PayTradeNO']; ?><br />
							<strong>支付账号：</strong><? echo $alipaycontent['PayBuyer']; ?><br />
							<strong>交易状态：</strong><font color="red">
							<? 
								if($alipaycontent['PayStatus'] =="TRADE_SUCCESS" || $alipaycontent['PayStatus'] =="TRADE_SUCCESS")	echo '支付成功'; else echo '交易未完成';
							?>
							</font><br />
							<strong>支付时间：</strong><? echo date("Y-m-d H:i:s",$alipaycontent['PayDate']);?><br />
				  </td>
                </tr>
				<? 
          }        
        }elseif($cinfo['FinanceFrom'] == 'allinpay'){
          $sql_a = "select * from ".DATABASEU.DATATABLE."_order_netpay where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderNO='".$cinfo['FinancePaysn']."' order by PayID desc limit 0,1";
          $netpaycontent = $db->get_row($sql_a);
          if(!empty($netpaycontent)){
          ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">网银支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
              <strong>交易号：</strong><? echo $netpaycontent['OrderNO']; ?><br />
              <strong>交易状态：</strong><font color="red">
              <? 
                if($netpaycontent['PayResult'] =="1")  echo '支付成功'; else echo '交易未完成';
              ?>
              </font><br />
              <strong>支付时间：</strong><? echo $netpaycontent['PayDateTime'];?><br />
          </td>
                </tr>
        <?
          }
        }elseif($cinfo['FinanceFrom'] == 'yijifu'){
 			$sql_a = "select * from ".DATABASEU.DATATABLE."_order_netpay where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderNO='".$cinfo['FinancePaysn']."' order by PayID desc limit 0,1";
         	$netpaycontent = $db->get_row($sql_a);
         	
         	$NetGetWay = new NetGetWay();
			$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['uc']['CompanyID'], '', true);
			$netInfo = Functions::rebulidInfo($netInfo, 'SignNO');
			
          	if(!empty($netpaycontent)){
         ?>
			<tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">快捷支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
              <strong>收款账户：</strong><? echo $getway_account_type[$netInfo[$netpaycontent['MerchantAccount']]['AccountType']]; ?><br />
              <strong>交 易 号 ：</strong><? echo $netpaycontent['OrderNO']; ?><br />
              <strong>交易状态：</strong><font color="red">
              <? 
                if($netpaycontent['PayResult'] =="1")  echo '支付成功'; else echo '交易未完成';
              ?>
              </font><br />
              <strong>支付时间：</strong><? echo $netpaycontent['PayDateTime'];?><br />
          </td>
                </tr>
		<?php
          	}
		}
        ?>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">操作人：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2" ><label>
                    填写修改：<? echo $cinfo['FinanceUser'];?><br />
                    确认到账：<? echo $cinfo['FinanceAdmin'];?><br />
                  </label></td>

                </tr>

               </table>
		  </fieldset>

			<br style="clear:both;" />
            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>
