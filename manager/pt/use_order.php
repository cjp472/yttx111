<?php 
$menu_flag = "common_count";
include_once ("header.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript">
$(function() {
	$("#bdate").datepicker({changeMonth: true,	changeYear: true});
	$("#edate").datepicker({changeMonth: true,	changeYear: true});
})
				
</script>
</head>

<body>
<?php include_once ("top.php");?> 
<?php include_once ("inc/son_menu_bar.php");?>       

    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>
        </div>

        <div class="line2"></div>

        <div class="bline">
<?php

	$sqlmsg = '';

		if(!empty($in['iid']))  $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; else $in['iid'] = '';
		if(!empty($in['aid']))
		{
			if(empty($areainfoselected['AreaParent']))
			{
				$sqlmsg .= " and ( c.CompanyArea=".$in['aid']." or c.CompanyArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
			}else{
				$sqlmsg .= " and c.CompanyArea=".$in['aid']." ";
			}
		}else{
			$in['aid'] = '';
		}
		if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
		if($in['date_field'] == 'begin_date'){
			$datefield = 's.CS_BeginDate';
		}else{
			$datefield = 's.CS_EndDate';
		}
		if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
		if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

		if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";


			$databasearr1 = $databasearr;
			if(!isset($in['d']) || $in['d'] == '') $in['d'] = array_pop($databasearr1);
			$in['d'] = intval($in['d']); 
			$sqlmsg .= " and c.CompanyDatabase=".$in['d']." ";

			if(empty($in['d'])){
				$sdatabase = DB_DATABASE.'.';
			}else{
				$sdatabase = DB_DATABASE.'_'.$in['d'].'.';
			}


		$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company  where c.CompanyFlag='0' and s.CS_Flag='T' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
		$list_data = $db->get_results($datasql);			
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold"></td>
					 <td align="right"  height="30" class="bottomlinebold">
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="use_order.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">公司名称/系统名称</td>
				  <td align="right" width="8%" class="bottomlinebold">总订单数</td>
				  <td align="right" width="12%" class="bottomlinebold">订单金额</td>
				  <td align="right" width="8%" class="bottomlinebold">30天订单数</td>
				  <td align="right" width="10%" class="bottomlinebold">30天金额</td>
				  <td align="right" width="8%" class="bottomlinebold">活跃用户数</td>
				  <td align="right" width="8%" class="bottomlinebold">有下单用户数</td>
				  <td align="right" width="8%" class="bottomlinebold">30天活跃用户数</td>
				  <td align="right" width="8%" class="bottomlinebold">30天下单用户数</td>
                </tr>
     		 </thead> 

      		<tbody>

<?php
	if(!empty($list_data))
	{		
		//订单
		$sqlorder = "select OrderCompany as CompanyID,count(*) as num,sum(OrderTotal) as oTotal from ".$sdatabase.DATATABLE."_order_orderinfo group by OrderCompany ";
		$ordercount = $db->get_results($sqlorder);
		foreach($ordercount as $v){
			$orderarr[$v['CompanyID']] = $v;
		}

		//订单最近30天
		$nowday = strtotime(date("Y-m-d",strtotime("-30 day"))." 00:00:00");
		$sqlorder30 = "select OrderCompany as CompanyID,count(*) as num,sum(OrderTotal) as oTotal from ".$sdatabase.DATATABLE."_order_orderinfo where OrderDate > ".$nowday." group by OrderCompany ";
		$order30count = $db->get_results($sqlorder30);
		foreach($order30count as $v){
			$order30arr[$v['CompanyID']] = $v;
		}
		//登录情况
		$sqllogin = "SELECT ClientCompany AS CompanyID, COUNT(*) AS num FROM ".DATABASEU.DATATABLE."_order_dealers WHERE LoginCount > 1  GROUP BY ClientCompany";
		$logincount = $db->get_results($sqllogin);
		foreach($logincount as $v){
			$loginarr[$v['CompanyID']] = $v;
		}

		//30天登录情况
		$sql30login = "SELECT ClientCompany AS CompanyID, COUNT(*) AS num FROM ".DATABASEU.DATATABLE."_order_dealers WHERE LoginDate > ".$nowday." and LoginCount > 1  GROUP BY ClientCompany";
		$login30count = $db->get_results($sql30login);
		foreach($login30count as $v){
			$login30arr[$v['CompanyID']] = $v;
		}
		//有下单的用户
		$sqlorderclient = "SELECT 
  COUNT(*) AS cnum,
  ClientCompany AS CompanyID
FROM
  ".$sdatabase.DATATABLE."_order_client c 
  INNER JOIN 
    (SELECT 
      OrderCompany,
      OrderUserID,
      COUNT(*) AS num 
    FROM
      ".$sdatabase.DATATABLE."_order_orderinfo
    GROUP BY OrderUserID) AS o 
    ON c.ClientID = o.OrderUserID 
WHERE o.num > 0 
GROUP BY ClientCompany ";
		$orderclientcount = $db->get_results($sqlorderclient);
		foreach($orderclientcount as $v){
			$ocarr[$v['CompanyID']] = $v;
		}
		//30天内有下单
		$sql30orderclient = "SELECT 
  COUNT(*) AS cnum,
  ClientCompany AS CompanyID
FROM
  ".$sdatabase.DATATABLE."_order_client c 
  INNER JOIN 
    (SELECT 
      OrderCompany,
      OrderUserID,
      COUNT(*) AS num 
    FROM
      ".$sdatabase.DATATABLE."_order_orderinfo where OrderDate > ".$nowday."
    GROUP BY OrderUserID) AS o 
    ON c.ClientID = o.OrderUserID 
WHERE o.num > 0 
GROUP BY ClientCompany ";
		$orderclient30count = $db->get_results($sql30orderclient);
		foreach($orderclient30count as $v){
			$oc30arr[$v['CompanyID']] = $v;
		}

		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
                  <td >10<? echo $lsv['CompanyID'];?></td>
                  <td ><a href="use_content.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
				  <td align="right"><?php echo $orderarr[$lsv['CompanyID']]['num'];?></td>
				  <td align="right"><?php echo '¥ '.number_format($orderarr[$lsv['CompanyID']]['oTotal'],2,'.',',');?></td>

				  <td align="right"><?php echo $order30arr[$lsv['CompanyID']]['num'];?></td>
				  <td align="right"><?php echo ' ¥ '.number_format($order30arr[$lsv['CompanyID']]['oTotal'],2,'.',',');?></td>
				  <td align="right"><?php echo $loginarr[$lsv['CompanyID']]['num'];?></td>
				  <td align="right"><?php echo $ocarr[$lsv['CompanyID']]['cnum'];?></td>
				  <td align="right"><?php echo $login30arr[$lsv['CompanyID']]['num'];?></td>
				  <td align="right"><?php echo $oc30arr[$lsv['CompanyID']]['cnum'];?></td>

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
       				 <td width="100" align="center"><?php echo count($list_data);?></td>
					 <td align="right"  height="30" >
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="use_order.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        <br style="clear:both;" />
    </div>
 
<?php include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>

</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠-";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat(" -+- ", $layer-2);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>