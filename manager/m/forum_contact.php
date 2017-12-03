<?php 
$menu_flag = "forum";
$pope	   = "pope_view";
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
<script src="js/forum.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="forum.php">客服</a> &#8250;&#8250; <a href="forum_contact.php">联系方式</a></div>
   	        </div>       
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div ><br />
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="javascript:;" title="联系方式">新增联系方式：</a></strong></div>
<ul>
	 <form id="MainForm2" name="MainForm2" method="post" action="" target="exe_iframe" >

	<li><strong>名称：</strong></li>
	<li><input name="ContactName" id="ContactName" type="text"onfocus="this.select();" style="width:160px;" /></li>
	<li><strong>内容：</strong></li>
	<li><input name="ContactValue" id="ContactValue" type="text" onfocus="this.select();" style="width:160px;" /></li>
	<li><input name="contactbuttom" id="contactbuttom" value="添 加" type="button" class="button_1" onclick="SubmitContact()" /></li>
	</form>
</ul>
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
		
		<div class="warning"> &nbsp;&nbsp; 客户端最多只能显示 5 个联系方式</div>
		
		<div class="line" id="editcontact" style="display:none;">
			<form id="MainForm3" name="MainForm3" enctype="multipart/form-data" method="post" target=""  action="">
			<input type="hidden" name="edit_ContactID" id="edit_ContactID" value=""/>
			<fieldset  class="fieldsetstyle">
				<legend>修改信息</legend>
				<table width="88%" border="0" cellspacing="4" cellpadding="0">   
					<tr>
						<td><strong>名称：</strong></td>
						<td><input name="edit_ContactName" id="edit_ContactName" type="text"onfocus="this.select();" /></td>
						<td><strong>内容：</strong></td>
						<td><input name="edit_ContactValue" id="edit_ContactValue" type="text" onfocus="this.select();" /></td>
						<td><input name="editbuttom" id="editbuttom" value="保存" type="button" class="bluebtn" onclick="SubmitEditContact()" /></td>
						<td><input name="editbuttomcacel" id="editbuttomcacel" value="取消" type="button" class="bluebtn" onclick="CancelEditContact()" /></td>
					</tr>
				</table>
			</fieldset>
		</div>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td  width="24%" class="bottomlinebold">名字</td>
                  <td  class="bottomlinebold" >内容</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$datasql   = "SELECT * FROM ".DATATABLE."_order_contact where ContactCompany = ".$_SESSION['uinfo']['ucompany']." ORDER BY ContactID DESC";
	$list_data = $db->get_results($datasql);

	if(!empty($list_data))
	{
		$n=1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ContactID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ContactID'];?>" value="<? echo $lsv['ContactID'];?>" /></td>
                   <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['ContactName'];?>&nbsp;</td>
                  <td ><? echo $lsv['ContactValue'];?></td>                  
                  <td align="center"><a href="javascript:void(0);" onclick="do_set_edit_contact(<? echo "'".$lsv['ContactID']."','".$lsv['ContactName']."','".$lsv['ContactValue']."'";?>);" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_contact('<? echo $lsv['ContactID'];?>');" ><span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的信息!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
<!-- rightbody end -->
        </div>              
          </div>
              
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>