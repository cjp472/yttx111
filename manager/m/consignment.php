<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ("arr_kuaidi.php");
 	$sqlmsg = '';
 	$sidmsg    = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
// 		$sidmsg    = '';
	}else{
		$sqlmsg .=" and ConsignmentClient = ".intval($in['cid'])." ";
		$sidmsg  .= '&cid='.$in['cid'];
	}
	if(isset($in['sid']) && $in['sid']!='')
	{
		if(empty($in['sid']))
		{
			$sqlmsg .= " and ConsignmentFlag = 0 ";
		}else{
			$sqlmsg .= " and ConsignmentFlag != 0 ";
		}
	}else{
		$in['sid'] = '';
	}
	
	if(!empty($in['kw'])){
		$sidmsg  .= '&kw='.$in['kw'];
		$sidmsg  .= '&stype='.$in['stype'];
		switch($in['stype']){
			case 'ConsignmentNO':
				$sqlmsg .= " and ConsignmentNO like '%".$in['kw']."%'";
				break;
			case 'ConsignmentOrder':
				$sqlmsg .= " and ConsignmentOrder like '%".$in['kw']."%'";
				break;
			case 'InceptMan':
				$sqlmsg .= " and InceptMan like '%".$in['kw']."%'";
				break;
			case 'InceptAddress':
				$sqlmsg .= " and InceptAddress like '%".$in['kw']."%'";
				break;
			case 'InceptCompany':
				$sqlmsg .= " and InceptCompany like '%".$in['kw']."%'";
				break;
			case 'InceptPhone':
				$sqlmsg .= " and InceptPhone like '%".$in['kw']."%'";
				break;
			case 'ConsignmentMan':
				$sqlmsg .= " and ConsignmentMan like '%".$in['kw']."%'";
				break;
			default:
				break;
		}
		//$sqlmsg .= " and (ConsignmentNO like '%".$in['kw']."%' or ConsignmentOrder like '%".$in['kw']."%' or InceptMan like '%".$in['kw']."%') ";
	}
	
	if(!empty($in['bdate'])){ $sqlmsg .= " and ConsignmentDate >= '".$in['bdate']."' ";$sidmsg  .= '&bdate='.$in['bdate'];}
	if(!empty($in['edate'])) {$sidmsg  .= '&edate='.$in['edate'];$sqlmsg .= " and ConsignmentDate <= '".$in['edate']."' ";}
	
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
<script src="js/consignment.js?v=<? echo VERID;?>5" type="text/javascript"></script>

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
        	  <form id="FormSearch" name="FormSearch" method="post" action="consignment.php">
        		<tr>
					<td width="40" align="center"><strong>搜索：</strong></td>
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
                    <td style="width:80px;">
                    	<input name="cid" type="hidden" value="<?php echo $in['cid'];?>" />
                    	<input name="sid" type="hidden" value="<?php echo $in['sid'];?>" />
                        <select name="stype">
                            <option value="ConsignmentNO">运单号</option>
                            <option value="ConsignmentOrder" <?php if($in['stype']=='ConsignmentOrder'){echo "selected='selected'";} ?>>订单号</option>
                            <option value="ConsignmentMan" <?php if($in['stype']=='ConsignmentMan'){echo "selected='selected'";} ?> >经办人</option>
                            <option value="InceptMan" <?php if($in['stype']=='InceptMan'){echo "selected='selected'";} ?>>收货人</option>
                            <option value="InceptAddress" <?php if($in['stype']=='InceptAddress'){echo "selected='selected'";} ?>>收货地址</option>
                            <option value="InceptCompany" <?php if($in['stype']=='InceptCompany'){echo "selected='selected'";} ?>>收货单位</option>
                            <option value="InceptPhone" <?php if($in['stype']=='InceptPhone'){echo "selected='selected'";} ?>>收货人电话</option>
                        </select>
                    </td>
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
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增发货单 " class="button_2" onclick="javascript:window.location.href='consignment_add.php'" /> </div> 
<hr style="clear:both;" />
<div ><strong><a href="consignment.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="consignment.php" method="get">
				<input name="kw" type="hidden" value="<?php echo $in['kw'];?>" />
				<input name="stype" type="hidden" value="<?php echo $in['stype'];?>" />
				<input name="bdate" type="hidden" value="<?php echo $in['bdate'];?>" />
				<input name="edate" type="hidden" value="<?php echo $in['edate'];?>" />
                <input name="sid" type="hidden" value="<?php echo $in['sid'];?>" />
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;" >
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
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
</ul><br />
<?php include_once ("inc/search_client.php");?>
<br />
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
                  <td width="6%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">药店/订单</td>
				  <td width="20%" class="bottomlinebold">货运公司</td>
                  <td width="15%" class="bottomlinebold" >发货人/时间</td>
                    <td width="10%" class="bottomlinebold">收货人</td>
				  <td width="8%" class="bottomlinebold" align="right" >签收</td>
                  <td width="12%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php


	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_consignment where ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"stype"=>$in['stype'],"cid"=>$in['cid'],"sid"=>$in['sid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT ConsignmentID,ConsignmentClient,ConsignmentOrder,ConsignmentLogistics,ConsignmentNO,ConsignmentMan,ConsignmentDate,ConsignmentFlag,InceptMan,ConsignmentRemark FROM ".DATATABLE."_order_consignment where ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by ConsignmentID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{     
		$datasql   = "SELECT LogisticsID,LogisticsName,LogisticsPinyi FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." Order by LogisticsID ASC";
		$lvdata = $db->get_results($datasql);
		foreach($lvdata as  $lv)
		{
			$logarr[$lv['LogisticsID']] = $lv;
		}
	 
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ConsignmentID'];?>" <? if(empty($lsv['ConsignmentRemark'])) echo " class='bottomline'"; ?>  <?  if(!empty($lsv['ConsignmentFlag'])) echo 'style = "background-color:#f1f1f1"'; else echo 'onmouseover="inStyle(this)"  onmouseout="outStyle(this)"';?> >
                  <td >
				  <span><a href="consignment_content.php?ID=<? echo $lsv['ConsignmentID'];?>" target="_blank"><? echo $lsv['ConsignmentID'];?></a></span><br />
				  <span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ConsignmentID'];?>" value="<? echo $lsv['ConsignmentID'];?>" /></span>
				  </td>
                  <td ><a href="order_manager.php?SN=<? echo $lsv['ConsignmentOrder'];?>" target="_blank"><? echo $clientarr[$lsv['ConsignmentClient']]."<br />订单号：<span class=TitleNUM>".$lsv['ConsignmentOrder']."</span>";?></a></td>
				  <td >
				  <?
					if($lsv['ConsignmentLogistics']=="0"){ echo '<a href="#">上门自提</a>';}else{
				  ?>
				  <a href="logistics_content.php?ID=<? echo $lsv['ConsignmentLogistics']; ?>" target="_blank">
				  <? 
						echo $logarr[$lsv['ConsignmentLogistics']]['LogisticsName']."</a>"; 
				        echo "<br />运单号：<span class=TitleNUM>".$lsv['ConsignmentNO']."</span>";
						if(in_array($logarr[$lsv['ConsignmentLogistics']]['LogisticsPinyi'],$arr_print_kuaidi)) echo '<br /><a href="javascript:void(0)" onclick="javascript:document.getElementById(\'exe_iframe\').src = \'print_kuaidi.php?ID='.$lsv['ConsignmentID'].'\'" class="buttonb" >&#8250; 打印快递单</a>';
						}
						?></td>
                  <td >发货人：<? echo $lsv['ConsignmentMan'];?><br />时间：<? echo $lsv['ConsignmentDate'];?></td>
                    <td><?php echo $lsv['InceptMan']; ?></td>
                  <td align="right" id="setflagline_<? echo $lsv['ConsignmentID'];?>"><? if(empty($lsv['ConsignmentFlag'])) echo '<a href="javascript:void(0);" onclick="setSendFlag(\''.$lsv['ConsignmentID'].'\')">确认收货</a>'; else echo '<font color="green">已签收</a>';?></td> 				  
                  <td align="right" id="setflagline_link_<? echo $lsv['ConsignmentID'];?>">
				  <? if(empty($lsv['ConsignmentFlag'])){ ?> 
					<a href="consignment_content.php?ID=<? echo $lsv['ConsignmentID'];?>" target="_blank">查看发货单</a>&nbsp;<br /><a href="consignment_edit.php?ID=<? echo $lsv['ConsignmentID'];?>" >修改</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="do_delete('<? echo $lsv['ConsignmentID'];?>');">删除&nbsp;</a>
				  <? }else{ ?>
					<a href="consignment_content.php?ID=<? echo $lsv['ConsignmentID'];?>"  target="_blank" >查看发货单</a>&nbsp;
				  <? }?>
				  </td>
                </tr>
                <? if(!empty($lsv['ConsignmentRemark'])){ ?> 
                <tr class="bottomline" id="lineB_<? echo $lsv['ConsignmentID'];?>" <?  if(!empty($lsv['ConsignmentFlag'])) echo 'style = "background-color:#f1f1f1"';?>>
                <td valign="top">&nbsp;</td>
                <td colspan="6" ><span title="备注" >备注： </span><? echo $lsv['ConsignmentRemark'];?></td> 
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
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_consignment_excel();" >批量导出</a></li></ul></td>
				   <td  align="right">&nbsp;</td>
     			 </tr>
     			 <tr>
       			   <td width="4%" align="center"  height="30" >&nbsp;</td>
   			       <td width="8%" >&nbsp;</td>
   			       <td class="sublink">&nbsp;</td>
				   <td  align="right"><? echo $page->ShowLink('consignment.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" id="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>