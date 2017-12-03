<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and FinanceClient = ".intval($in['cid'])." ";
		$sidmsg  = '&cid='.$in['cid'];
	}
	if(empty($in['aid']))
	{
		$in['aid'] = '';
	}else{
		$sqlmsg .=" and FinanceAccounts = ".intval($in['aid'])." ";
	}
	if(isset($in['sid']) && $in['sid']!='')
	{
		if(empty($in['sid']))
		{
			$sqlmsg .= " and FinanceFlag = 0 ";
		}else{
			$sqlmsg .= " and FinanceFlag = 2 ";
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
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="finance.php">
        		<tr>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
					</td>
					<td width="80">
					<select id="ptype" name="ptype" >
						<option value="" >⊙ 所有类型</option>
						<option value="Z" <?php if($in['ptype']=='Z') echo 'selected="selected"';?> >┠-线下转账</option>
						<option value="Y" <?php if($in['ptype']=='Y') echo 'selected="selected"';?>>┠-余额支付</option>
						<option value="O" <?php if($in['ptype']=='O') echo 'selected="selected"';?>>┠-在线支付</option>
					</select>
					</td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="finance.php">收款单</a></div></td>
				</tr>
   	          </form>
			 </table>      
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增收款单 " class="button_2" onclick="javascript:window.location.href='finance_add.php'" /> </div>
    <hr style="clear:both;"/>
<div >
<strong><a href="finance.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="finance.php" method="get">
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
</ul>
<br /><br />
<?php include_once ("inc/search_client.php");?>

<hr style="clear:both;" />
<div ><strong><a href="finance.php">所有收款单</a></strong></div>
<ul>
	<?php
		foreach($finance_arr as $skey=>$svar)
		{
			if(isset($in['sid']) && $in['sid']!='')
			{
				if($in['sid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="finance.php?sid='.$skey.''.$sidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>
<hr style="clear:both;" />
<div >
<strong><a href="finance.php">收款账户</a></strong></div>
<ul>
	<?php 
		$accarr = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['uinfo']['ucompany']." ORDER BY AccountsID ASC ");
		$n = 1;
		foreach($accarr as $accvar)
		{
			if($in['aid'] == $accvar['AccountsID']) $smsg = 'class="locationli"'; else $smsg ="";
			echo '<li>'.$n++.'、<a href="finance.php?aid='.$accvar['AccountsID'].'" '.$smsg.' >'.$accvar['AccountsBank'].'<br />('.$accvar['AccountsNO'].')'.'</a></li>';
			$accancearr[$accvar['AccountsID']] = $accvar['AccountsBank'].'('.$accvar['AccountsNO'].')';
		}
		$accancearr[0] = '余额支付';
	?>
</ul>
</div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
                <div style="margin-top:5px;">
                        <img src="img/explanations/images/asdFS1.jpg" style="width:100%;margin-bottom:10px;"/>
                </div>
          <form id="MainForm" name="MainForm" method="post" action="finance_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>

                  <td width="5%" class="bottomlinebold">编号</td>
                  <td  class="bottomlinebold">&nbsp;客户</td>
				  <td width="12%" class="bottomlinebold">&nbsp;收款账户</td>
                  <td width="14%" class="bottomlinebold" >&nbsp;对应订单</td>
					<td width="13%" class="bottomlinebold" >&nbsp;金额/日期</td>
					<td width="16%" class="bottomlinebold" >确认到账日期</td>
                  <td width="8%" class="bottomlinebold" align="left">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	if(!empty($in['bdate'])) $sqlmsg .= " and FinanceToDate >= '".$in['bdate']."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and FinanceToDate <= '".$in['edate']."' ";	
	if(!empty($in['ptype'])) $sqlmsg .= " and FinanceType = '".$in['ptype']."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("cid"=>$in['cid'],"aid"=>$in['aid'],"sid"=>$in['sid'],"bdate"=>$in['bdate'],"edate"=>$in['edate'],'ptype'=>$in['ptype']);        
	$datasql   = "SELECT * FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by FinanceID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
		$tall = 0;
		$tno  = 0;
		 foreach($list_data as $lsv)
		 {
			 if($lsv['FinanceType'] != 'Y'){
				$tall = $tall + $lsv['FinanceTotal'];
				if(!empty($lsv['FinanceFlag'])) $tno = $tno + $lsv['FinanceTotal'];
			 }
?>
                <tr id="line_<? echo $lsv['FinanceID'];?>" class="bottomline" <? if(empty($lsv['FinanceFlag'])) echo 'onmouseover="inStyle(this)"  onmouseout="outStyle(this)"'; else echo 'bgcolor="#fcfcfc"'; ?>   >
                  <td >
				  <span><a href="finance_content.php?ID=<? echo $lsv['FinanceID'];?>" target="_blank" ><? echo $lsv['FinanceID'];?></a></span><br />
				  <span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['FinanceID'];?>" value="<? echo $lsv['FinanceID'];?>" /></span>
				  </td>
                  <td >
					<a href="finance_content.php?ID=<? echo $lsv['FinanceID'];?>" title="查看付款单" target="_blank" ><? echo $clientarr[$lsv['FinanceClient']];?></a>&nbsp;
				  </td>
				  <td >
				  <?
						if($lsv['FinanceFrom'] == 'alipay'){
							echo '<a href="finance_content.php?ID='.$lsv['FinanceID'].'" target="_blank" >【支付宝支付】</a><br />'; 
						}elseif($lsv['FinanceFrom'] == 'allinpay'){
							echo '<a href="finance_content.php?ID='.$lsv['FinanceID'].'" target="_blank" >【网银支付】</a><br />'; 
						}elseif($lsv['FinanceFrom'] == 'yijifu'){
							echo '<a href="finance_content.php?ID='.$lsv['FinanceID'].'" target="_blank" >【快捷支付】</a><br />'; 
						}else{
							echo $accancearr[$lsv['FinanceAccounts']];
						}
				  ?>
				  </td>
                  <td >
					<? if(empty($lsv['FinanceOrder'])) echo '预付款'; else echo str_replace(",","<br />",$lsv['FinanceOrder']);?>
				  </td>
				  <td ><span title='金额' class=font12>¥ <? echo $lsv['FinanceTotal'];?></span><br /><? echo $lsv['FinanceToDate'];?></td>
				  <td id="line_set_<? echo $lsv['FinanceID'];?>"><? if(!empty($lsv['FinanceFlag'])) echo '<font color="#666">'.date("Y-m-d H:i",$lsv['FinanceUpDate']).'</font>'; else echo '未确认 <br />- <a href="javascript:void(0);" onclick="do_validate('.$lsv['FinanceID'].');">确认到账</a>';?></td>
                  <td align="left" id="line_set_del_<? echo $lsv['FinanceID'];?>">
				  <a href="finance_content.php?ID=<? echo $lsv['FinanceID'];?>" target="_blank" >查看</a><br />
				  <? 
				  if(empty($lsv['FinanceFlag']) && ($lsv['FinanceUser'] == $_SESSION['uinfo']['username'] || $_SESSION['uinfo']['userflag'] == "9")){ ?> 
					<a href="finance_edit.php?ID=<? echo $lsv['FinanceID'];?>" >修改</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="do_delete_finance('<? echo $lsv['FinanceID'];?>');">删除</a>
				  <? }else{ ?>
					<font color="gray">修改</font>&nbsp;|&nbsp;<font color="gray">删除</font>
				  <? }?>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
			  <? if(!empty($tall)) {?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			    <tr class="bottomline">
       				 <td colspan="8" height="35" align="center"><strong>  本页合计付款单金额： ¥ <? echo number_format($tall,2,'.',',');?> &nbsp;&nbsp;确认到账金额：¥ <? echo number_format($tno,2,'.',',');?> &nbsp;&nbsp;未到账金额：¥ <? echo number_format($tall - $tno,2,'.',',');?> </strong></td>
       			 </tr>
			 </table>
			 <? }?>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_finance_excel();" >批量导出</a></li></ul></td>
				   <td  align="right">&nbsp;</td>
     			 </tr>
     			 <tr>
       			   <td width="4%" align="center"  height="30" >&nbsp;</td>
   			       <td width="8%" >&nbsp;</td>
   			       <td class="sublink">&nbsp;</td>
				   <td  align="right"><? echo $page->ShowLink('finance.php');?></td>
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