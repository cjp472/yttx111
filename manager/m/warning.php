<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}
if(empty($in['ty'])) $in['ty'] = 'down';

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');
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
<style type="text/css">
<!--
.tc thead tr td{font-weight:bold; background: #efefef; height:20px; padding:2px;}
.tc tbody tr td{ background: #ffffff;  height:20px; padding:0 2px;  overflow:hidden; text-align:left; width:50px;}
.tc tbody tr td input{width:95%; border:0;}
-->
</style>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="warning.php" >
        	    <label>
        	     &nbsp;&nbsp;名称/型号/拼音码： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label> &nbsp;&nbsp;
				<label>
				<select name="ty" id="ty">
						<option value="down" <? if($in['ty']=='down') echo 'selected="selected"';?> >⊙ 库存下限</option>
						<option value="up" <? if($in['ty']=='up') echo 'selected="selected"';?> >⊙ 库存上限</option>
					  </select>
				</label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="warning.php">库存预警</a> &#8250;&#8250; <a href="product.php"><? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        <div id="sortleft">
<!-- tree --> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value="多属性库存预警" class="bluebtn" onclick="javascript:window.location.href='warning_ms.php'" /> </div> 
<hr style="clear:both;" />
<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="warning.php">所有商品</a></strong></div>  	  
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
		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			<input type="hidden" name="ty" id="ty" value="<?php echo $in['ty'];?>" />
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold">&nbsp;</td>
				  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="15%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">名称</td>
				  <td width="12%" class="bottomlinebold" >条码</td>
				  <td width="8%"align="right" class="bottomlinebold" >可用库存</td>
				  <td width="8%" align="right" class="bottomlinebold" >实际库存</td>
				  <td width="8%" align="right" class="bottomlinebold" >报警值</td>
				  <td width="6%" align="center" class="bottomlinebold" >单位</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?php
	$sqlmsg = '';
	if(!empty($in['sid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
	if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(i.Name,i.Coding,i.Barcode,i.Pinyi) like '%".$in['kw']."%' ";
	if($in['ty'] == 'up'){
		$sqlmsg .= " and n.ContentNumber > i.LibraryUp and i.LibraryUp > 0 "; 
		$fieldsql = " i.LibraryDown,i.LibraryUp as Library, ";
	}else{
		$sqlmsg .= " and n.ContentNumber < i.LibraryDown ";
		$fieldsql = " i.LibraryDown as Library,i.LibraryUp, ";
	}
	//yangmm 2017-11-28 代理商只能看到自己商品的信息
	$userid=$_SESSION['uinfo']['userid'];
	$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
	if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
	if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";		
	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID inner join ".DATATABLE."_order_number n ON n.ContentID=i.ID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']."  and i.FlagID=0 ".$sqlmsg." ");

	$datasql   = "SELECT i.ID,i.Name,i.Coding,i.Barcode,i.Units,".$fieldsql." n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID inner join ".DATATABLE."_order_number n ON n.ContentID=i.ID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and i.FlagID=0 ".$sqlmsg." ORDER BY i.OrderID DESC, i.ID DESC";

	$page = new ShowPage;
    $page->PageSize = 100;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw'],"sid"=>$in['sid'],"ty"=>$in['ty']);
	$list_data = $db->get_results($datasql." ".$page->OffSet());

	$n=1;
	if(!empty($list_data))
	{
		$n = 1;
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
             <tr  class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ID'];?>" value="<? echo $lsv['ID'];?>" /></td>
				<td ><? echo $n++;?></td>
                <td ><? echo $lsv['Coding'];?></td>
				<td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
				<td ><? echo $lsv['Barcode'];?></td>				
				<td align="right">	<? echo $lsv['OrderNumber'];?></td>
				<td align="right">	<? echo $lsv['ContentNumber'];?></td>
				<td align="right">	<? echo $lsv['Library'];?></td>
				<td align="center">	<? echo $lsv['Units'];?></td>
             </tr>
	<?php
		}
	}else{ 
	?>
     			<tr>
       				<td colspan="8" height="30" align="center">此分类暂无符合条件的商品，请选择下级分类或其他分类!</td>
       			</tr>
<?php }?>
 				</tbody>                
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%" height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td width="20%" class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_warning_excel();" >批量导出</a></li></ul></td>
				   <td align="right"><? echo $page->ShowLink('warning.php');?></td>
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
				$frontMsg  .= '<li><a href="warning.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
			}else{
				$frontMsg  .= '<li><a href="warning.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
			}
			$frontMsg2  = "";
			$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID']);
			if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
			$frontMsg .= '</li>';
		}
	}		
	return $frontMsg;
}
?>