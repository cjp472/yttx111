<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." and ExpenseID=".intval($in['ID'])." limit 0,1");
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
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="FinanceID" id="FinanceID" value="<? echo $cinfo['FinanceID'];?>" />
		
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="expense.php">费用</a> &#8250;&#8250;  <a href="#">费用详细</a></div>
   	        </div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

            <fieldset class="fieldsetstyle">
			<legend>其他款项信息</legend>
              <table width="98%" border="0" cellpadding="8" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">状态：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <? if($cinfo['FlagID']=="2") echo '已审核'; else echo '';?>&nbsp;
                  </label>
                 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? 
					$clientarr = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".$cinfo['ClientID']." and ClientFLag=0 ORDER BY ClientID ASC");
					echo $clientarr['ClientTrueName']." - ".$clientarr['ClientCompanyName'];
					?>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;</td>
                </tr>
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">日期 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    </label>
				  <label>日期：<? echo $cinfo['ExpenseDate'];?></label><br />
				  <label>审核：<? if($cinfo['FlagID'] == "2") echo date("Y-m-d",$cinfo['ExpenseTime']);?></label><br />

					</td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">类型：</div></td>
                  <td bgcolor="#FFFFFF">
                    <?
					$accarr = $db->get_row("SELECT BillNO,BillName FROM ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." and BillID=".$cinfo['BillID']." ");
	
					echo $accarr['BillNO'].' - '.$accarr['BillName'];
					?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    ¥ <? echo $cinfo['ExpenseTotal'];?>&nbsp;元
                  </label>
                 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2" ><label>
                    <? echo nl2br($cinfo['ExpenseRemark']);?>
                  </label></td>
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">操作人：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2" ><label>
                    填写修改：<? echo $cinfo['ExpenseUser'];?>
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
