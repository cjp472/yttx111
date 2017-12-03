<?php 
$menu_flag = "consignment";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_kuaidi.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="consignment.php">发货</a> &#8250;&#8250; <a href="logistics.php">物流公司</a> &#8250;&#8250; <a href="logistics_add.php">新增公司</a></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px;">			
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_logistics();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='logistics.php'" />
			</div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

            <fieldset title="“*” 为必填项"class="fieldsetstyle">
		<legend>货运公司信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">常用物流公司：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <select name="data_LogisticsPinyi" id="data_LogisticsPinyi" class="select2" style="width:574px;" onchange="setCompanyCode(this.options[this.selectedIndex].text);">
                    <option value="">⊙ 请选择常用物流公司</option>
                    <?
					foreach($arr_kuaidi as $key=>$var)
					{
						echo '<option value="'.$key.'"> '.$key.' - '.$var.'</option>';
					}
					?>
                  </select>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;可使用字拼音码快速查找，选择此列表中的物流公司可查询物流状态</td>
                </tr>   
				 
				 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                     <input type="text" name="data_LogisticsName" id="data_LogisticsName"  />
                 <span class="red"> *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_LogisticsContact" id="data_LogisticsContact" value=""  />
                    </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_LogisticsPhone" id="data_LogisticsPhone"  />
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_LogisticsFax" id="data_LogisticsFax" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_LogisticsMobile" id="data_LogisticsMobile"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地 址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_LogisticsAddress" id="data_LogisticsAddress"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">网 站：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_LogisticsUrl" id="data_LogisticsUrl"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">经营线路：<br />(简介)&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_LogisticsAbout" rows="4" id="data_LogisticsAbout"></textarea></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>



              </table>
		  </fieldset>

			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_logistics();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='logistics.php'" />
			</div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
