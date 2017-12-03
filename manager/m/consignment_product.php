<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']     = date("Y-m-d");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

$urlmsg = "consignment_product.php?begindate=".$in['begindate']."&enddate=".$in['enddate']."";
if(!empty($in['cid'])) $urlmsg .= "&cid=".$in['cid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
        	<div class="leftdiv"></div>
            
			<div class="location"><strong>当前位置：</strong> <a href="consignment.php">发货单</a></div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div ><strong><a href="consignment_product.php?begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>">按药店统计</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="consignment_product.php?begindate=<? echo $in['begindate'];?>&enddate=<? echo $in['enddate'];?>" method="post">
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
          <form id="MainForm" name="MainForm" method="post" action="consignment_product_excel.php?action=product"  >
			<input name="clientid" id="clientid" type="hidden" value='<? if(!empty($in['cid'])) echo $in['cid'];?>' />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>待发货商品统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="35" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>" />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_consignment_product_data()"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_consignment_product_excel('product');" /></td>
     			 </tr>
			<?php
				$sdmsg = '';
				if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
				{
					echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
				}else{
					$sqll  = '';
					$sql2 = '';
					if(!empty($in['cid'])){
						$sqll  .= " and o.OrderUserID = ".$in['cid']." ";
						$sql2  .= " and o.ReturnClient = ".$in['cid']." ";
					}
					$sdmsg = " and ((o.OrderPayStatus < 2 and o.OrderPayType IN (4,5,6,8)) or o.OrderPayStatus >= 2)";

					//订购商品
					$statsql  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' ".$sdmsg." and o.OrderStatus!=0 and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ContentID order by cnum desc";
					$statdata = $db->get_results($statsql);
					
					//赠品
					$statsqlg  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' ".$sdmsg." and o.OrderStatus!=0 and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ContentID order by cnum desc";
					$statdatag = $db->get_results($statsqlg);

					$totals = 0;
					$totalm = 0;
					$totalq = 0;
					$gdata = null;
					if(!empty($statdatag))
					{
						foreach($statdatag as $rvar)
						{
							$gdata[$rvar['ContentID']] = $rvar['cnum'];
							$gsdata[$rvar['ContentID']] = $rvar['snum'];
							$gdataarr[$rvar['ContentID']] = $rvar;
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
						$pcoding = $db->get_results("SELECT i.ID,i.Coding,i.Units FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
						foreach($pcoding as $v)
						{
							if(empty($v['Coding'])) $v['Coding'] = '&nbsp;';
							$codingarr[$v['ID']]['Coding'] = $v['Coding'];
							$codingarr[$v['ID']]['Units']  = $v['Units'];
						}
				 ?>
     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="14%" class="bottomlinebold">编号</td>
				  <td class="bottomlinebold">商品名称</td>
                  <td align="right" width="10%" class="bottomlinebold">订购数</td>
				  <td align="right" width="10%" class="bottomlinebold">赠送数</td>
				  <td align="right" width="10%" class="bottomlinebold">已发数</td>
				  <td align="right" width="10%" class="bottomlinebold">待发数</td>
				  <td width="8%" class="bottomlinebold" align="center">单位</td>
                </tr>
     		   </thead>			 
			 <tbody>
			 <?php
			    $n = 1;
				foreach($statdata as $var)
				{
					if(empty($codingarr[$var['ContentID']])) continue;
					$var['onum'] = $var['cnum'];
					if(!empty($gdata[$var['ContentID']])) $var['gnum'] = $gdata[$var['ContentID']]; else $var['gnum'] = 0;
					if(!empty($gsdata[$var['ContentID']])) $var['gsnum'] = $gsdata[$var['ContentID']]; else $var['gsnum'] = 0;					
					$var['cnum'] = $var['onum'] + $var['gnum'] - $var['snum']- $var['gsnum'];

					$totalm = $totalm + $var['onum'];
					$totalg = $totalg + $var['gnum'];
					$totals = $totals + $var['snum'] + $var['gsnum'];
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <?php echo $codingarr[$var['ContentID']]['Coding'];?>
				  </td>
				  <td ><a href="show_consignment_product.php?<? echo 'pid='.$var['ContentID'].'&begindate='.$in['begindate'].'&enddate='.$in['enddate'];?>" target="_blank" ><? echo $var['ContentName'];?></a></td>
                  <td align="right"><? echo $var['onum'];?></td>
				  <td align="right"><? echo $var['gnum'];?></td>
                  <td align="right"><? echo $var['snum']+$var['gsnum'];?></td>
                  <td align="right"><strong><? echo $var['cnum'];?></strong></td>
				  <td align="center"><?php echo $codingarr[$var['ContentID']]['Units'];?></td>
			 </tr>
			 <?php 
					unset($gdataarr[$var['ContentID']]);
				}
			if(!empty($gdataarr))
			{
				foreach($gdataarr as $var)
				{
					if(empty($codingarr[$var['ContentID']])) continue;
					$totalg = $totalg + $var['cnum'];
					$totals = $totals + $var['snum'];
			?>
				<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <? echo $codingarr[$var['ContentID']]['Coding'];?>
				  </td>
				  <td ><a href="show_consignment_product.php?<? echo 'pid='.$var['ContentID'].'&begindate='.$in['begindate'].'&enddate='.$in['enddate'];?>" target="_blank" ><? echo $var['ContentName'];?></a></td>
                  <td align="right">0</td>
				  <td align="right"><? echo $var['cnum'];?></td>
                  <td align="right"><? echo $var['snum'];?></td>
                  <td align="right"><strong><? echo $var['cnum'] - $var['snum'];?></strong></td>
				  <td align="center"><?php echo $codingarr[$var['ContentID']]['Units'];?></td>
			 </tr>
			 <?php
				}
			}
				$total = $totalm + $totalg - $totals;
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				<td></td>
				  <td ><strong>合计：</strong></td>
				  <td ><strong>&nbsp;<? echo $n-1;?> </strong></td>
				  <td align="right"><strong> <? echo $totalm;?> </strong></td>
				  <td align="right"><strong> <? echo $totalg;?> </strong></td>
				  <td align="right"><strong> <? echo $totals;?> </strong></td>
                  <td align="right"><strong> <? echo $total;?> </strong></td>
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
              <INPUT TYPE="hidden" name="referer" value ="" />
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />

    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
     <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">待发货订单明细：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"></div>
	</div>
</body>
</html>
<?php
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