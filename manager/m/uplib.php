<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');

$cid = 523;

$arr = $idArr = array();
	$datasql   = "SELECT * FROM ".DATATABLE."_order_resource where CompanyID = ".$cid." ";
	$list_data = $db->get_results($datasql);

	foreach($list_data as $v){
		if(in_array($v['Name'],$arr)){
			echo $sql = "delete from ".DATATABLE."_order_resource where CompanyID = ".$cid." and Name='".$v['Name']."' limit 1; <br />";
		}
		$arr[] = $v['Name'];
	}


exit('over');

	$datasql   = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
	$list_data = $db->get_results($datasql);
	
	$datasql2   = "select ContentID,sum(OrderNumber) as num from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." group by ContentID "; 
	$list_data2 = $db->get_results($datasql2);


	foreach($list_data as $v){
		$numarr[$v['ContentID']] = $v['OrderNumber'];
	}

	foreach($list_data2 as $v){
		if($v['num'] != $numarr[$v['ContentID']]){
			echo $v['ContentID'].'子：'.$v['num'].'总：'.$numarr[$v['ContentID']].'<br />';

			if($numarr[$v['ContentID']] == ''){
				$db->query("insert into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$v['ContentID'].",0,0)");
			}else{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=(select sum(OrderNumber) from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v['ContentID']." ) where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v['ContentID']." limit 1");
			}
			
		}
	}

exit('over');

$darr = array(
		'LT'		=>  '<',
		'EQ'		=>  '=',
		'GT'		=>  '>',
		'KLT'		=>  '<',
		'KEQ'		=>  '=',
		'KGT'		=>  '>'
 	 );

$sarr = array(
		'goods'		=>  ' ORDER BY  i.OrderID DESC, i.ID DESC ',
		'asc'		=>  ' ORDER BY  n.ContentNumber asc ',
		'desc'		=>  ' ORDER BY  n.ContentNumber desc '
 	 );

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}


