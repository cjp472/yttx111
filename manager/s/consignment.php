<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

	$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");

 	$sqlmsg = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and c.ConsignmentClient = ".intval($in['cid'])." ";
		$sidmsg  = '&cid='.$in['cid'];
	}
	if(isset($in['sid']) && $in['sid']!='')
	{
		if(empty($in['sid']))
		{
			$sqlmsg .= " and c.ConsignmentFlag = 0 ";
		}else{
			$sqlmsg .= " and c.ConsignmentFlag != 0 ";
		}
	}else{
		$in['sid'] = '';
	}

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
<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="consignment.php">
        		<tr>
					<td width="100" align="center"><strong>运单/订单号：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="consignment.php">发货信息</a></div></td>
				</tr>
   	          </form>
			 </table>  
    	</div>
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist">

<hr style="clear:both;" />
<div ><strong><a href="consignment.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="consignment.php" method="get">
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
<hr style="clear:both;" />
<div ><strong><a href="consignment.php">所有发货单</a></strong></div>
<ul>
	<?php
		foreach($incept_arr as $skey=>$svar)
		{
			if($skey==2) continue;
			if(isset($in['sid']) && $in['sid']!='')
			{
				if($in['sid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="consignment.php?sid='.$skey.''.$sidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
	<li>&nbsp;</li>
</ul>
<div  style="margin:32px 0 0 0; clear:both; width:170px; height:40px; overflow:hidden;"><a href="kuaidi_search.php" target="_blank"  title="快递查询"><img src="img/c1.jpg" alt="快递查询" /></a></div>
<div style="margin-top:12px; clear:both; width:170px; height:40px; overflow:hidden;"><a href="wuliu_search.php" target="_blank"  title="物流查询"><img src="img/c2.jpg" alt="物流查询" /></a></div>
 </div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="consignment_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="2">
               <thead>
                <tr>

                  <td width="8%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">药店/订单</td>
				  <td width="25%" class="bottomlinebold">货运公司</td>
                  <td width="20%" class="bottomlinebold" >发货人/时间</td>
				  <td width="8%" class="bottomlinebold" align="right" >签收</td>
                  <td width="12%" class="bottomlinebold" align="center">查看</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	if(!empty($in['kw']))  $sqlmsg .= " and (c.ConsignmentNO like '%".$in['kw']."%' or c.ConsignmentOrder like '%".$in['kw']."%') ";
	if(!empty($in['bdate'])) $sqlmsg .= " and c.ConsignmentDate >= '".$in['bdate']."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and c.ConsignmentDate <= '".$in['edate']."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_consignment c left join ".DATATABLE."_order_salerclient s ON c.ConsignmentClient=s.ClientID where c.ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"sid"=>$in['sid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT c.ConsignmentID,c.ConsignmentClient,c.ConsignmentOrder,c.ConsignmentLogistics,c.ConsignmentNO,c.ConsignmentMan,c.ConsignmentDate,c.ConsignmentFlag,c.ConsignmentRemark FROM ".DATATABLE."_order_consignment  c left join ".DATATABLE."_order_salerclient s ON c.ConsignmentClient=s.ClientID where c.ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." ".$sqlmsg." Order by c.ConsignmentID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{     
		$datasql   = "SELECT LogisticsID,LogisticsName,LogisticsContact,LogisticsPhone,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." Order by LogisticsID ASC";
		$lvdata = $db->get_results($datasql);
		foreach($lvdata as  $lv)
		{
			$logarr[$lv['LogisticsID']] = $lv['LogisticsName'];
		}
	 
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ConsignmentID'];?>" <? if(empty($lsv['ConsignmentRemark'])) echo " class='bottomline'"; ?> <?  if(!empty($lsv['ConsignmentFlag'])) echo 'style = "background-color:#f1f1f1"'; else echo 'onmouseover="inStyle(this)"  onmouseout="outStyle(this)"';?> >
                  <td >
				  <span><a href="consignment_content.php?ID=<? echo $lsv['ConsignmentID'];?>" target="_blank"><? echo $lsv['ConsignmentID'];?></a></span>
				  </td>
                  <td ><a href="order_manager.php?SN=<? echo $lsv['ConsignmentOrder'];?>" target="_blank"><? echo $clientarr[$lsv['ConsignmentClient']]."<br />订单号：<span class=TitleNUM>".$lsv['ConsignmentOrder']."</span>";?></a></td>
				  <td >
				  <?
					if($lsv['ConsignmentLogistics']=="0"){ echo '<a href="#">上门自提</a>';}else{
					?>
				  <a href="logistics_content.php?ID=<? echo $lsv['ConsignmentLogistics']; ?>" target="_blank">
				  <? 
						echo $logarr[$lsv['ConsignmentLogistics']]."</a>"; 
						}
						echo "<br />运单号：<span class=TitleNUM>".$lsv['ConsignmentNO']."</span>";?></td>
                  <td >发货人：<? echo $lsv['ConsignmentMan'];?><br />时间：<? echo $lsv['ConsignmentDate'];?></td>  
                  <td align="right" id="setflagline_<? echo $lsv['ConsignmentID'];?>">
				  <? if(empty($lsv['ConsignmentFlag'])) echo '<a href="javascript:void(0);" onclick="setSendFlag(\''.$lsv['ConsignmentID'].'\')">确认收货</a>'; else echo '<font color="green">已签收</a>';?>
				  </td> 				  
                  <td align="right" id="setflagline_link_<? echo $lsv['ConsignmentID'];?>">
					<a href="consignment_content.php?ID=<? echo $lsv['ConsignmentID'];?>"  target="_blank" >查看发货单</a>&nbsp;
				  </td>
                </tr>
                <? if(!empty($lsv['ConsignmentRemark'])){ ?> 
                <tr class="bottomline" <?  if(!empty($lsv['ConsignmentFlag'])) echo 'style = "background-color:#f1f1f1"';?>>
                <td ><span title="备注" >备注： </span></td>
                <td colspan="5" ><span><? echo $lsv['ConsignmentRemark'];?></span></td> 
                </tr>
                <? }?>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('consignment.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" />
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>