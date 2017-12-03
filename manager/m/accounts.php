<?php
$menu_flag = "system";
include_once ("header.php");
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

<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv"> </div>            
			<div class="location"><strong>当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="accounts.php">收款账户</a>  </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 

<div><input type="button" name="newbutton" id="newbutton" value="新增收款账号" class="button_2" onclick="javascript:window.location.href='accounts_add.php'" style="margin:5px;" />
</div>
<hr style="clear:both;" />

<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
<br style="clear:both;" />
</div>
<!-- tree -->  
       	  </div>

          <div id="sortright">
              <div style="margin-top:5px;">
                 <img src="img/explanations/images/asdFS1.jpg" />
              </div>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">ID</td>
                  <td width="24%" class="bottomlinebold">开户行</td>
				  <td width="22%" class="bottomlinebold">账号</td>
                  <td  class="bottomlinebold" >开户名称(收款人)</td>
                  <td width="8%" class="bottomlinebold" >账户类型</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['uinfo']['ucompany']." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];        
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by AccountsID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
	 $n=1;
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['AccountsID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n++;?></td>
                  <td ><a href="#"><? echo $lsv['AccountsBank'];?></a></td>
				  <td ><? echo $lsv['AccountsNO'];?></td>
                  <td ><? echo $lsv['AccountsName'];?></td>
                  <td ><? echo $lsv['AccountsType'];?></td>		  
                  <td align="center">
					<a href="accounts_edit.php?ID=<? echo $lsv['AccountsID'];?>" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_accounts('<? echo $lsv['AccountsID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('accounts.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>


        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>