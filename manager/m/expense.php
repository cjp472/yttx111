<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

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

<script src="js/finance.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
    	<div id="searchline" align="right">
			<input type="button" name="newbutton" id="newbutton" value="新增其他款项" class="button_2" onclick="javascript:window.location.href='expense_add.php'" />     
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

		<fieldset>     
			<legend><strong>查询条件：</strong></legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF">          
                <form id="searchform" name="searchform" method="get" action="expense.php" >
				<tr>
                  <td width="80" align="center"><strong>药店：</strong></td>
                  <td width="200"><label>
                <select id="cid" name="cid" style="width:180px;" class="select2">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select></label></td>
				<td width="50"><strong>类型：</strong></td>
				<td width="120">
				<select id="tid" name="tid" >
				<option value="" >⊙ 所有类型</option>
		<?php 
			$billdata = $db->get_results("select BillID,BillNO,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." ");
			foreach($billdata as $var)
			{
				$billarr[$var['BillID']] = $var['BillName'];
				if($in['tid'] == $var['BillID']) $smsg = 'selected="selected"'; else $smsg ="";

				echo '<option value="'.$var['BillID'].'" '.$smsg.' title="'.$var['BillName'].'" >'.$var['BillNO'].' - '.$var['BillName'].'</option>';
			}
		?>
				</select>				
				</td>
				<td width="80">
				    <select id="fid" name="fid" >
						<option value="" >⊙ 所有状态</option>
						<option value="1" >┠-未审核</option>
						<option value="2" >┠-已审核</option>
					</select>
				</td>
                  <td width="60" align="center"><strong>时间：</strong>从 </td>
                  <td width="80"><input name="bdate" type="text" id="bdate" maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   /></td>
				  <td width="20">到</td>
				  <td width="80"><input name="edate" type="text" id="edate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   /></td>
				  <td ><input type="submit" name="newbutton1" id="newbutton1" value="查 看" class="mainbtn"  /></td>
                </tr>
				</form>
            </table>
            </fieldset>             
            <br style="clear:both;" />
		
		
		<fieldset>     
			<legend><strong>费用明细：</strong></legend>
          <form id="MainForm" name="MainForm" method="post" action="expense_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  <td width="4%" class="bottomlinebold">&nbsp;</td>
                  <td width="4%" class="bottomlinebold">行号</td>
                  <td width="24%" class="bottomlinebold">&nbsp;药店</td>
				  <td width="10%" class="bottomlinebold">&nbsp;其他款项类型</td>
                  <td width="8%" class="bottomlinebold" >&nbsp;金额</td>
				  <td width="8%" class="bottomlinebold" >&nbsp;日期</td>
				  <td  class="bottomlinebold" >&nbsp;说明</td>
				  <td width="8%" class="bottomlinebold" align="left">操作员</td>
				  <td width="4%" align="center" class="bottomlinebold" >审核</td>
                  <td width="4%" class="bottomlinebold" align="center">管理</td>
				  
                </tr>
     		 </thead>      		
      		<tbody>
<?php

	$sqlmsg = '';
	if(!empty($in['cid'])) $sqlmsg .= " and ClientID = ".intval($in['cid'])." ";
	if(!empty($in['tid'])) $sqlmsg .= " and BillID = ".intval($in['tid'])." ";	
	if(!empty($in['fid'])) $sqlmsg .= " and FlagID = '".$in['fid']."' ";
	if(!empty($in['bdate'])) $sqlmsg .= " and ExpenseDate >= '".$in['bdate']."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and ExpenseDate <= '".$in['edate']."' ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("cid"=>$in['cid'],"tid"=>$in['tid'],"fid"=>$in['fid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT ExpenseID,ClientID,BillID,ExpenseTotal,ExpenseDate,ExpenseRemark,ExpenseUser,FlagID FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by ExpenseID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	$total = $totals = 0;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		 foreach($list_data as $lsv)
		 {
			$total = $total + $lsv['ExpenseTotal'];
			if($lsv['FlagID'] == "2") $totals = $totals + $lsv['ExpenseTotal'];
?>
                <tr id="line_<? echo $lsv['ExpenseID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
				  <td> <span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ExpenseID'];?>" value="<? echo $lsv['ExpenseID'];?>" /></span></td>
                  <td >
				  <span><?php echo $n++;?></span>				 
				  </td>
                  <td >
					<a href="client_content.php?ID=<? echo $lsv['ClientID'];?>"  target="_blank" ><? echo $clientarr[$lsv['ClientID']];?></a>&nbsp;
				  </td>
				  <td >
				  <?php echo $billarr[$lsv['BillID']];?></td>
                  <td >¥ <? echo $lsv['ExpenseTotal'];?> </td>
				  <td ><? echo $lsv['ExpenseDate'];?></td>
				  <td ><? echo $lsv['ExpenseRemark'];?></td>
					<td ><? echo $lsv['ExpenseUser'];?></td>				  
				  <td align="center" id="line_set_<? echo $lsv['ExpenseID'];?>" ><?php if($lsv['FlagID'] == "2") echo '<font color=green>√</font>'; else echo '[<a href="#" onclick="do_validate_expense(\''.$lsv['ExpenseID'].'\')" >审核</a>]';?></td>
                  <td align="center" id="line_set_del_<? echo $lsv['ExpenseID'];?>">
				  <?php if($lsv['FlagID'] == "2") {?>
					<font color="gray">删除</font>
				  <?php }else{?>
					<a href="javascript:void(0);" onclick="do_delete_expense('<? echo $lsv['ExpenseID'];?>');">删除</a>
				  <?php }?>
				  </td>
				  
                </tr>
				<?php }?>
     			 <tr class="bottomline">
       			   <td   height="30" ><span class="selectinput"><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></span></td>
   			       <td >全选/取消</td>
				   <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_expense_excel();" >批量导出</a></li></ul></td>
   			       <td ><strong>本页合计：</strong></td>
				   <td colspan="6">总金额：<strong>¥  <?php echo number_format($total,2,'.',',');?></strong> - 已审核金额：<strong>¥  <?php echo number_format($totals,2,'.',',');?></strong></td>

     			 </tr>
<? }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>

 				</tbody>                
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td height="30" align="right"><? echo $page->ShowLink('expense.php');?></td>
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