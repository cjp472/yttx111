<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

if(empty($in['cid'])) $in['cid'] = '';
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
<script src="js/order.js?v=4<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker();
		$("#edate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="order_guestbook.php">
        		<tr>
					<td width="80" align="center"><strong>订单商品：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
					<td width="180" align="center">
				<select id="cid" name="cid" style="width:160px;" class="select2">
				<option value="" >⊙ 所有药店</option>
				<?php 
					$n = 0;
					foreach($clientdata as $areavar)
					{
						$n++;
						if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
						$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
						echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'"  >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
					}
				?>
				</select>
				</td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="order_guestbook.php">订单留言</a>  </div> </td>
					</tr>
   	          </form>
			 </table>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
		<fieldset  class="fieldsetstyle">
			<legend>订单留言数据</legend>
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

<table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr class="bottomlinebold">

    <td width="4%" >行号</td>
	<td width="20%" >药店</td>
	<td width="12%">帐号</td>
    <td width="10%">&nbsp;联系人</td>
    <td width="10%">时间</td>
    <td >留言内容</td>
    <td width="12%" align="right">关联订单</td>    
  </tr>
   </thead>
   <tbody>
	<?php 
			$sqlmsg = $sqlmsg1 = '';
			
			if(!empty($in['cid']))   $sqlmsg  .= " and o.OrderUserID = ".$in['cid']." ";
			if(!empty($in['bdate'])) $sqlmsg  .= ' and s.Date > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sqlmsg  .= ' and s.Date < '.strtotime($in['edate'].'23:59:59').' ';
			
			if(!empty($in['kw']))
			{				
				$sqlmsg .= " AND (s.Content LIKE '%".$in['kw']."%' OR o.OrderSN LIKE '%".$in['kw']."%') ";
			}
			$rowsql = "select count(*) as allrow
			FROM ".DATATABLE."_order_ordersubmit s 
			inner join ".DATATABLE."_order_orderinfo o on s.OrderID=o.OrderID where s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.Status='客户留言' and o.OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg;

			$InfoDataNum = $db->get_row($rowsql);			
			$datasql = "select s.*,o.OrderID,o.OrderSN,o.OrderUserID FROM ".DATATABLE."_order_ordersubmit s 
			inner join ".DATATABLE."_order_orderinfo o on s.OrderID=o.OrderID  where s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.Status='客户留言' and o.OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY s.ID DESC ";
			
			$page = new ShowPage;
			$page->PageSize = 50;
			$page->Total    = $InfoDataNum['allrow'];
			$page->LinkAry  = array("kw"=>$in['kw'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
	
			$list_data = $db->get_results($datasql." ".$page->OffSet());
			$n = 1;
			$totalnumber = $totalnumbers = $totalmoney = 0;
			if(!empty($list_data))
			{			
				if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $ckey=>$cvar)
				{

	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >

	<td height="30">&nbsp;<? echo $n++;?></td>
	<td style="padding-right: 5px;"><a href="client_content.php?ID=<? echo $cvar['OrderUserID'];?>" target="_blank"><? echo $clientarr[$cvar['OrderUserID']];?></a></td>
	<td ><? echo $cvar['AdminUser'];?></td>
    <td ><? echo $cvar['Name'];?></td>
    <td><? echo date("Y-m-d H:i",$cvar['Date']);?>  </td>
    <td><? echo $cvar['Content'];?> </td>
    <td  align="right"><a href="order_manager.php?ID=<? echo $cvar['OrderID'];?>" target="_blank"><? echo $cvar['OrderSN'];?></a>&nbsp;</td>
  </tr>
   <?php }} ?>

   </tbody>
</table>

              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('order_guestbook.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
			  </fieldset>
       	  </div>
        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>