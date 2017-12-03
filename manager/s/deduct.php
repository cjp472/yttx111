<?php 
$menu_flag = "";
$pope		   = "";
include_once ("header.php");
include_once ("arr_data.php");

$sqlmsg = " and DeductUser = ".$_SESSION['uinfo']['userid']." ";
if(!empty($in['tid']))
{
	$sqlmsg .= " and DeductStatus = '".$in['tid']."' ";
}else{
	$in['tid'] = '';
}
if(!empty($in['cid']))
{
	$sqlmsg .= " and ClientID = '".$in['cid']."' ";
}else{
	$in['cid'] = '';
}

$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");
foreach($clientdata as  $lv)
{
	$clientarr[$lv['ClientID']] = $lv['ClientCompanyName'];
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
<script src="js/saler.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
        	  <form id="FormSearch" name="FormSearch" method="post" action="deduct.php">
        		<tr>
					<td width="100" align="center"><strong>订单号：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
					<td align="center" width="80">起止时间：</td>
					<td width="240"><input type="text" name="bdate" id="bdate" class="inputline" style="width:100px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:100px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="deduct.php">提成明细</a></div></td>
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
<div ><strong><a href="order.php">药店</a></strong></div>
<ul style="padding: 2px 0 10px 0;">
				<form name="changetypeform" id="changetypeform" action="deduct.php" method="get">
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
<div ><strong><a href="deduct.php">所有业务提成</a></strong></div>
<ul>
	<?php
		$deductarr = array('T'=>'已发放提成','F'=>'未放发提成');
		foreach($deductarr as $skey=>$svar)
		{
			if($skey==2) continue;
			if(isset($in['tid']) && $in['tid']!='')
			{
				if($in['tid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="deduct.php?tid='.$skey.''.$sidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>

 </div>
<!-- tree -->   
       	  </div>
    
		<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="2">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold" >序号</td>
                  <td width="18%" class="bottomlinebold">药店</td>
                  <td width="20%" class="bottomlinebold" >订单号</td>
				  <td width="12%" class="bottomlinebold" align="right" >提成金额</td>
                  <td width="12%" class="bottomlinebold" align="center">发放状态</td>
                  <td width="18%" class="bottomlinebold" align="center">发放时间</td>
                  <td  class="bottomlinebold" align="center">明细/说明</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	if(!empty($in['kw']))  $sqlmsg .= " and OrderSN = '".$in['kw']."' ";
	if(!empty($in['bdate'])) $sqlmsg .= " and DeductDate > '".strtotime($in['bdate'].'00:00:00')."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and DeductDate < '".strtotime($in['edate'].'23:59:59')."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"sid"=>$in['sid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by DeductID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{     
		$n=1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['DeductID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td >  <? echo $n++; ?> </td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['ClientID']; ?>" target="_blank"><? echo $clientarr[$lsv['ClientID']];?></a></td>
                  <td >
				  <?php if($lsv['DeductType'] == 'S'){?>
				  <a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" target="_blank"><? echo $lsv['OrderSN'];?></a>
				  <?php }else{ echo $lsv['OrderSN'];}?>
				  </td>  
                  <td align="right"><? echo "<span  class=font12>¥ ".$lsv['DeductTotal']."</span>";?></td>  
                  <td align="center" id="status_<? echo $lsv['DeductID'];?>"><? 
					if($lsv['DeductStatus']=="T"){
						echo '<span class="title_green_w" title="已发放" >√</span> ';
					}elseif($lsv['DeductStatus']=="F"){ ?> 
					X
				  <? }?></td>
                  <td align="center" id="status_<? echo $lsv['DeductID'];?>"><? 
					if($lsv['DeductStatus']=="T"){
						echo date("Y-m-d",$lsv['DeductToDate']); 
					}?></td>  
                  <td >
				  <?php if($lsv['DeductType'] == 'S'){?>
					<div align="center"><a href="javascript:void(0)" onclick="show_deduct(<? echo $lsv['DeductID'];?>);" >明细</a></div>
				  <?php }else{ echo $lsv['Remark'];}?>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%" align="center"  height="30" ></td>
				   <td  align="right"><? echo $page->ShowLink('deduct.php');?></td>
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

    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">订单提成明细</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>

</body>
</html>