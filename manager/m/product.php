<?php 
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}
$productarr  = get_set_arr('product');

	$sqlmsg = $orderby = '';
	//yangmm 2017-11-28 代理商只能看到自己商品的信息
	$userid=$_SESSION['uinfo']['userid'];
	$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
	if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
	if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";
	
	if(!empty($in['sid'])) $sqlmsg .= " and SiteNO like '".$sortinfo['SiteNO']."%' ";
	if(!empty($in['BrandID'])) $sqlmsg .= " and BrandID = ".intval($in['BrandID'])." ";
	if(!empty($in['CommendID'])) $sqlmsg .= " and CommendID = ".intval($in['CommendID'])." ";
	if(!empty($in['kw']))
	{
		$kwn = str_replace(' ','%',$in['kw']);
		if(strpos($kwn,'%'))
		{
		    $temsql = array();
		    $kwnarr = explode('%',$kwn);
		    foreach($kwnarr as $v)
		    {
		        $temsql[] = " Name like '%".$v."%' ";
		    }
		    $sqlmsg  .= " AND ((".implode(" AND ",$temsql).") OR (Pinyi like '%".$kwn."%' OR Coding like '%".$kwn."%' OR Barcode like '%".$kwn."%')) ";
		}
		else
		{
		    $sqlmsg  .= " and (Name like '%".$kwn."%' OR Pinyi like '%".$kwn."%' OR Coding like '%".$kwn."%' OR Barcode like '%".$kwn."%' or Appnumber like '%".$kwn."%') ";
		}
	}
	if(empty($in['osc'])) $in['osc'] = 'DESC';
	if($in['osc'] != 'ASC' && $in['osc'] != 'DESC') exit('参数错误！');
	$orderbyarr = array('ID','OrderID','Coding','Price1','Price2');
	
	if(!empty($in['oby']))
	{
		if(!in_array($in['oby'],$orderbyarr)) exit('参数错误！');
		$orderby = ' '.$in['oby'].' '.$in['osc'].' ';
	}else{
		$orderby = ' OrderID DESC, ID DESC ';
	}

	$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '<font color=purple>[预]</font>',
		'2'			=>  '<font color=#ff6600>[特]</font>',
		'3'			=>  '<font color=#35ce00>[控]</font>',
		'4'			=>  '<font color=#ff0000>[热]</font>',
		'8'			=>  '<font color=#000000>[赠]</font>',
		'9'			=>  '<font color=#00b2fc>[缺]</font>'
 	 );

	setcookie("backurl", $_SERVER['REQUEST_URI']);
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
<script src="js/json2.js" type="text/javascript"></script>
<script src="js/product.js?v=ff<? echo VERID;?>" type="text/javascript"></script>

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
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="product.php">
				<input type="hidden" name="oby" id="oby" value="<?php if(!empty($in['oby'])) echo $in['oby'];?>" />
				<input type="hidden" name="osc" id="osc" value="<?php if(!empty($in['osc'])) echo $in['osc'];?>" />
        		<tr>
					<td width="150" align="center"><strong>名称/编号/拼音码/条码：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
					<td width="150"><select name="sid" id="set_sid" style="width:200px;" class="select2" >
                    <option value="">⊙ 所有商品分类</option>
                    <?php 
					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowOptionMenu($sortarr,0,$in['sid'],1);
					?>
                  </select></td>
					
					<td width="120"><select name="BrandID" id="BrandID" class="select2" style="margin-left:3px; width:120px;">
						<option value="">⊙ 所有品牌</option>
						<?php
							$bsql   = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by BrandPinYin ASC";
							$bdata = $db->get_results($bsql);

							foreach($bdata as $v)
							{
								if($in['brand']==$v['BrandID']) $smsg = 'selected="selected"'; else $smsg = '';
								echo '<option value="'.$v['BrandID'].'" '.$smsg.'>'.substr($v['BrandPinYin'],0,1).' - '.$v['BrandName'].'</option>';
							}
						?>
					  </select></td>  
					<td width="120"><select name="CommendID" id="CommendID" class="select2">
						<option value="">⊙ 所有属性</option>
						<?php
							$attarr = array(
							'1'		=> '预售',
							'2'		=> '特价',
							'3'		=> '控销 ',
							'4'		=> '热销',
							'8'		=> '赠品',
							'9'		=> '缺货',
							);

							foreach($attarr as $k=>$v)
							{
								if($in['CommendID']==$k) $smsg = 'selected="selected"'; else $smsg = '';
								echo '<option value="'.$k.'" '.$smsg.'>┠- '.$v.'</option>';
							}
						?>
					  </select></td>  
					<td width="60"><input type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td align="right"><div class="location"><strong>当前位置：</strong><a href="product.php">商品列表</a> &#8250;&#8250; <a href=""><? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft">
<!-- tree --> 
<div class="linebutton">
    <?php if(!erp_is_run($db,$_SESSION['uinfo']['ucompany'])) { ?>
    <input type="button" name="newbutton" id="newbutton" value=" 新增商品 " class="button_2" onclick="javascript:window.location.href='product_add.php'" />
    <?php }else { ?>
     	Erp用户请通过接口同步新增商品资料。
     <?php } ?>
</div>
<hr style="clear:both;" />

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="product.php">商品分类</a></strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		echo ShowTreeMenu($sortarr,0);
	?>	
</ul>
 </div>
