<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

	$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");

	$sdmsg = '';
	$locationmsg = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$cidmsg    = '';
	}else{
		$sdmsg .=" and o.OrderUserID = ".intval($in['cid'])." ";
		$cidmsg  = '&cid='.$in['cid'];
	}

	$valuearr = get_set_arr('product');
	setcookie("backurl", $_SERVER['REQUEST_URI']);

	/* start 判断是否过期 addby lxc 20160421 */
	$timsgu = (strtotime($_SESSION['uc']['EndDate'])+60*60*24);
	$starDate = (strtotime($_SESSION['uc']['BeginDate']." +1 month")+60*60*24);
	$strMsg = '使用';
	$booTimeOut = false;
	if(($timsgu - $starDate) <= 0)
		$strMsg = '试用';
	if(time() > $timsgu){
		$strMsg = '您的'.$strMsg.'时间已经到期，请升级至正式版!';
		$booTimeOut = true;
	}
	/* end */
	
	//定制导出留言 addby lxc 20160428
	$customizedList = '';
	if(@file_exists("./order_list_excel_".$_SESSION['uinfo']['ucompany'].".php")){
		$customizedList =  $_SESSION['uinfo']['ucompany'];
	}
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
		$("#bdate").datepicker({changeMonth: true,	changeYear: true});
		$("#edate").datepicker({changeMonth: true,	changeYear: true});
	});

	function AlertMsg(){
		var strMsg = '<?php echo $strMsg?>';
		alert(strMsg);
	}
</script>
</head>

<body>
<? include_once ("top.php");?>
    <div id="bodycontent">
    <div class="bodyline" style="height:25px;"></div>
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="order.php">
        		<tr>
					<td width="80" align="center"><strong>订单搜索：</strong></td>
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td align="right"><div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a>  <? echo $locationmsg;?></div></td>
				</tr>
   	          </form>
			 </table>
        </div>


        <div class="bline">
       	<div id="sortleft">

<!-- tree --> 
<div class="leftlist"> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增订单 " class="button_2" onclick="javascript:window.location.href='order_add.php'" /> </div> 
<hr style="clear:both;" />
<div ><strong><a href="order.php">药店</a></strong></div>
<ul style="padding: 2px 0 10px 0;">
		<form name="changetypeform" id="changetypeform" action="order.php" method="get">
				<select id="cid" name="cid" onchange="javascript:submit()" style="width:160px !important; width:145px;" class="select2">
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
		</form>
</ul>
<?
if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on"){
?>
<hr style="clear:both;"  />
<div ><strong><a href="order.php">订单初审状态</a></strong></div>
<ul>
	<?php 
			if($in['stype']=="F") $smsg = 'class="locationli"'; else $smsg ='';
			echo '<li><a href="order.php?stype=F" '.$smsg.' >未初审</a></li>';
			if($in['stype']=="T") $smsg = 'class="locationli"';  else $smsg ='';
			echo '<li><a href="order.php?stype=T" '.$smsg.' >已初审</a></li>';
	?>
</ul>
<? }?>

