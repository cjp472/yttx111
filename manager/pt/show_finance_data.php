<?php 
$menu_flag = "manager";
include_once ("header.php");

if($_SESSION['uinfo']['userid'] != "1" && $_SESSION['uinfo']['userid'] != "9") exit('非法路径!');

	$finance_arr = array(
		'0'			=>  '在途',
		'2'			=>  '确认到帐'
 	 );

$cid = intval($in['cid']);
$csql   = "SELECT CompanyID,CompanyDatabase FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$cid." ORDER BY CompanyID ASC limit 0,1";
$cominfo = $db->get_row($csql);
if(!empty($cominfo['CompanyDatabase']))
{
	$sdbname = DB_DATABASE.'_'.$cominfo['CompanyDatabase'].".";
}else{
	$sdbname = DB_DATABASE.'.';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<link rel="stylesheet" href="css/showpage.css" />
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:28px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:bold; height:28px; padding:2px;}
.tcheader{font-weight:bold; background: #efefef; height:25px; padding:2px;}
input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}


.button_1{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/anns.jpg) 0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_1:hover {background:url(./img/anns.jpg) 0 -26px no-repeat;}

.button_3{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/dnn5.jpg)  0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_3:hover {background:url(./img/dnn5.jpg) 0 -26px no-repeat;}

