<?php 

$menu_flag = "sms";
$pope	       = "pope_form";
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

<script src="js/sms.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>



<body>

<?php include_once ("top.php");?>
    <div id="bodycontent">

    	<div class="lineblank"></div>

        

		<div id="searchline">

        	<div class="leftdiv">

        	 <div class="location"><a name="editname"></a><strong>&nbsp;&nbsp; 当前位置：</strong><a href="sms.php">短信</a>  &#8250;&#8250; <a href="sms_phonebook.php">通讯录</a> </div>

   	        </div>

        </div>

    	

        <div class="line2"></div>

        <div class="bline" >



       	<div id="sortleft">         

<!-- tree --> 

<div class="leftlist"> 

<div ><br />

<strong><img src="css/images/home.gif" alt="分组"  />&nbsp;&nbsp;联系人分组</strong></div>

<ul>

	<?

	$sinfo = $db->get_results("SELECT SortID,SortName,SortOrder FROM ".DATATABLE."_order_phonebook_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");

	for($i=0;$i<count($sinfo);$i++)

	{

		echo '<li>'.($i+1).'、<a href="#editname" onclick="set_edit_sort(\''.$sinfo[$i]['SortID'].'\',\''.$sinfo[$i]['SortName'].'\',\''.$sinfo[$i]['SortOrder'].'\');">'.$sinfo[$i]['SortName'].'</a></li>';

	}

	?>
</ul>

<br style="clear:both;" />

</div>

<!-- tree -->   

       	  </div>



<div id="sortright">

 <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target=""  action="">

			<fieldset title="“*”为必填项！" class="fieldsetstyle">

		<legend>新增分组</legend>

       

              <table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">


                <tr>

                  <td width="16%" bgcolor="#F0F0F0"><div align="right">分组名称：</div></td>

                  <td width="55%" bgcolor="#FFFFFF"><label>

                    <input type="text" name="data_SortName" id="data_SortName" />

                    <span class="red">*</span></label></td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>

                  <td bgcolor="#FFFFFF"><input name="data_SortOrder" type="text" id="data_SortOrder" value="0"  maxlength="10" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  /></td>

                </tr>

            </table>

		</fieldset>

            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_sort();" />
			</div>

            

		<fieldset  class="fieldsetstyle">

			<legend>修改分组（先点击左边相应的分组）</legend>

			<table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                <tr>

                  <td width="16%" bgcolor="#F0F0F0"><div align="right">分组名称：</div></td>

                  <td width="55%" bgcolor="#FFFFFF"><label>

                     <input type="hidden" name="edit_SortID" id="edit_SortID" value=""/>

					<input type="text" name="edit_SortName" id="edit_SortName" />

                    <span class="red">*</span></label></td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>

                  <td bgcolor="#FFFFFF"><input name="edit_SortOrder" type="text" id="edit_SortOrder" value="0"  maxlength="10" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  /></td>

                </tr>

            </table>

         </fieldset>

            

            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_sort();" />
			<input name="backid" type="button" class="button_3" id="backid" value="删 除" onclick="do_delete_sort();" />
			</div>

              <INPUT TYPE="hidden" name="referer" value ="" >

      </form>

        </div>

              

          </div>    

        <br style="clear:both;" />              

        <br style="clear:both;" />

    </div>

    



<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 

</body>

</html>