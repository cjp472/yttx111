<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['aid']))
{
	$in['aid'] = '';
}else{
	if(empty($areaarr_p[$in['aid']]))
	{
		$sqlmsg .= "and (ClientArea in (SELECT AreaID FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and (AreaParentID=".$in['aid']." OR AreaID=".$in['aid'].") ORDER BY AreaID ASC ) or ClientArea=".$in['aid'].") ";
	}else{
		$sqlmsg .= ' and ClientArea='.$in['aid'].' ';
	}
	$sidmsg   = '&aid='.$in['aid'];
}
if(isset($in['cid']) && $in['cid']) {
    $sqlmsg .= " AND ClientID=" . $in['cid'];
}
$begindate = $in['begindate'];
$enddate = $in['enddate'];
$is_jump = false;
if(empty($in['begindate'])) {
    $begindate = date('Y-m-d',strtotime('-100 years'));
} else {
    $is_jump = true;
}
if(empty($in['enddate'])) {
    $enddate   = date("Y-m-d");
} else {
    $is_jump  = true;
}
$jump = $is_jump ? "&jump=1" : "";

$fwhere = $ewhere = $owhere = $rwhere = "";
$fwhere .= " AND FROM_UNIXTIME(FinanceUpDate) BETWEEN '{$begindate}' AND '{$enddate} 23:59:59' ";
$ewhere .= " AND ExpenseDate BETWEEN '{$begindate}' AND  '{$enddate}' ";
$owhere .= " AND FROM_UNIXTIME(OrderDate) BETWEEN '{$begindate}' AND '{$enddate} 23:59:59' ";
$rwhere .= " AND FROM_UNIXTIME(ReturnDate) BETWEEN '{$begindate}' AND '{$enddate} 23:59:59'";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="js/finance.js?v=3<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});

            $("#begindate").datepicker();
            $("#enddate").datepicker();

		});
    function search_finance() {
        document.searchform.action = 'finance_client.php';
        document.searchform.target = '_self';
        document.searchform.submit();
    }
</script>
<style>

</style></head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
  
   	        </div>            
			<div class="location"><strong>当前位置：</strong> <a href="#">应收款</a> </div>        
        </div>
    	
        <div class="line2"></div>
        <div class="bline">	
		<div id="sortleft" style="border:none;>
<!-- tree -->
<div id="sidetree"> 
<div class="treeheader">
<strong><a href="finance_client.php">地区分类</a></strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		echo ShowTreeMenu($sortarr,0);

		foreach($sortarr as $areavar)
		{
			$areaarr[$areavar['AreaID']]   = $areavar['AreaName'];
		}
	?>	
</ul>
 </div>
<!-- tree --> 
		</div>


		<div id="sortright">
            <fieldset>
                <legend><strong>查询条件：</strong></legend>
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF">
                    <form id="searchform" name="searchform" method="get" action="finance_client.php" >
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
                            <td width="60" align="center"><strong>时间：</strong>从 </td>
                            <td width="100"><input name="begindate" type="text" id="begindate" maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   /></td>
                            <td width="20">到</td>
                            <td width="100"><input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   /></td>
                            <td >
                                <input type="button" name="newbutton1" id="newbutton1" value="查 看" class="mainbtn" onclick="search_finance();" />
                            </td>
                        </tr>
                    </form>
                </table>
            </fieldset>
            <br style="clear:both;" />

            <fieldset>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
               <thead>
                <tr>
				  <td width="4%" class="bottomlinebold">&nbsp;</td>
				  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="10%" class="bottomlinebold">地区</td>
				  <td width="20%" class="bottomlinebold">药店帐号</td>
				  <td width="28%" class="bottomlinebold">药店名称</td>
				  <td width="25%" class="bottomlinebold" align="right">期末应收(元)</td>
				  <td width="10%" align="center" class="bottomlinebold">查看</td>
                </tr>
     		 </thead>      		
      		<tbody>

		<?php	
				$statsql2  = "SELECT sum(FinanceTotal) as Ftotal,FinanceClient from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') {$fwhere} group by FinanceClient  ";
				$statdata2 = $db->get_results($statsql2);

				foreach($statdata2 as $v)
				{
					$rdata['finance'][$v['FinanceClient']] = $v['Ftotal'];
				}
		
				$statsql4 = "SELECT sum(ExpenseTotal) as Ftotal,ClientID from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." and FlagID = '2' {$ewhere} group by ClientID ";
				$statdata4 = $db->get_results($statsql4);

				foreach($statdata4 as $v)
				{
					$rdata['expense'][$v['ClientID']] = $v['Ftotal'];
				}

// 				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']."  and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 {$owhere} group by OrderUserID ";
				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']."  and OrderStatus!=8 and OrderStatus!=9 {$owhere} group by OrderUserID ";
				$statdatat = $db->get_results($statsqlt);

				foreach($statdatat as $v)
				{
					$rdata['order'][$v['OrderUserID']] = $v['Ftotal'];
				}

				$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal,ReturnClient from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']."  and (ReturnStatus=3 or ReturnStatus=5) {$rwhere} group by ReturnClient";
				$statdata1 = $db->get_results($statsqlt1);

				foreach($statdata1 as $v)
				{
					$rdata['return'][$v['ReturnClient']] = $v['Ftotal'];
				}

		$clientdata = $db->get_results("select ClientID,ClientArea,ClientName,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ".$sqlmsg." order by ClientID asc");
		$n=0;
		$alltotal = 0;
		foreach($clientdata as $var)
		{
			$n++;

		?>
                <tr id="line_<? echo $n;?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td> <span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $var['ClientID'];?>" value="<? echo $var['ClientID'];?>" /></span></td>
				  <td height="28"><? echo $n;?></td>
				  <td><? echo $areaarr[$var['ClientArea']];?></td>
                  <td ><? echo $var['ClientName'];?></td>
				  <td ><a href="client_content.php?ID=<?php echo $var['ClientID'];?>" target="_blank"><? echo $var['ClientCompanyName'];?>&nbsp;</a></td>
				  <td align="right"> <?php
					$tall = floatval($rdata['order'][$var['ClientID']]) - floatval($rdata['return'][$var['ClientID']]) - floatval($rdata['expense'][$var['ClientID']]) - floatval($rdata['finance'][$var['ClientID']]);
					$alltotal = $alltotal + $tall;
					echo number_format($tall,2,'.',',');
					?>&nbsp;</td>
				  <td align="center"><a href="reconciliation.php?cid=<?php echo $var['ClientID'];?>&begindate=<?php echo $in['begindate']; ?>&enddate=<?php echo $in['enddate']; ?><?php echo $jump; ?>" target="_blank">查看对帐</a></td>
                </tr>
<? }?>
                <tr id="line_<? echo $n+1;?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td height="30" ><span class="selectinput"><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></span></td>
   			       <td >全选/取消</td>
				   <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_finace_client_excel();" >批量导出</a></li></ul></td>
                  <td >&nbsp; <strong>合计：</strong></td>
				  <td >&nbsp;</td>
                  <td align="right"> <strong><? echo number_format($alltotal,2,'.',','); ?>&nbsp;</strong></td>
				  <td >&nbsp;</td>
				</tr>
 				</tbody>                
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </fieldset> 
        </div>
		</div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				if($var['AreaParentID']=="0")
				{
					$frontMsg  .= '<li><a href="finance_client.php?aid='.$var['AreaID'].'"><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="finance_client.php?aid='.$var['AreaID'].'">'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>