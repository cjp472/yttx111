<?php 
$menu_flag = "finance";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceID=".intval($in['ID'])." limit 0,1");

	if(!empty($cinfo['FinanceFlag']) || ($cinfo['FinanceUser']!=$_SESSION['uinfo']['username'] && $_SESSION['uinfo']['userflag']!="9"))
	{
		echo '
		<script language="javascript">
		alert("非法操作!");
		window.location.href="finance.php";
		</script>
		';
		exit();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/finance.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_FinanceToDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="FinanceID" id="FinanceID" value="<? echo $cinfo['FinanceID'];?>" />
		
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="finance.php">收款</a> &#8250;&#8250;  <a href="#">修改收款单</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_edit_finance();" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='finance.php'" />
			</div>            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
            <fieldset title="“*” 为必填项"class="fieldsetstyle">
			<legend>转账信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（转款人）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label><span class="red">
                    <? 
					$clientarr = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientID=".$cinfo['FinanceClient']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFLag=0 ");
					echo $clientarr['ClientCompanyName'].' ('.$clientarr['ClientTrueName'].')';
					?>
                  *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;</td>
                </tr>
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">相关订单 ：</div></td>
                  <td  bgcolor="#FFFFFF" >
                   <? 
				   if(!empty($cinfo['FinanceOrder']))
				   {
					   $foarr = explode(",",$cinfo['FinanceOrder']);
					   foreach($foarr as $fvar)
					   {
							echo '<li><a href="order_manager.php?SN='.$fvar.'" target="_blank" >'.$fvar.'</a></li>';
					   }
				   }else{
						echo '预付款';
				   }
				   ?>
   			      </td>
				   <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">转款日期 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinanceToDate" id="data_FinanceToDate" value="<? echo $cinfo['FinanceToDate'];?>"  />
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款账户：</div></td>
                  <td bgcolor="#FFFFFF"><select name="data_FinanceAccounts" id="data_FinanceAccounts"  >
                    <option value="0">⊙ 请选择收款账户</option>
                    <? 
					$accarr = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AccountsID ASC");
					foreach($accarr as $accvar)
					{
						if($cinfo['FinanceAccounts'] == $accvar['AccountsID']) $smsg = 'selected="selected"'; else $smsg = '';
						echo '<option value="'.$accvar['AccountsID'].'" '.$smsg.'>┠- '.$accvar['AccountsBank'].' ('.$accvar['AccountsNO'].') '.$accvar['AccountsName'].'</option>';
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">新增【<a href="accounts_add.php">账号</a>】</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">转款金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinanceTotal" id="data_FinanceTotal"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" value="<? echo $cinfo['FinanceTotal'];?>"  />&nbsp;元
                  </label>
                  <span class="red">* </span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_FinanceAbout" rows="4" id="data_FinanceAbout"><? echo $cinfo['FinanceAbout'];?></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF">可注明转款原因，转款人</td>
                </tr>
               </table>
		  </fieldset>

			<br style="clear:both;" />
          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_edit_finance();" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='finance.php'" />		  
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>