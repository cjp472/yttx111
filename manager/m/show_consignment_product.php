<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
if(empty($in['pid'])) exit('参数错误！');
if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']     = date("Y-m-d");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
	foreach($clientdata as $var)
	{
		$clientarr[$var['ClientID']] = $var['ClientCompanyName'];
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
</head>

<body>
       
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv"></div>
            
			<div class="location"><strong>当前位置：</strong> <a href="consignment.php">待发货明细</a></div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
          <form id="MainForm" name="MainForm" method="post" action=""  >
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>待发货商品明细</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">

			<?php 
				if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
				{
					echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
				}else{

					$sqll  .= " and c.ContentID=".$in['pid']." ";
					$pcoding = $db->get_row("SELECT ID,Coding,Units FROM ".DATATABLE."_order_content_index  where ID=".$in['pid']." and  CompanyID = ".$_SESSION['uinfo']['ucompany']." ");
					
					$sdmsg = " and ((o.OrderPayStatus < 2 and o.OrderPayType IN (4,5,6,8)) or o.OrderPayStatus >= 2)";
					
					//订购商品
					$statsql  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName,c.ClientID from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' ".$sdmsg." and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ClientID order by cnum desc";
					$statdata = $db->get_results($statsql);
					
					//赠品
					$statsqlg  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName,c.ClientID from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' ".$sdmsg." and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ClientID order by cnum desc";
					$statdatag = $db->get_results($statsqlg);

					$totals = 0;
					$totalm = 0;
					$totalq = 0;
					$gdata = null;
					if(!empty($statdatag))
					{
						foreach($statdatag as $rvar)
						{
							$gdata[$rvar['ClientID']] = $rvar['cnum'];
							$gsdata[$rvar['ClientID']] = $rvar['snum'];
							$gdataarr[$rvar['ClientID']] = $rvar;
						}
					}
				 ?>

     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="12%" class="bottomlinebold">编号</td>
				  <td class="bottomlinebold">商品名称</td>
                  <td align="right" width="8%" class="bottomlinebold">订购数</td>
				  <td align="right" width="8%" class="bottomlinebold">赠送数</td>
				  <td align="right" width="8%" class="bottomlinebold">已发数</td>
				  <td align="right" width="8%" class="bottomlinebold">待发数</td>
				  <td width="6%" class="bottomlinebold" align="center">单位</td>
				  <td width="24%" class="bottomlinebold" align="left">药店</td>
                </tr>
     		   </thead>			 
			 <tbody>
			 <?php
			if(!empty($statdata))
			{
			    $n = 1;
				foreach($statdata as $var)
				{
					$var['onum'] = $var['cnum'];
					if(!empty($gdata[$var['ClientID']])) $var['gnum'] = $gdata[$var['ClientID']]; else $var['gnum'] = 0;
					if(!empty($gsdata[$var['ClientID']])) $var['gsnum'] = $gsdata[$var['ClientID']]; else $var['gsnum'] = 0;					
					$var['cnum'] = $var['onum'] + $var['gnum'] - $var['snum']- $var['gsnum'];

					$totalm = $totalm + $var['onum'];
					$totalg = $totalg + $var['gnum'];
					$totals = $totals + $var['snum'] + $var['gsnum'];
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <?php echo $pcoding['Coding'];?>
				  </td>
				  <td ><a href="product_content.php?ID=<? echo $var['ContentID'];?>" target="_blank" ><? echo $var['ContentName'];?></a></td>
                  <td align="right"><? echo $var['onum'];?></td>
				  <td align="right"><? echo $var['gnum'];?></td>
                  <td align="right"><? echo $var['snum']+$var['gsnum'];?></td>
                  <td align="right"><strong><? echo $var['cnum'];?></strong></td>
				  <td align="center"><?php echo $pcoding['Units'];?></td>
				  <td align="left"><a href="client_content.php?ID=<?php echo $var['ClientID'];?>"><?php echo $clientarr[$var['ClientID']];?></a></td>  
			 </tr>
			 <?php 
					unset($gdataarr[$var['ClientID']]);
				}
			}
			if(!empty($gdataarr))
			{
				foreach($gdataarr as $var)
				{
					$totalg = $totalg + $var['cnum'];
					$totals = $totals + $var['snum'];
			?>
				<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><? echo $n++;?></td>
				  <td>
				  <? echo $pcoding['Coding'];?>
				  </td>
				  <td ><? echo $var['ContentName'];?></td>
                  <td align="right">0</td>
				  <td align="right"><? echo $var['cnum'];?></td>
                  <td align="right"><? echo $var['snum'];?></td>
                  <td align="right"><strong><? echo $var['cnum'] - $var['snum'];?></strong></td>
				  <td align="center"><?php echo $pcoding['Units'];?></td>
				  <td align="left"><a href="client_content.php?ID=<?php echo $var['ClientID'];?>"><?php echo $clientarr[$var['ClientID']];?></a></td>
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
				  <td>&nbsp;</td>
			 </tr>
			 </tbody>
			</table>

					 </td>
     			 </tr>
				<? }?>
              </table>
		    </fieldset>  
			 </div>
            <br style="clear:both;" />
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        </div>
        <br style="clear:both;" />
    </div>    

</body>
</html>