<hr style="clear:both;"  />
<div ><strong><a href="order.php">所有订单</a></strong></div>
<ul>
	<?php 
		foreach($order_status_arr as $skey=>$svar)
		{
			if($skey==5) continue;
			if(isset($in['sid']) && $in['sid']!='')
			{
				if($in['sid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="order.php?sid='.$skey.''.$cidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>

 </div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
            <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >
        	  <table width="98%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="20%" class="bottomlinebold">&nbsp;订单号</td>
                  <td  class="bottomlinebold">收货信息</td>
                  <td width="20%" class="bottomlinebold" >配送</td>
				  <td width="18%" class="bottomlinebold" >款项</td>
                  <td width="10%" class="bottomlinebold" >&nbsp;管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	if(isset($in['sid']) && $in['sid']!='') 	$sdmsg .= " and OrderStatus = ".intval($in['sid'])." ";
	if(!empty($in['stype'])) 	$sdmsg .= " and o.OrderSaler ='".$in['stype']."' ";
	if(!empty($in['kw']))   $sdmsg .= " and o.OrderSN like binary '%%".trim($in['kw'])."%%' ";
	if(!empty($in['bdate'])) $sdmsg .= ' and o.OrderDate > '.strtotime($in['bdate'].'00:00:00').'';
	if(!empty($in['edate'])) $sdmsg .= ' and o.OrderDate < '.strtotime($in['edate'].'23:59:59').'';


	//wangd 2017-11-29 判断是否为代理商客情，代理商客情只能看到自己所管辖商品相关的订单
	$user_flag = trim($_SESSION['uinfo']['userflag']);
	if ($user_flag == '2')
	{
		$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$_SESSION['uinfo']['userid']."");

		$sqlnum = "select count(distinct o.OrderID) as allrow from ".DATATABLE."_order_orderinfo o left join ".DATATABLE."_order_salerclient s ON o.OrderUserID=s.ClientID 
			left join ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid'].
			" and c.AgentID=".$type['UpperID']." ".$sdmsg." ";
		$datasql = "SELECT o.* FROM ".DATATABLE."_order_orderinfo o inner join ".DATATABLE."_order_salerclient s ON o.OrderUserID=s.ClientID  
			left join ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid'].
			" and c.AgentID=".$type['UpperID']." ".$sdmsg." Order by o.OrderID Desc";	
	}
	else // 商业公司客情能看到所有订单
	{
		$sqlnum = "select count(*) as allrow from ".DATATABLE."_order_orderinfo o left join ".DATATABLE."_order_salerclient s ON o.OrderUserID=s.ClientID where  o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." ".$sdmsg." ";
		$datasql = "SELECT o.* FROM ".DATATABLE."_order_orderinfo o inner join ".DATATABLE."_order_salerclient s ON o.OrderUserID=s.ClientID  where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']."  ".$sdmsg." Order by o.OrderID Desc";	
	}

	$InfoDataNum = $db->get_row($sqlnum);
	$page = new ShowPage;
	$page->PageSize = 12;
	$page->Total = $InfoDataNum['allrow'];
	$page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"sid"=>$in['sid'],"stype"=>$in['stype'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
	$list_data = $db->get_results($datasql." ".$page->OffSet());

	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['OrderID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" title="<? echo $lsv['OrderRemark'];?>" >
                  <td height="48" >
					<span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['OrderID'];?>" value="<? echo $lsv['OrderID'];?>" /></span>&nbsp;&nbsp;<? if($lsv['OrderType']=="M") echo "<span class=font12h title='管理员代下单'>M</span>"; elseif($lsv['OrderType']=="S") echo "<span class=font12h title='业务员代下单'>S</span>"; else echo "<span class=font12h title='客户端下单'>C</span>";?><br />
					<span title="订单号" ><a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" class="no"><? echo $lsv['OrderSN'];?></a></span>
					<?php if($lsv['OrderSpecial'] == 'T') {
                          ?>
                        <span style="color:#B61BD2;border:1px solid;margin-left:5px;"><b>特价单</b></span>
                      <?php
                      } ?>
					<br />
                    <span title="订单时间"><? echo date("Y-m-d H:i",$lsv['OrderDate']);?></span>  
				  </td>
                  <td >
						<a href="client_content.php?ID=<? echo $lsv['OrderUserID'];?>" target="_blank"><? echo $clientarr[$lsv['OrderUserID']];?></a><br />
						<span title="收货人"><? echo $lsv['OrderReceiveName'];?></span><br />
						<span title="联系方式"><? echo $lsv['OrderReceivePhone'];?></span><br />
				  </td>

				  <td >
					<span title="配送方式"><? echo $senttypearr[$lsv['OrderSendType']];?></span>&nbsp;&nbsp;
				    <span title="配送状态" class="red">[<? echo $send_status_arr[$lsv['OrderSendStatus']];?>]</span><br />
				    <?php
				    if(!empty($lsv['DeliveryDate']) && $lsv['DeliveryDate'] != '0000-00-00') echo '<span title="交货日期" style="color:green">交货日期：'.$lsv['DeliveryDate'].'</span><br />';
				    ?>
					</td>
                  <td >
					<?
					//2017-12-12 ymm 判断当前登录的人的身份如果是代理商的客情的话就查询出对应的订单信息
				   $user_flag = trim($_SESSION['uinfo']['userflag']);
				   if ($user_flag == '2'){
				        $agent=$db->get_row("select UpperID from ".DATABASEU.DATATABLE."_order_user where UserID=".$_SESSION['uinfo']['userid']."");
				        $agentid=$agent['UpperID'];
				        $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$lsv['OrderID']." and AgentID=".$agentid." order by SiteID asc, BrandID asc, ID asc";
				    }
				    else //管理员和商业公司可以看到所有订单
				    {
				        $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$lsv['OrderID']." order by SiteID asc, BrandID asc, ID asc";
				    }
				   $total=$db->get_results($sql1);
				   $alltotal=0;
				   //2017-12-12 ymm 算出负责的订单总金额
				  foreach ($total as $key => $cvar) {
				  	$alltotal+=$cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
				  }
					 echo "<span title='金额' class=font12>¥ ".$alltotal."</span><br /><span title='付款方式'>".$paytypearr[$lsv['OrderPayType']]."</span><br /><span title='付款状态' class=red>".$pay_status_arr[$lsv['OrderPayStatus']]."</span><br />";?>
					</td>
                  <td >&nbsp;&nbsp;<? 
					if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
					{
						if($lsv['OrderStatus']=="8")
						{
							 echo '<span title="订单状态" class=font12h>客户端取消'; 
						}elseif($lsv['OrderStatus']=="9"){
							 echo '<span title="订单状态" class=font12h>管理端取消'; 
						}else{						 
							if($lsv['OrderSaler']=="T") echo '<span title="初审状态" class=font12>已初审'; else echo '<span title="初审状态" class=red>未初审';
						}
					}else{
						echo '<span title="订单状态" class=red>'.$order_status_arr[$lsv['OrderStatus']];
					}
					?></span><br />
					<a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" >&#8250; 管理订单</a><br />					
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
   			       <td class="sublink">
	   			       <ul>
		   			       <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_excel();'; ?>" >导出详单</a></li>
		   			       <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_orderlist_excel(\''.$customizedList.'\');'; ?>" >导出数据</a></li>
	   			       </ul>
   			       </td>
				   <td  align="right"><? echo $page->ShowLink('order.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>






<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>





