<?php 
$menu_flag = "saler";
$pope		   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

 	$sqlmsg = " and DeductType = 'R' ";
	if(empty($in['sid']))
	{
		$in['sid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and DeductUser = ".intval($in['sid'])." ";
		$sidmsg  = '&sid='.$in['sid'];
	}
	if(!empty($in['tid']))
	{
		$sqlmsg .= " and DeductStatus = '".$in['tid']."' ";
	}else{
		$in['tid'] = '';
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
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="deduct_other.php">
        		<tr>
					<td width="100" align="center"><strong>订单号：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="deduct_other.php">其他提成明细</a></div></td>
				</tr>
   	          </form>
			 </table>  
    	</div>
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist">
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增提成 " class="button_2" onclick="javascript:window.location.href='deduct_other_add.php'" /> </div> 
<hr style="clear:both;" />
<div ><strong><a href="deduct_other.php">所有客情官</a></strong></div>
<ul>
	<form name="changetypeform" id="changetypeform" action="deduct_other.php" method="get">
		<select id="sid" name="sid" onchange="javascript:submit()" class="select2" style="width:85%;">
		<option value="" >⊙ 所有客情官</option>
		<?php 
		$n = 0;
		$clientdata = $db->get_results("select UserID,UserName,UserTrueName from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S'  order by UserID asc");
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['sid'] == $areavar['UserID']) $smsg = 'selected="selected"'; else $smsg ="";
			echo '<option value="'.$areavar['UserID'].'" '.$smsg.' title="'.$areavar['UserTrueName'].'"  > '.$areavar['UserName'].' - '.$areavar['UserTrueName'].'</option>';
			$salerarr[$areavar['UserID']] = $areavar['UserTrueName'];
		}
	?>
				</select>
				</form>
</ul>
<hr style="clear:both;" />
<div ><strong><a href="deduct_other.php">所有其他提成</a></strong></div>
<ul>
	<?php
		$deductarr = array('T'=>'已发放提成','F'=>'未发放提成');
		foreach($deductarr as $skey=>$svar)
		{
			if($skey==2) continue;
			if(isset($in['tid']) && $in['tid']!='')
			{
				if($in['tid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="deduct_other.php?tid='.$skey.''.$sidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
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
                  <td width="5%" class="bottomlinebold">&nbsp;</td>
                  <td width="20%" class="bottomlinebold">药店</td>
				  <td width="10%" class="bottomlinebold">客情官</td>
                  <td width="10%" class="bottomlinebold" >订单号</td>
				  <td width="12%" class="bottomlinebold" align="right" >提成金额</td>
                  <td width="16%" class="bottomlinebold" align="center">发放状态/时间</td>
                  <td  class="bottomlinebold" >说明</td>
				  <td width="5%" align="center" class="bottomlinebold">删除</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	if(!empty($in['kw']))  $sqlmsg .= " and OrderSN = '".$in['kw']."' ";
	if(!empty($in['bdate'])) $sqlmsg .= " and DeductDate > '".strtotime($in['bdate'].'00:00:00')."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and DeductDate < '".strtotime($in['edate'].'23:59:59')."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"tid"=>$in['tid'],"sid"=>$in['sid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by DeductID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{     
		$cinfo = $db->get_results("select ClientID,ClientCompanyName FROM ".DATATABLE."_order_client  where ClientCompany=".$_SESSION['uinfo']['ucompany']."  limit 0,10000");
		foreach($cinfo as  $lv)
		{
			$clientarr[$lv['ClientID']] = $lv['ClientCompanyName'];
		}

		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['DeductID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td >
				  <span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['DeductID'];?>" value="<? echo $lsv['DeductID'];?>"  /></span>
				  </td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['ClientID']; ?>" target="_blank"><? echo $clientarr[$lsv['ClientID']];?></a></td>
				  <td ><? echo $salerarr[$lsv['DeductUser']];?></td>
                  <td ><? echo $lsv['OrderSN'];?></td>  
                  <td align="right"><? echo "<span  class=font12>¥ ".$lsv['DeductTotal']."</span>";?></td>  
                  <td align="center" id="status_<? echo $lsv['DeductID'];?>"><? 
					if($lsv['DeductStatus']=="T"){
						echo '<span class="title_green_w" title="已发放" >√</span> '.date("Y-m-d",$lsv['DeductToDate']); 
					}elseif($lsv['DeductStatus']=="F"){ ?> 
					<a href="javascript:void(0)" onclick="do_validate(<? echo $lsv['DeductID'];?>);" title="点此操作发放提成" >未发放</a>
				  <? }?></td> 				  
                  <td >
					<? echo $lsv['Remark'];?>
				  </td>
				  <td align="center">
				  <?php if($lsv['DeductStatus']=="F"){?>
				  <a href="javascript:void(0);" onclick="do_deduct_delete('<? echo $lsv['DeductID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>
				  <?php }else{ echo '&nbsp;';}?>
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
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="do_more_validate();" >批量发放</a></li><li><a href="javascript:void(0);" onclick="deduct_excel();" >批量导出</a></li></ul></td>
				   <td  align="right"><? echo $page->ShowLink('deduct_other.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

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