<!-- tree -->
       	  </div>

        <div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" onsubmit="return false;" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold hand" title="按添加顺序"  onclick="set_orderby('ID','<?php if($in['oby']=="ID" && $in['osc']=="DESC") echo 'ASC'; else echo 'DESC';?>');" >&nbsp;&nbsp;<?php if($in['oby']=="ID") echo '<img src="img/'.$in['osc'].'.gif" alt="排序" />'; else echo '<img src="img/SC.gif" alt="排序" />';?> </td>
                  <td width="6%" class="bottomlinebold">行号 </td>
				  <td width="6%" class="bottomlinebold hand" title="按排序权重排序" onclick="set_orderby('OrderID','<?php if($in['oby']=="OrderID" && $in['osc']=="DESC") echo 'ASC'; else echo 'DESC';?>');">排序 <?php if($in['oby']=="OrderID") echo '<img src="img/'.$in['osc'].'.gif" alt="排序" />'; else echo '<img src="img/SC.gif" alt="排序" />';?></td>
				  <td width="12%" class="bottomlinebold hand" title="按商品编号排序" onclick="set_orderby('Coding','<?php if($in['oby']=="Coding" && $in['osc']=="DESC") echo 'ASC'; else echo 'DESC';?>');" >编号 <?php if($in['oby']=="Coding") echo '<img src="img/'.$in['osc'].'.gif" alt="排序" />'; else echo '<img src="img/SC.gif" alt="排序" />';?></td>
                  <td class="bottomlinebold">商品名称</td>
                    <td width="12%" class="bottomlinebold">药品规格</td>
                    <?php
                    $price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
                    $price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";
                    ?>
                  <td width="10%" class="bottomlinebold hand" align="right" title="按<?php echo $price1_name; ?>排序" onclick="set_orderby('Price1','<?php if($in['oby']=="Price1" && $in['osc']=="DESC") echo 'ASC'; else echo 'DESC';?>');" ><?php echo $price1_name; ?>(元) <?php if($in['oby']=="Price1") echo '<img src="img/'.$in['osc'].'.gif" alt="排序" />'; else echo '<img src="img/SC.gif" alt="排序" />';?></td>
                  <td width="12%" class="bottomlinebold hand" align="right" title="按<?php echo $price2_name; ?>排序" onclick="set_orderby('Price2','<?php if($in['oby']=="Price2" && $in['osc']=="DESC") echo 'ASC'; else echo 'DESC';?>');" ><?php echo $price2_name; ?>(元) <?php if($in['oby']=="Price2") echo '<img src="img/'.$in['osc'].'.gif" alt="排序" />'; else echo '<img src="img/SC.gif" alt="排序" />';?></td>
                  <td width="8%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?php

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0 ".$sqlmsg."  ");
	$datasql   = "SELECT ID,SiteID,SiteName,OrderID,CommendID,Name,Coding,Price1,Price2,Units,Model FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0  ".$sqlmsg." ORDER BY ".$orderby;

	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid'],"sid"=>$in['sid'],"BrandID"=>$in['BrandID'],"CommendID"=>$in['CommendID'],"oby"=>$in['oby'],"osc"=>$in['osc']);        
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ID'];?>" value="<? echo $lsv['ID'];?>"  /></td>
                  <td ><? echo $n++;?></td>
				  <td ><input type="text" class="numberinput" name="OrderID[]" id="order_<? echo $lsv['ID'];?>" value="<? echo $lsv['OrderID'];?>" onBlur="do_change_order('<? echo $lsv['OrderID'];?>','<? echo $lsv['ID']; ?>');" onfocus="this.select();" /></td>
                  <td ><? echo $lsv['Coding'];?>&nbsp;</td>
				  <td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" ><? echo $producttypearr[$lsv['CommendID']];?> <? echo $lsv['Name'];?></a></td>
                    <td><?php echo $lsv['Model']; ?>&nbsp;</td>
                  <td class="TitleNUM">¥ <? echo $lsv['Price1'];?> / <?=$lsv['Units']?></td>                  
                  <td class="TitleNUM">¥ <? echo $lsv['Price2']." / ".$lsv['Units'];?></td>
                  <td align="center"><a href="product_edit.php?ID=<? echo $lsv['ID'];?>" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;&nbsp;
                  <a href="javascript:void(0);" onclick="do_delete('<? echo $lsv['ID'];?>');" ><span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">此分类暂无商品，请选择下级分类或其他分类!</td>
       			 </tr>
<? }?>
 				</tbody>
                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" class="selectinput" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
				   <td width="12%" class="sublink"><ul><li><a href="javascript:void(0);" onclick="going('muledit','<? echo $in['sid'];?>')" >批量修改</a></li></ul></td>
   			       <td class="sublink">
				     <ul>
					   <li><a href="javascript:void(0);" onclick="going('del','<? echo $in['sid'];?>')" >批量下架</a></li>
					   <li><a href="javascript:void(0);" onclick="going('move','<? echo $in['sid'];?>')" >批量移动</a></li>
					   <li><a href="javascript:void(0);" onclick="going('outexcel','<? echo $in['sid'];?>')" >批量导出</a></li>
					   <li><a href="#" onclick="out_all_product_excel();" >全部导出</a></li>
					</ul>
				   </td>
     			 </tr>
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('product.php');?></td>
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
			<h3 id="windowtitle">导出全部商品数据</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>

<div id="windowForm_T" style="display:none;">
    <div id="windowContent_T">
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
					$frontMsg  .= '<li><a href="product.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="product.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
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
				$repeatMsg = str_repeat(" -+- ", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >┠-".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

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
