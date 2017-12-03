<?php 
$menu_flag = "sms";
$pope	   = "pope_view";
include_once ("header.php"); 
setcookie("backurl", $_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function() {
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});
</script>
<script src="js/sms.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="leftdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="sms_send.php">发件箱</a> </div>
   	        </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
		<fieldset class="fieldsetstyle">
			<legend>系统通知短信 </legend>
      <table width="98%" border="0" cellspacing="0" cellpadding="4" align="center">
       <form id="sForm" name="sForm" method="post" action="sms_post.php"> <tr>
          <td width="7%"><strong>搜索：</strong></td>
          <td width="23%" nowrap="nowrap" >
            时间范围: 
      <input type="text" name="begindate" id="begindate" size="12" onfocus="this.select();" value="<? echo $in['begindate'];?>" readonly="" /> 
      至 
      <input type="text" name="enddate" id="enddate" size="12" onfocus="this.select();" value="<? echo $in['enddate'];?>" readonly="" />
      &nbsp;&nbsp;</td>
          <td width="25%"><label>
            号码：
                <input type="text" name="kw" id="kw" size="16" value="" onfocus="this.select();" />
          </label></td>
          <td width="10%"><label>
            <input name="button" type="submit" class="bluebtn" id="button" value=" 搜 索 " />
          </label></td>
          <td ><label>
            <input name="button2" type="button" class="bluebtn" id="button2" value="全部列出" onclick="window.location.href='sms_post.php'"  />
          </label></td>
		  <td width="14%"><a href="sms_send.php">自定义短信</a></td>
          </tr></form>
      </table>

			<hr align="center" />
        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">&nbsp;行号</td>
				  <td width="15%" class="bottomlinebold">接收号码</td>
				  <td class="bottomlinebold">短信内容</td>
                  <td width="12%" class="bottomlinebold">发送时间</td>
				  <td width="18%" class="bottomlinebold">药店</td>
                </tr>
     		   </thead>			 
			 <tbody>
			 <?
			$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
			foreach($clientdata as $areavar)
			{
				$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			}

			$sqlmsg = '';
			if(!empty($in['begindate'])) $sqlmsg .= ' and PostDate > '.strtotime($in['begindate']." 00:00:00").' ';
			if(!empty($in['begindate'])) $sqlmsg .= ' and PostDate < '.strtotime($in['enddate']." 23:59:59").' ';
			if(!empty($in['kw']))  $sqlmsg .= " and PostPhone like binary '%%".$in['kw']."%%' ";

			$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_sms_post where PostCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
			$page = new ShowPage;
			$page->PageSize = 20;
			$page->Total = $InfoDataNum['allrow'];
			$page->LinkAry = array("kw"=>$in['kw'],"begindate"=>$in['begindate'],"enddate"=>$in['enddate']);        
			
			$datasql   = "SELECT PostID,PostCompany,PostDate,PostPhone,PostUser,PostClient,PostContent,PostFlag FROM ".DATATABLE."_order_sms_post where PostCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  ORDER BY PostID DESC";
			$list_data = $db->get_results($datasql." ".$page->OffSet());
				
			if(!empty($list_data))	
			{
				$n= 1;
			    if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $var)
				{
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td >&nbsp;<? echo $n++;?></td>
				  <td><? echo $var['PostPhone'];?></td>
				  <td ><? echo 
			 str_replace("【".$_SESSION['uc']['CompanySigned']."】","",str_replace("[".$_SESSION['uc']['CompanySigned']."]","",$var['PostContent']));
			 ?></td>
                  <td ><? echo date("Y-m-d H:i",$var['PostDate']);?></td>
                  <td ><? echo $clientarr[$var['PostClient']];?>&nbsp;</td>
			 </tr>
			 <? 
				 }
			}
			 ?>
			 <tr >
				  <td colspan="10" align="right">&nbsp; <? echo $page->ShowLink('sms_post.php');?></td>
			 </tr>
			 </tbody>
			</table>

	      </fieldset >
         </div>              
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">查看短信</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"> <div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>