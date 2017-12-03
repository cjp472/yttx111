<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['quick'])) {
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate'])) $in['enddate']     = date("Y-m-d");
}else if($in['quick'] == 'month'){
	$BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));	
	$in['begindate'] =  date('Y-m-01', strtotime(date("Y-m-d")));
 	$in['enddate']     = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
}else if($in['quick'] == 'today'){
	$in['begindate']     = date("Y-m-d");
	$in['enddate']     = date("Y-m-d");
}

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

$urlmsg = "product_statistics.php?begindate=".$in['begindate']."&enddate=".$in['enddate']."";
if(!empty($in['cid'])) $urlmsg .= "&cid=".$in['cid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="js/statistics.js?v=<? echo VERID;?>" type="text/javascript"></script>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function() {
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});
</script>

<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>
            
			<div class="location"><strong>当前位置：</strong> <a href="product_statistics.php">商品统计</a></div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div ><strong><a href="product_statistics.php?begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>">按药店统计</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="product_statistics.php?begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>" method="post">
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;" >
				<option value="" >⊙ 所有药店</option>
				<?php 
					$n = 0;
					foreach($clientdata as $areavar)
					{
						$n++;
						if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
						$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];

						echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
					}
				?>
				</select>
				</form>
</ul>
 </div>
<?php //include_once ("inc/search_client.php");?>

<hr style="clear:both;" />

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="<? echo $urlmsg;?>">商品分类</a></strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php
		$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
		echo ShowTreeMenu($sortarr,0,$urlmsg);
	?>	
</ul>
 </div>