.bottomlinebold{border-bottom:#CCCCCC solid 2px; font-weight:bold; height:32px;}
.bottomlinebold td{border-bottom:#CCCCCC solid 2px; font-weight:bold; height:32px;}
.bottomline td{border-bottom:#CCCCCC dotted 1px; height:auto; padding:4px 0; line-height:28px;}
.bottonline input{border:0;}
.sublink ul{float:left; list-style:none; margin:0; padding:0;}
.sublink ul li{background: #0774bc; border:#0774bc 1px solid;  color: #fff; font-size: 12px; padding:1px; height:20px; line-height:20px; float:left; width:65px; margin:0 4px; text-align:center;}
.sublink ul li a{text-decoration:none; color:#fff; display:block;  font-size:12px;}
.sublink ul li a:hover{text-decoration:none; color:#fff; display:block;  font-size:12px; }

-->
</style>
<script type="text/javascript">
	$(function() {
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});

	var old_bg="";
	function inStyle(obj)
	{
		old_bg=obj.style.background;
		obj.style.background="#edf3f9";
	}
	function outStyle(obj)
	{
		obj.style.background=old_bg;
	}

	function delete_data(did)
	{
		if(confirm('确认删除吗?'))
		{
			var cid = "<?php echo $cid;?>";
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_delete.php",
				{m:"delete_finance", cid: cid, did:did},
				function(data){
					if(data == "ok"){
						var delline = "line_" + did;
						$("#"+delline).hide();
						$.blockUI({ message: "<p>删除成功!</p>" }); 
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" }); 
					}					
				}		
			);
		}else{
			return false;
		}
		window.setTimeout($.unblockUI, 1500); 
	}

	function delete_mul_data()
	{
		if(confirm('确认删除吗?'))
		{
			var cid = "<?php echo $cid;?>";
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_delete.php?m=delete_finance", $("#MainForm").serialize(),
				function(data){
					if(data == "ok"){
						$.blockUI({ message: "<p>删除成功!</p>" });
						window.location.reload();
						$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" });
						$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					}				
				}		
			);
			$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
			window.setTimeout($.unblockUI, 1500);
		}
	}
</script>
</head>

<body>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="tc" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="show_finance_data.php">		  
        		<input type="hidden" name="cid" id="cid2"  value="<? if(!empty($cid)) echo $cid;?>"   />
				<thead>
				<tr>
					<td width="80" align="center"><strong>款项搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" style="width:200px;" /></td>
        	        <td width="80">
        	        <select name="stype" id="stype" class="selectline">
						<option value="idsn" <?php if($in['stype']=="idsn") echo 'selected="selected"'; ?> >收款单</option>
						<option value="ordersn" <?php if($in['stype']=="ordersn") echo 'selected="selected"'; ?> >相关订单</option>						
					</select>
					</td>
        	        <td width="80">
        	        <select name="ostatus" id="ostatus" class="selectline">
						<option value=""  >状态</option>
						<?php
						foreach($finance_arr as $key=>$var)
						{
							if(isset($in['ostatus']) && $in['ostatus']!='')
							{
								if($in['ostatus'] == $key) $smsg = 'selected="selected"'; else $smsg ="";
							}else{
								$smsg =" ";
							}
							echo '<option value="'.$key.'" '.$smsg.' >'.$var.'</option>';
						}
						?>
						
					</select>
					</td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="begindate" id="begindate" class="inputline" style="width:80px;" value="<? if(!empty($in['begindate'])) echo $in['begindate'];?>" /> - <input type="text" name="enddate" id="enddate" class="inputline" style="width:80px;" value="<? if(!empty($in['enddate'])) echo $in['enddate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="redbtn" id="searchbutton" value="搜 索" /> </td>
					<td align="right"><input name="expensebutton" type="button" class="bluebtn" id="expensebutton" value="其他款项" onclick="window.location.href='show_expense_data.php?cid=<?php echo $cid;?>'" /></td>
				</tr>
				</thead>
   	          </form>
			 </table> 
			 

		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			<input type="hidden" name="cid" id="cid"  value="<? if(!empty($cid)) echo $cid;?>"   />
        	 <div style="width:100%; height:590px; overflow:auto; margin:0; padding:0;">
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  <td width="8%" class="bottomlinebold" >&nbsp;行号</td>
                  <td width="8%" class="bottomlinebold" >&nbsp;&nbsp;ID</td>
				  <td width="14%" class="bottomlinebold">对应订单</td>
                  <td  class="bottomlinebold">经销商</td>
                  <td width="10%" class="bottomlinebold" >款项</td>
                  <td width="10%" class="bottomlinebold" >时间</td>
				  <td width="8%" class="bottomlinebold" >状态</td>
                  <td width="6%" class="bottomlinebold" nowrap="nowrap">&nbsp;管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php	
	$pagearr['cid'] = $cid;
	$sqlmsg = '';

	if(!empty($in['kw'])){	
		$in['kw'] = str_replace(' ',',',$in['kw']);			
		if($in['stype']=='idsn'){
			
			$sqlmsg .=  " AND FinanceID IN(".$in['kw'].")";
		}else{
			$in['kw'] = str_replace(",","%",$in['kw']);
			$sqlmsg .=  " AND FinanceOrder like '%".$in['kw']."%'";
		}
	}
	if(isset($in['ostatus']) and $in['ostatus'] != ''){
		$sqlmsg .= ' and FinanceFlag = '.$in['ostatus'].' ';
		$pagearr['ostatus'] = $in['ostatus'];
	}
	if(!empty($in['begindate']))
	{
		$sqlmsg .= " and FinanceToDate >= '".$in['begindate']."' ";
		$pagearr['begindate'] = $in['begindate'];
	}
	if(!empty($in['enddate']))
	{
		$sqlmsg .= " and FinanceToDate <= '".$in['enddate']."' ";
		$pagearr['enddate'] = $in['enddate'];
	}
	$sqlnum = "SELECT count(*) AS allrow FROM ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid." ".$sqlmsg." ";
	$datasql   = "SELECT * FROM ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid." ".$sqlmsg." Order by FinanceID Desc";

	$InfoDataNum = $db->get_row($sqlnum);
	$page        = new ShowPage;
    $page->PageSize = 50;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = $pagearr;        
	
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".$sdbname.DATATABLE."_order_client where ClientCompany=".$cid." and ClientFlag=0 order by ClientCompanyPinyi asc");
		foreach($clientdata as $areavar)
		{
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
		}

		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['FinanceID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td >&nbsp;<span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['FinanceID'];?>" value="<? echo $lsv['FinanceID'];?>" /></span>&nbsp;<? echo $n++;?></td>
				  <td height="28" >
					<span ><? echo $lsv['FinanceID'];?></span>
				  </td>
				  <td><? echo $lsv['FinanceOrder'];?></td>
                  <td >		
						<?php echo $clientarr[$lsv['FinanceClient']];?>
				  </td>
                  <td >
				  <?php 
				  echo "<span title='金额' class=font12>¥ ".$lsv['FinanceTotal']."</span> ";
				  ?>
					</td>
				  <td><?php echo $lsv['FinanceToDate'];?></td>
                  <td >
					<? echo $finance_arr[$lsv['FinanceFlag']];?>			
				  </td>
				  <td>[<a href="javascript:void(0)" onclick="delete_data('<?php echo $lsv['FinanceID'];?>');">删除</a>]</td>
                </tr>
	<? } }else{?>
     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
	<? }?>
 				</tbody>                
              </table>
			  </div>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%"   height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="delete_mul_data();" >删除</a></li>
				   </ul></td>
				   <td  align="right"><? echo $page->ShowLink('show_finance_data.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

</body>
</html>