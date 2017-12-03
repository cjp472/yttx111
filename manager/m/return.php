<?php
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

	$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");


 	$sqlmsg = '';
	$locationmsg = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and ReturnClient = ".intval($in['cid'])." ";
		$cidmsg  = '&cid='.$in['cid'];
	}

	if(!isset($in['sid']) || $in['sid']=='')
	{
		//$in['sid']='';
		$sidmsg = '';
	}else{
		$sqlmsg .=" and ReturnStatus = ".intval($in['sid'])." ";
		$sidmsg  = '&sid='.$in['sid'];
		$locationmsg .= ' &#8250;&#8250; '.$order_status_arr[$in['sid']];
	}
	if(!empty($in['kw'])) $locationmsg = ' &#8250;&#8250; 搜索：“'.$in['kw'].'” 的退货单';

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
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="return.php">
        		<tr>
					<td width="80" align="center"><strong>退货单搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
        	        <td width="80"><select name="stype" id="stype" class="inputline">
					<? if($in['stype']=="productname"){?>
						<option value="ordersn">退单号</option>
						<option value="productname"  selected="selected"> 商品名称 </option>
					<? }else{?>
						<option value="ordersn" selected="selected">退单号</option>
						<option value="productname"> 商品名称 </option>
					<? }?>
					</select>
					</td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="return.php">退单管理</a>  <? echo $locationmsg;?></div></td>
				</tr>
   	          </form>
			 </table>        
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 退货申请 " class="button_2" onclick="javascript:window.location.href='return_add.php'" /> </div> 
<hr style="clear:both;" />
<div ><strong><a href="return.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="return.php" method="get">
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['ClientCompanyPinyi']).'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select>
				</form>
</ul>
<br /><br />
<?php //include_once ("inc/search_client.php");?>

<hr style="clear:both;" />
<div ><strong><a href="return.php">所有退货单</a></strong></div>
<ul>
	<?php 
		foreach($return_status_arr as $skey=>$svar)
		{
			if(isset($in['sid']) && $in['sid']!='')
			{
				if($in['sid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="return.php?sid='.$skey.''.$cidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>
<hr style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>
          
		  <div id="sortright">
            <form id="MainForm" name="MainForm" method="post" action="return_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="13%" class="bottomlinebold">&nbsp;退货单</td>
				  <td width="32%" class="bottomlinebold">客户/订单</td>
                  <td width="20%" class="bottomlinebold">货运/外观/包装</td>
                  <td class="bottomlinebold" >原因/说明</td>
                  <td width="12%" class="bottomlinebold" >&nbsp;管理</td>
                </tr>
     		   </thead>
      		<tbody>
<?
if(empty($in['stype']))
{
	$sqlnum = "SELECT count(*) AS allrow FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ";
	$InfoDataNum = $db->get_row($sqlnum);
	$page        = new ShowPage;
    $page->PageSize = 12;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = array("sid"=>$in['sid'],"cid"=>$in['cid'],"pid"=>$in['pid'],"fid"=>$in['fid']);        
	
	$datasql   = "SELECT ReturnID,ReturnSN,ReturnOrder,ReturnClient,ReturnSendType,ReturnSendStatus,ReturnAbout,ReturnProductW,ReturnProductB,ReturnTotal,ReturnStatus,ReturnDate,ReturnType FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by ReturnID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

}else{
	$sdmsg  = '';
	$sdmsg1 = '';
	if($in['stype']=="productname")
	{
		if(!empty($in['kw'])){	  $sdmsg .= " and c.ContentName like binary '%%".trim($in['kw'])."%%' ";}
		if(!empty($in['bdate'])){ $sdmsg .= ' and o.ReturnDate > '.strtotime($in['bdate'].'00:00:00').''; }
		if(!empty($in['edate'])){ $sdmsg .= ' and o.ReturnDate < '.strtotime($in['edate'].'23:59:59').'';}

		$sqlnum = "select count(*) as allrow from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.ContentName like binary '%%".trim($in['kw'])."%%' and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sdmsg." group by o.ReturnID ";

		$datasql = "select o.* from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.ContentName like '%".trim($in['kw'])."%' and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sdmsg." group by o.ReturnID Order by o.ReturnID Desc";

	}else{
		if(!empty($$in['kw']))   $sdmsg .= " and ReturnSN like '%".trim($in['kw'])."%' ";
		if(!empty($in['bdate'])) $sdmsg .= ' and ReturnDate > '.strtotime($in['bdate'].'00:00:00').'';
		if(!empty($in['edate'])) $sdmsg .= ' and ReturnDate < '.strtotime($in['edate'].'23:59:59').'';

		$sqlnum  = "select count(*) as allrow FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnSN like '%".trim($in['kw'])."%' ".$sdmsg." ";
		$datasql   = "SELECT ReturnID,ReturnSN,ReturnOrder,ReturnClient,ReturnSendType,ReturnSendStatus,ReturnAbout,ReturnProductW,ReturnProductB,ReturnTotal,ReturnStatus,ReturnDate,ReturnType FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." ".$sdmsg." Order by ReturnID Desc";
	}
		$InfoDataNum = $db->get_row($sqlnum);
		$page = new ShowPage;
		$page->PageSize = 12;
		$page->Total = $InfoDataNum['allrow'];
		$page->LinkAry = array("kw"=>$in['kw'],"stype"=>$in['stype'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
		$list_data = $db->get_results($datasql." ".$page->OffSet());

}
if(!empty($list_data))
{
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['ReturnID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td >
                  		<span title="单号"><a href="return_manager.php?ID=<? echo $lsv['ReturnID'];?>" class="no1"><? echo $lsv['ReturnSN'];?></a></span><br />
						<span title="状态" class=red ><? echo $return_status_arr[$lsv['ReturnStatus']];?></span><br />	
						<span class="selectinput">&nbsp;<input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ReturnID'];?>" value="<? echo $lsv['ReturnID'];?>" /></span>
				  </td>
                  <td >
						<a href="client_content.php?ID=<? echo $lsv['ReturnClient'];?>" target="_blank"><? echo $clientarr[$lsv['ReturnClient']];?></a><br />
						<span title="相关订单"><a href="order_manager.php?SN=<? echo $lsv['ReturnOrder'];?>"><? echo $lsv['ReturnOrder'];?></a></span><br />
				  </td>

				  <td >
					<span title="货运方式" class="font12"> <? echo $lsv['ReturnSendType'];?></span><br />
				    <span title="产品外观" ><? echo $lsv['ReturnProductW'];?></span><br />
					<span title="产品包装" ><? echo $lsv['ReturnProductB'];?></span>
				  </td>
                  <td >
					<? echo nl2br($lsv['ReturnAbout']);?>
					</td>

                  <td >
					&nbsp;<span title="金额" class="font12">¥ <? echo $lsv['ReturnTotal'];?></span><br />
					&nbsp;&nbsp;<? if($lsv['ReturnType']=="M") echo "<span class=font12h title='管理员下单'>M</span>"; else echo "<span class=font12h title='客户下单'>C</span>";?><br />
					<a href="return_manager.php?ID=<? echo $lsv['ReturnID'];?>" >&#8250; 管理退货单</a><br />					
				  </td>
                </tr>
	<? } }else{?>
     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
	<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_return_excel();" >批量导出</a></li></ul></td>
				   <td  align="right">&nbsp;</td>
     			 </tr>
     			 <tr>
       			   <td width="4%" align="center"  height="30" >&nbsp;</td>
   			       <td width="8%" >&nbsp;</td>
   			       <td class="sublink">&nbsp;</td>
				   <td  align="right"><? echo $page->ShowLink('return.php');?></td>
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