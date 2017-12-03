<?php 
$menu_flag = "notice_list";
include_once ("header.php");
if(!empty($in['ID'])){
	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_pay_notice where ID=".intval($in['ID'])." limit 0,1");
	$sql="select view_type from ".DATATABLE."_pay_notice_type where id=".$InfoData['type'];
	$view_type =$db->get_var($sql);
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
<script language="javascript" type="text/javascript" src="../plugin/My97DatePicker/WdatePicker.js"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
     
<?php if($view_type==1){ ?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="id" id="noticeid" value="<?php echo $InfoData['id'];?>">
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">平台通知</a> &#8250;&#8250; <a href="#">编辑支付提示信息</a></div>
   	        </div>
           
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>编辑支付提示信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                                
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">标题：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="title" id="data_title" value="<? if(!empty($InfoData['title'])) echo $InfoData['title']; ?>" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr cellpadding="0" cellspacing="0">
                  <td bgcolor="#F0F0F0" valign="top"><div align="right">提示内容：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="content" rows="5"  id="data_notice"><? if(!empty($InfoData['content'])) echo $InfoData['content']; ?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">有效日期：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="bdate" id="bdate"  onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" class="inputline" style="width:150px;" value="<? if(!empty($InfoData['start_date'])) echo date("Y-m-d H:i:s",$InfoData['start_date']); ?>" /> -<input type="text" name="edate" id="edate" class="inputline" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" style="width:150px;" value="<? if(!empty($InfoData['end_date'])) echo date("Y-m-d H:i:s",$InfoData['end_date']); ?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_notice();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
	<!----------------------------------------------------->
<?php }else if($view_type == 2){?>	   
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="id" id="noticeid" value="<?php echo $InfoData['id'];?>">
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">平台通知</a> &#8250;&#8250; <a href="edit_platform_notice.php">编辑公告</a></div>
   	        </div>
           
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>编辑平台公告</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                                
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">标题：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="title" id="data_title" value="<? if(!empty($InfoData['title'])) echo $InfoData['title']; ?>" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr cellpadding="0" cellspacing="0">
                  <td bgcolor="#F0F0F0" valign="top"><div align="right">提示内容：</div></td>
                  <td bgcolor="#FFFFFF"><script src="../ckeditor/ckeditor.js?v=3"></script>
				  <script>$(function () {
						CKEDITOR.replace('content', { height: '300px', width: '927px' });
					});
				  </script>
					<textarea class="ckeditor" style="width:300px;height:200px;" cols="60" id="content" name="content" rows="8"> <? if(!empty($InfoData['content'])) echo $InfoData['content']; ?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">有效日期：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="bdate" id="bdate"  onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" class="inputline" style="width:150px;" value="<? if(!empty($InfoData['start_date'])) echo date("Y-m-d H:i:s",$InfoData['start_date']); ?>" /> -<input type="text" name="edate" id="edate" class="inputline" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" style="width:150px;" value="<? if(!empty($InfoData['end_date'])) echo date("Y-m-d H:i:s",$InfoData['end_date']); ?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">重要/紧急：</div></td>
                  <td bgcolor="#FFFFFF" id="important"><input type="radio" name="important" style="width:20px;" <?php if($InfoData['important']==0){ echo 'checked="ture"';  }?>  value="0" />不重要 &nbsp;&nbsp;&nbsp;<input type="radio" name="important" style="width:20px;" <?php if($InfoData['important']==1){ echo 'checked="ture"';  }?>  value="1" />重要 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_plat_notice();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
	<!----------------------------------------------------->
<?php }else if($view_type ==3){?>	  	
	 <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="id" id="noticeid" value="<?php echo $InfoData['id'];?>">
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">平台通知</a> &#8250;&#8250; <a href="#">常见问题解答</a></div>
   	        </div>
            
          
            
        </div>

    	
        <div class="line2"></div>
		
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>常见问题解答</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle"> 
				
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">问题：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="title" id="data_title" value="<? if(!empty($InfoData['title'])) echo $InfoData['title']; ?>" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" valign="top"><div align="right">答案：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="content" rows="5" placeholder="请输入答案。如果是页面请直接输入完整链接地址，并选择下方按钮。" id="data_notice"><? if(!empty($InfoData['content'])) echo $InfoData['content']; ?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				
				<tr>
                  <td bgcolor="#F0F0F0"><div align="right">是否为链接：</div></td>
                  <td bgcolor="#FFFFFF" id="important"><input  <?php if($InfoData['important']==0){ echo 'checked="ture"';  }?> type="radio" name="important" style="width:20px;" checked="ture"  value="0" />否 &nbsp;&nbsp;&nbsp;<input  <?php if($InfoData['important']==1){ echo 'checked="ture"';  }?> type="radio" name="important" style="width:20px;"   value="1" />是 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				

              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_Q_A();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
	<!----------------------------------------------------->
 <?php } ?>	   
<?php include_once ("bottom.php");?>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
