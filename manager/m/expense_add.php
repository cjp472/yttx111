<?php 
$menu_flag = "finance";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");
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
		$("#data_ExpenseDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
	<div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >

	     <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="expense.php">其他款项</a> &#8250;&#8250;  <a href="expense_add.php">新增其他款项</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_expense();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='expense.php'" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
            <fieldset title="“*” 为必填项"class="fieldsetstyle">
			<legend>其他款项信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <select name="data_ClientID" id="data_ClientID" class="select2" style="width:574px;"  >
                    <option value="0">⊙ 请选择药店</option>
                    <? 
					$orderintoarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFLag=0 ORDER BY ClientCompanyPinyi ASC");
					foreach($orderintoarr as $orderinfovar)
					{
						$smsg = '';
						echo '<option value="'.$orderinfovar['ClientID'].'" title="'.$orderinfovar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$orderinfovar['ClientCompanyPinyi']).'" '.$smsg.' >'.substr($orderinfovar['ClientCompanyPinyi'],0,1).' - '.$orderinfovar['ClientCompanyName'].'</option>';
					}
					?>
                  </select><span class="red"> *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;可输入名称首字母快速匹配</td>
                </tr> 

                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">其他款项类型：</div></td>
                  <td width="55%" bgcolor="#FFFFFF" >
					<select name="data_BillID" id="data_BillID" style="width:90%;">
                    <option value="0">⊙ 请选择其他款项类型</option>
					<?php
						$billdata = $db->get_results("select BillID,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." ");
						foreach($billdata as $var)
						{
							echo '<option value="'.$var['BillID'].'">┠-'.$var['BillName'].'</option>';
						}
					?>
					</select> <span class="red">*</span>
				  </td>
                  <td width="29%" bgcolor="#FFFFFF" >新增【<a href="expense_bill.php">类型</a>】</td>
                </tr>
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">日期 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ExpenseDate" id="data_ExpenseDate" value="" />
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ExpenseTotal" id="data_ExpenseTotal"  maxlength="12" />&nbsp;
                  </label>
                  <span class="red">* </span>元
				  &nbsp;
				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;可以填负数</td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_ExpenseRemark" rows="4" id="data_ExpenseRemark"></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
               </table>
		  </fieldset>

			<br style="clear:both;" />
          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_expense();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='expense.php'" />
		 </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>