$sqlmsg = $orderby = '';
if(!empty($in['sid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
if(!empty($in['BrandID'])) $sqlmsg .= " and i.BrandID = ".intval($in['BrandID'])." ";

if(!empty($in['numw'])){
	$in['num'] = intval($in['num']);
	if(substr($in['numw'],0,1)=='K'){
		$sqlmsg .= " and n.OrderNumber ".$darr[$in['numw']]." ".$in['num']." ";
	}else{
		$sqlmsg .= " and n.ContentNumber ".$darr[$in['numw']]." ".$in['num']." ";
	}		
}else{
	$in['num'] = '';
}
if(!empty($in['kw']))
{
	$in['kw'] = str_replace(' ','%',$in['kw']);
	$sqlmsg .= " and (i.Name like '%".$in['kw']."%' OR CONCAT(i.Pinyi, i.Coding, i.Barcode) like '%".$in['kw']."%') ";
}
if(empty($in['sc'])) $in['sc'] = 'goods';
if(empty($in['sp'])) $in['sp'] = 'Price1';

$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript">

	$(function() {
		$("#tree").treeview({
			collapsed: true,
			animated: "medium",
			control:"#sidetreecontrol",
			persist: "location"
		});
	})

function changeprice(price)
{
    document.getElementById("sp").value = price;
	document.FormSearch.submit();
}
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="inventory.php">
				<input type="hidden" name="sp" id="sp" value="<?php echo $in['sp'];?>" />
        		<tr>
					<td width="150" align="center"><strong>名称/编号/条码：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<?php echo $in['kw'];?>" /></td>
					<td width="150"><select name="sid" id="set_sid" style="width:200px;" class="select2">
                    <option value="">⊙ 所有商品分类</option>
                    <?php 
					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowOptionMenu($sortarr,0,$in['sid'],1);
					?>
                  </select></td>
					
					<td width="120"><select name="BrandID" id="BrandID" class="select2" style="width:120px;margin-left:3px;">
						<option value="">⊙ 所有品牌</option>
						<?php
							$bsql   = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by BrandPinYin ASC";
							$bdata = $db->get_results($bsql);

							foreach($bdata as $v)
							{
								if($in['BrandID']==$v['BrandID']) $smsg = 'selected="selected"'; else $smsg = '';
								echo '<option value="'.$v['BrandID'].'" '.$smsg.'>'.substr($v['BrandPinYin'],0,1).' - '.$v['BrandName'].'</option>';
							}
						?>
					  </select></td>  
					<td width="220">
					<select name="numw" id="numw" style="width:125px;" class="select2" >
						<option value="">⊙按库存数量筛选</option>
						<optgroup label="- 实际库存 -">
						<option value="LT" <?php if($in['numw']=="LT") echo 'selected="selected" ';?> >小于</option>
						<option value="EQ" <?php if($in['numw']=="EQ") echo 'selected="selected" ';?> >等于</option>
						<option value="GT" <?php if($in['numw']=="GT") echo 'selected="selected" ';?> >大于</option>
						<optgroup label="- 可用库存 -">
						<option value="KLT" <?php if($in['numw']=="KLT") echo 'selected="selected" ';?> >小于</option>
						<option value="KEQ" <?php if($in['numw']=="KEQ") echo 'selected="selected" ';?> >等于</option>
						<option value="KGT" <?php if($in['numw']=="KGT") echo 'selected="selected" ';?> >大于</option>
					  </select>
					  <input type="text" title="库存数量" name="num" id="num" class="inputline" style="width:40px; padding:1px 4px;" value="<? echo $in['num'];?>" />
					  </td> 
					<td width="140">
					<select name="sc" id="sc" style="width:100px;">
						<option value="goods" <?php if($in['sc']=="goods") echo 'selected="selected" ';?> >默认商品顺序</option>
						<option value="desc" <?php if($in['sc']=="desc") echo 'selected="selected" ';?> >实际库存降级</option>
						<option value="asc" <?php if($in['sc']=="asc") echo 'selected="selected" ';?> >实际库存升级</option>
	 			    </select>
					</td>
					<td ><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        <div id="sortleft">
			<!-- tree --> 
			<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 库存明细 " class="button_2" onclick="javascript:window.location.href='inventory_list.php'" /></div>
			<hr style="clear:both;" />
			<div id="sidetree"> 
			<div class="treeheader">&nbsp;<strong>商品分类</strong></div>  	  
			<div id="sidetreecontrol"><img src="css/images/home.gif" alt="分类"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
			<ul id="tree">
				<?php 
					echo ShowTreeMenu($sortarr,0);
				?>	
			</ul>
			 </div>
			<!-- tree -->
       	  </div>

        <div id="sortright">

		<form id="MainForm" name="MainForm" method="post" action="inventory.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
				  <td width="4%" class="bottomlinebold">&nbsp;</td>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">名称</td>
                  <td width="14%" class="bottomlinebold">编号</td>
				  <td width="12%" class="bottomlinebold" >包装</td>				  
                  <td width="6%" class="bottomlinebold" align="right">可用库存</td>
                  <td width="8%" class="bottomlinebold" align="right">实际库存</td> 
                  <td width="10%" class="bottomlinebold" align="right">	

				  <select name="price" id="price" style="width:60px;" onchange="javascript:changeprice(this.options[this.selectedIndex].value)" >
						<option value="Price1" <?php if($in['sp']=="Price1") echo 'selected="selected" ';?> ><?php echo $price1_name; ?></option>
						<option value="Price2" <?php if($in['sp']=="Price2") echo 'selected="selected" ';?> ><?php echo $price2_name; ?></option>
	 			  </select>

				  </td> 
                  <td width="10%" class="bottomlinebold" align="right" title="实际库存金额">库存金额</td> 
				  <td width="5%" class="bottomlinebold" align="center">单位</td>
                </tr>
     		 </thead> 

      		<tbody>
<?php
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index i left JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and i.FlagID=0 ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid'],"sc"=>$in['sc'],"numw"=>$in['numw'],"num"=>$in['num'],"BrandID"=>$in['BrandID']);        
	$datasql   = "SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Price1,i.Price2,i.Units,i.Casing,i.Color,i.Specification,n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_site s ON i.SiteID=s.SiteID left join ".DATATABLE."_order_number n on i.ID=n.ContentID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']."  and i.FlagID=0 ".$sqlmsg." ".$sarr[$in['sc']];
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		$page_onumber = 0;
		$page_cnumber = 0;
		$page_total   = 0;
		 if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		 foreach($list_data as $lsv)
		 {
			$lsv['Price'] = $lsv[$in['sp']];
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ID'];?>" value="<? echo $lsv['ID'];?>" /></td>
				  <td ><? echo $n++;?></td>
                  <td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
                  <td ><? echo $lsv['Coding'];?>&nbsp;</td>
                  <td ><? echo $lsv['Casing'];?>&nbsp;</td>                  
                  <? if(empty($lsv['Color']) && empty($lsv['Specification'])){ ?>
                  <td align="right" class="bold" title="无子库存"><? if(empty($lsv['OrderNumber'])) echo "0"; else echo $lsv['OrderNumber'];?>&nbsp;</td>
                  <td align="right" class="bold" title="无子库存" ><? if(empty($lsv['ContentNumber'])) echo "0"; else echo $lsv['ContentNumber'];?>&nbsp;</td>
				  <? 
					}else{
						if(empty($lsv['OrderNumber']))
						{
							echo '<td align="right" class="bold" >0&nbsp;</td>';
						}else{
				  ?>
						<td align="right" class="TitleNUM3" title="点击查看库存详细" onclick="show_inventory('<? echo $lsv['ID'];?>','order');"><? echo $lsv['OrderNumber'];?>&nbsp;</td>
				  <? 
					 }
						if(empty($lsv['ContentNumber']))
						{
							echo '<td align="right" class="bold" >0&nbsp;</td>';
						}else{
				  ?>
						<td align="right" class="TitleNUM3" title="点击查看库存详细" onclick="show_inventory('<? echo $lsv['ID'];?>','content');"><? echo $lsv['ContentNumber'];?>&nbsp;</td>
				  <? }}?>
				  <td align="right"><? echo $lsv['Price'];?>&nbsp;</td>
				  <td align="right"><? echo $linetotal = $lsv['Price']*$lsv['ContentNumber'];?>&nbsp;</td>
				  <td align="center"><? echo $lsv['Units'];?>&nbsp;</td>
                </tr>
				<?php 
				$page_onumber = $page_onumber + $lsv['OrderNumber'];
				$page_cnumber = $page_cnumber + $lsv['ContentNumber'];
				$page_total   = $page_total + $linetotal;
				}
				 ?>
     			 <tr class="bottomline">
					 <td ></td><td ></td>
       				 <td colspan="3" height="30" class="font12h">本页小计：</td>
					 <td class="font12h" align="right"><?php echo $page_onumber;?></td>
					 <td class="font12h" align="right"><?php echo $page_cnumber;?></td>
					 <td ></td>
					 <td class="font12h" align="right"><?php echo $page_total;?></td>
					 <td ></td>
       			 </tr>
<?php
}else{
?>
     			 <tr>
       				 <td colspan="10" height="30" align="center">暂无符合条件的数量!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%" height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_inventory_excel();" >批量导出</a></li><li><a href="#" onclick="out_all_inventory_excel();" >全部导出</a></li></ul></td>
     			 </tr>
     			 <tr>
       			   <td height="30" ></td>
				   <td ></td>
   			       <td align="right"><? echo $page->ShowLink('inventory.php');?></td>
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
			<h3 id="windowtitle">商品库存详细</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>  
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				if($var['ParentID']=="0")
				{
					$frontMsg  .= '<li><a href="inventory.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="inventory.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}

 	function ShowOptionMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";
		
		if($p_id=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat(" -- ", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowOptionMenu($resultdata,$var['SiteID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
	
	function charblank($char)
	{
		if(strlen($char) > 5)
		{
			$rchar = substr($char,0,4);
		}else{
			$rchar = $char.str_repeat(" -", (4-strlen($char)));
		}
		return $rchar;
	}
?>