<?php 
$menu_flag = "inventory";
$pope	       = "pope_view";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}

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
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    
    

    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">

			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="inventory.php">
        		<tr>
					<td width="120" align="center"><strong>名称/型号/拼音码：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="inventory.php">库存状况 &#8250;&#8250; <? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        <div id="sortleft">
<!-- tree --> 

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong>商品分类</strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
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
                  <td width="16%" class="bottomlinebold">编号/货号</td>
				  <td width="14%" class="bottomlinebold" >包装</td>				  
                  <td width="10%" class="bottomlinebold" align="right">可用库存</td>
                  <td width="10%" class="bottomlinebold" align="right">实际库存</td> 
				  <td width="6%" class="bottomlinebold" align="center">单位</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	$sqlmsg2 = '';
		if(!empty($in['sid']))
		{
			$sarray = $db->get_col("select SiteID from ".DATATABLE."_order_site where ParentID=".$in['sid']." and CompanyID=".$_SESSION['uinfo']['ucompany']." order by SiteID asc");
			if(!empty($sarray))
			{
				$sinid  = implode(",", $sarray);
				$sinid  = $in['sid'].",".$sinid;
				$sqlmsg  .= " and SiteID in ( ".$sinid." ) ";
				$sqlmsg2 .= " and i.SiteID in ( ".$sinid." ) ";
			}else{
				$sqlmsg  .= " and SiteID = ".$in['sid']." ";
				$sqlmsg2 .= " and i.SiteID = ".$in['sid']." ";
			}
		}

	if(!empty($in['kw']))  $sqlmsg .= " and (Name like binary '%%".$in['kw']."%%' or Coding like '%%".$in['kw']."%%' or Pinyi like '%%".strtoupper($in['kw'])."%%' ) ";
	if(!empty($in['kw']))  $sqlmsg2 .= " and (i.Name like binary '%%".$in['kw']."%%' or i.Coding like '%%".$in['kw']."%%' or i.Pinyi like '%%".strtoupper($in['kw'])."%%' ) ";

	//wangdan 2017-11-29 代理商的客情只能看到自己所代理商品的情况
	$userid=$_SESSION['uinfo']['userid'];
	$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
	if($type['UserType']=='S' && $type['UserFlag']==2)    
	{
		$sqlmsg .=" AND AgentID= ".$type['UpperID']." ";
		$sqlmsg2 .=" AND i.AgentID= ".$type['UpperID']." ";
	}	

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and FlagID=0 ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
	
	$datasql   = "SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Units,i.Casing,i.Color,i.Specification,n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and i.FlagID=0 ORDER BY i.OrderID DESC, i.ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
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
				  <td align="center"><? echo $lsv['Units'];?>&nbsp;</td>
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
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
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
    
<? include_once ("bottom.php");?>
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
<?
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
?>