<!-- tree -->   
       	  </div>
        
		<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="statistics_product_excel.php?action=product"  >
			<input name="clientid" id="clientid" type="hidden" value='<? if(!empty($in['cid'])) echo $in['cid'];?>' />
			<input name="siteid" id="siteid" type="hidden" value='<? if(!empty($in['sid'])) echo $in['sid'];?>' />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>商品订购统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="35" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
       				 <input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;
       				 <input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>" />&nbsp;      			
       				 <input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_product_stat_data('between')"/>&nbsp;&nbsp;
       				 <input type="button" name="newbutton" id="btnToday" value=" 今天 " class="mainbtn" onclick="show_product_stat_data_quick('today')"/>&nbsp;&nbsp;
       				 <input type="button" name="newbutton" id="btnWeek" style="display:none" value=" 本周 " class="mainbtn" onclick="show_product_stat_data_quick('week')"/>
       				 <input type="button" name="newbutton" id="btnMonth" value=" 本月 " class="mainbtn" onclick="show_product_stat_data_quick('month')"/>&nbsp;&nbsp;
       				 <input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_product_excel('product');" /></td>
     				 <input type="hidden" value="" name="quick" id="quick" />
     			 </tr>
			<?php 
				if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
				{
					echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
				}else{
					$sqll  = '';
					$sql2 = '';
					if(!empty($in['cid'])){
						$sqll  .= " and c.ClientID = ".$in['cid']." ";
						$sql2  .= " and o.ReturnClient = ".$in['cid']." ";
					}
					//订购商品
					$statsql  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,sum(ContentPrice*ContentNumber*ContentPercent/10) as ctotal,c.ContentID,c.ContentName from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentID order by cnum desc";
					$statdata = $db->get_results($statsql);
					
					//赠品
					$statsqlg  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentID order by cnum desc";
					$statdatag = $db->get_results($statsqlg);

					//退货
					$statsqlr  = "SELECT sum(ContentNumber) as cnum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sql2." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ContentID order by cnum desc";
					$rdata = $db->get_results($statsqlr);

					$totalr = $totalc = 0;
					$totalm = 0;
					$totalq = 0;
					$gdata = null;
					if(!empty($statdatag))
					{
						foreach($statdatag as $rvar)
						{
							$gdata[$rvar['ContentID']] = $rvar['cnum'];
							$gdatas[$rvar['ContentID']] = $rvar['snum'];
							$gdataarr[$rvar['ContentID']] = $rvar;
						}
					}
					$returndata = null;
					if(!empty($rdata))
					{
						foreach($rdata as $rvar)
						{
							$returndata[$rvar['ContentID']]  = $rvar['cnum'];							
						}
					}

					if(!empty($statdata))
					{
						$sqlmsg = '';
						if(!empty($in['sid']))
						{
							$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
							if(!empty($in['sid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
						}						
						$pcoding = $db->get_results("SELECT i.ID,i.Coding FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
						foreach($pcoding as $v)
						{
							if(empty($v['Coding'])) $v['Coding'] = '&nbsp;';
							$codingarr[$v['ID']] = $v['Coding'];
						}
				 ?>
     			 <tr>
       				 <td height="28" bgcolor="#efefef" ><? if(!empty($in['cid'])) echo " &nbsp;&nbsp;&nbsp;&nbsp; <strong>药店:  ".$clientarr[$in['cid']]." - 所订商品</strong>";?> </td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">行号</td>
				  <td width="12%" class="bottomlinebold">编号</td>
				  <td class="bottomlinebold">商品名称</td>
                  <td width="8%" class="bottomlinebold" align="right">订购数</td>
                  <td width="12%" class="bottomlinebold" align="right">订购金额</td>
				  <td width="8%" class="bottomlinebold" align="right">赠送数</td>
				  <td width="8%" class="bottomlinebold" align="right">退货数</td>
				  <td width="8%" class="bottomlinebold" align="right">实际数</td>
				  <td width="8%" class="bottomlinebold" align="right">发货数</td>
				  <td width="16%" class="bottomlinebold" align="right">属性统计</td>
                </tr>
     		   </thead>			 
			 <tbody>
			 <?php
			    $n = 1;
				foreach($statdata as $var)
				{
					if(empty($codingarr[$var['ContentID']])) continue;

					$var['onum'] = $var['cnum'];
					if(!empty($returndata[$var['ContentID']])) $var['rnum'] = $returndata[$var['ContentID']]; else $var['rnum'] = 0;
					if(!empty($gdata[$var['ContentID']])) $var['gnum'] = $gdata[$var['ContentID']]; else $var['gnum'] = 0;
					$var['cnum'] = $var['onum'] + $var['gnum'] - $var['rnum'];
					//发货数量
					if(!empty($gdatas[$var['ContentID']])) $var['gsnum'] = $gdatas[$var['ContentID']]; else $var['gsnum'] = 0;
					$var['snum'] = $var['snum'] + $var['gsnum'];

					$totalm = $totalm + $var['onum'];
					$totalg = $totalg + $var['gnum'];
					$totalr = $totalr + $var['rnum'];
					$totals = $totals + $var['snum'];
					$totalc = $totalc + $var['ctotal'];
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <?
					if(!empty($in['cid']))
					{
						echo $codingarr[$var['ContentID']];
					}else{
					?>
				  <a href="product_stat_client.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>" target="_blank" ><? echo $codingarr[$var['ContentID']];?></a>
				  <? }?>
				  </td>
				  <td >
                      <a href="product_stat_cs.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>&cid=<?php echo $in['cid']; ?>" target="_blank" ><? echo $var['ContentName'];?></a>
                  </td>
                  <td align="right"><? echo $var['onum'];?></td>
                  <td align="right" >¥ <? echo sprintf("%01.2f", round($var['ctotal'],2));?></td>
				  <td align="right"><? echo $var['gnum'];?></td>
                  <td align="right"><? echo $var['rnum'];?></td>
                  <td align="right"><? echo $var['cnum'];?></td>
                  <td align="right"><? echo $var['snum'];?></td>
				  <td align="right">
                      <a href="product_stat_client.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>" target="_blank" >药店</a>
                      &nbsp;|&nbsp;
                      <a href="product_stat_cs.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>&cid=<?php echo $in['cid']; ?>" target="_blank">属性</a>
                      <br/>
                      <a href="product_stat_color.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?><? if(!empty($in['cid'])) echo '&cid='.$in['cid'];?>" target="_blank">颜色</a>
                      &nbsp;|&nbsp;
                      <a href="product_stat_spec.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?><? if(!empty($in['cid'])) echo '&cid='.$in['cid'];?>" target="_blank">规格</a>
                  </td>
			 </tr>
			 <?php 
					unset($gdataarr[$var['ContentID']]);
				}
				
				foreach($gdataarr as $var)
				{
					if(empty($codingarr[$var['ContentID']])) continue;
					$totalg = $totalg + $var['cnum'];
			?>
				<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <? 
					if(!empty($in['cid']))
					{
						echo $codingarr[$var['ContentID']];
					}else{
					?>
				  <a href="product_stat_client.php?ID=<? echo $var['ContentID'];?>&begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>" target="_blank" ><? echo $codingarr[$var['ContentID']];?></a>
				  <? }?>
				  </td>
				  <td ><? echo $var['ContentName'];?></td>
                  <td align="right">0</td>
                  <td align="right">0</td>
				  <td align="right"><? echo $var['cnum'];?></td>
                  <td align="right">0</td>
                  <td align="right"><? echo $var['cnum'];?></td>
                  <td align="right"><? echo $var['snum'];?></td>
				  <td ></td>
			 </tr>
			 <?php
					$totals = $totals + $var['snum'];
				}
				$total = $totalm + $totalg - $totalr;
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td colspan="2"><strong>合计：</strong></td>
				  <td ><strong>&nbsp;<? echo $n-1;?> 种</strong></td>
				  <td align="right"><strong> <? echo $totalm;?> </strong></td>
				  <td align="right"><strong>¥  <? echo sprintf("%01.2f", round($totalc,2));?> </strong></td>
				  <td align="right"><strong> <? echo $totalg;?> </strong></td>
				  <td align="right"><strong> <? echo $totalr;?> </strong></td>
                  <td align="right"><strong> <? echo $total;?> </strong></td>
				  <td align="right"><strong> <? echo $totals;?> </strong></td>
				  <td>&nbsp;</td>
			 </tr>
			 </tbody>
			</table>

					 </td>
     			 </tr>
				 <? }else{?>
     			 <tr>
       				 <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
     			 </tr>
				<? }}?>
              </table>
		    </fieldset>  
			 </div>
            <br style="clear:both;" />
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">订购明细：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContentList" >
        数据载入中...       
        </div>
	</div>
</body>
</html>
<?
 	function ShowTreeMenu($resultdata,$p_id,$umsg) 
	{
		$frontMsg  = "";
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				if($var['ParentID']=="0")
				{
					$frontMsg  .= '<li><a href="'.$umsg.'&sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}else{
					$frontMsg  .= '<li><a href="'.$umsg.'&sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
				}
				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$umsg);
				if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
				$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>