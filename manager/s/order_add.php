<?php 
$menu_flag = "order";
$pope	   = "pope_form";
include_once ("header.php");

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
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    
    

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">            
			<div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a> &#8250;&#8250; <a href="#">新增订单</a></div>    
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        	<div id="line">
			<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			<br class="clearfloat" />

            <fieldset  class="fieldsetstyle">
		<legend>新增订单</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（订单客户）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label><span class="red">
                    <select name="data_OrderUserID" id="data_OrderUserID" class="select2" style="width:455px;" onChange="javascript: get_addlist(this.options[this.selectedIndex].value);"  >
                    <option value="0">⊙ 请选择客户（订单客户）</option>
                    <? 
					$orderintoarr = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");
					foreach($orderintoarr as $orderinfovar)
					{
						echo '<option value="'.$orderinfovar['ClientID'].'" title="'.$orderinfovar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$orderinfovar['ClientCompanyPinyi']).'" >'.substr($orderinfovar['ClientCompanyPinyi'],0,1).' - '.$orderinfovar['ClientCompanyName'].'</option>';
					}
					?>
                  </select>
                  *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;可输入名称首字母快速匹配</td>
                </tr> 
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">收货信息：</div></td>
                  <td width="45%"  bgcolor="#FFFFFF" >
				  
				  <div style="width:450px; height:250px; overflow:scroll;" id="showuseraddress">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
                        <td width="35%"><strong>&nbsp;收货人</strong></td>
                        <td width="25%"><strong>&nbsp;联系人</strong></td>
                        <td ><strong>&nbsp;送货地址</strong></td>
                      </tr>
                    </table>
                  <div>

				  </td>
				  <td>
				  <strong>收货人/公司：</strong>
				  <br /><input type="text" name="data_OrderReceiveCompany" id="data_OrderReceiveCompany" />
				  <br /><strong>联系人：</strong>
				  <br /><input type="text" name="data_OrderReceiveName" id="data_OrderReceiveName" />
				  <br /><strong>联系电话：</strong>
				  <br /><input type="text" name="data_OrderReceivePhone" id="data_OrderReceivePhone" />
				  <br /><strong>送货地址：</strong>
				  <br /><input type="text" name="data_OrderReceiveAdd" id="data_OrderReceiveAdd" />
				  
				  </td>

                </tr>

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">配送方式 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <select name="data_OrderSendType" id="data_OrderSendType"   >
                    <? 
					$orderintoarr = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_sendtype  ORDER BY TypeID ASC");
					foreach($orderintoarr as $orderinfovar)
					{
						echo '<option value="'.$orderinfovar['TypeID'].'" '.$smsg.'>┠- '.$orderinfovar['TypeName'].' ('.$orderinfovar['TypeAbout'].')</option>';
					}
					?>
                  </select>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款方式：</div></td>
                  <td bgcolor="#FFFFFF"><select name="data_OrderPayType" id="data_OrderPayType"  >
                    <? 
					$orderintoarr = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 ORDER BY TypeID ASC");
					foreach($orderintoarr as $orderinfovar)
					{
						echo '<option value="'.$orderinfovar['TypeID'].'">┠- '.$orderinfovar['TypeName'].' ('.$orderinfovar['TypeAbout'].') '.'</option>';
					}
					?>
                  </select>
                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_OrderRemark" rows="4" id="data_OrderRemark"></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#FFFFFF"></td>
                  <td bgcolor="#FFFFFF" align="right"><input type="button" name="newbutton" id="newbutton" value="下一步，订购商品" class="button_9" style="width:108px; height:24px; font-size:12px;" onclick="do_save_new_order()" /> <label>                    
                  </label></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
               </table>
		  </fieldset>

			</form>
			</div>
		</div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>