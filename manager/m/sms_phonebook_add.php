<?php 
$menu_flag = "sms";
$pope	       = "pope_form";
include_once ("header.php");

if(empty($in['linenumber'])) $in['linenumber'] = 5;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/sms.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="sms_phonebook.php">
        		<tr>
					<td width="100" align="center"><strong>姓名/电话：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="#">通讯录</a> </td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">

		 <div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增联系人 " class="button_2" onclick="javascript:window.location.href='sms_phonebook_add.php" /></div>
         <hr style="clear:both;" />

<div class="leftlist"> 
<div ><strong><a href="sms_phonebook_client.php">- 药店通讯录</a></strong></div>

<hr style="clear:both;" />
<div ><strong><a href="sms_phonebook.php">联系人分组</a></strong></div>
	<ul>
			<?
			$sinfo = $db->get_results("SELECT SortID,SortName,SortOrder FROM ".DATATABLE."_order_phonebook_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
			for($i=0;$i<count($sinfo);$i++)
			{
				echo '<li>'.($i+1).'、<a href="sms_phonebook.php?sid='.$sinfo[$i]['SortID'].'" >'.$sinfo[$i]['SortName'].'</a></li>';
			}
			?>
	</ul>
</div>
<div style="clear:both;" >&nbsp;</div>
<hr style="clear:both;" />
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 分组管理 " class="button_2" onclick="javascript:window.location.href='sms_phonebook_sort.php'" /></div>

       	  </div>
          <div id="sortright">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td   height="38" width="150" ><strong>&nbsp;&nbsp;批量增加数据行数：</strong></td>
					 <td><input name="linenumber" type="text" id="linenumber" value="<? echo $in['linenumber'];?>"  maxlength="10" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" onKeyDown="javascript: if(window.event.keyCode == 13) add_line();"  />&nbsp;&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 执 行 " class="bluebtn" onclick="add_line();" /></td>
     			 </tr>
              </table>
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>新增联系人</legend>       
              <table width="98%" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc" class="inputstyle" align="center">
                <tr>
                  <td width="14%" bgcolor="#F0F0F0" ><div align="right">联系人分组：</div></td>
                  <td  bgcolor="#FFFFFF" colspan="6"><label>
					<select name="data_PhoneSort" id="data_PhoneSort"  style="width:200px;">
                    <option value="0">⊙ 请选择联系人分组</option>
					<?
					for($i=0;$i<count($sinfo);$i++)
					{
						echo '<option value="'.$sinfo[$i]['SortID'].'">'.$sinfo[$i]['SortName'].'</option>';
					}
					?>
					</select>
                    <span class="red">*</span></label></td>
                </tr>
				<? 
				for($i=0;$i<$in['linenumber'];$i++)
				{
				?>
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right"><span class="red">*</span>&nbsp;姓名：</div></td>
				  <td bgcolor="#FFFFFF" width="18%"><input name="data_PhoneName[]" type="text" id="data_PhoneName_<? echo $i;?>" value=""    /></td>
				  <td bgcolor="#F0F0F0" width="10%"><div align="right"><span class="red">*</span>&nbsp;手机：</div></td>
                  <td bgcolor="#FFFFFF" width="18%"><input name="data_PhoneNumber[]" type="text" id="data_PhoneNumber_<? echo $i;?>" value=""  maxlength="11" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  /></td>
				  <td bgcolor="#F0F0F0" width="14%"><div align="right">单位/公司：</div></td>
				  <td bgcolor="#FFFFFF"><input name="data_PhoneBranch[]" type="text" id="data_PhoneBranch_<? echo $i;?>" value=""    /></td>
                </tr>
				<? }?>
				<tr>
                  <td bgcolor="#F0F0F0">&nbsp;</td>
				  <td  bgcolor="#FFFFFF" colspan="6" height="32" align="left"><input type="button" name="newbutton" id="newbutton" value=" 保 存 " class="button_1" onclick="add_phonebook_save();" style="width:72px; height:24px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="cancelbutton" id="cancelbutton" value=" 重 置 " class="button_3" style="width:72px; height:24px;" /></td>
				</tr>
				</table>
			</fieldset>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    
</body>
</html>