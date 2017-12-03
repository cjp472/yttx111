<?php 
$menu_flag = "order";
$pope	       = "pope_form";
include_once ("header.php");

if(empty($in['oid']))
{
	exit('参数错误!');
}else{
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." limit 0,1");

	if($oinfo['OrderStatus'] > 1) exit('错误地址!');
	$cinfo = $db->get_row("SELECT ClientID,ClientLevel,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientSetPrice FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$cinfo['ClientLevel'] = "A_".$cinfo['ClientLevel'];
		}
}

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
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
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="order_add_gifts_product.php">
				<input type="hidden" name="oid" id="oid" value="<? echo $in['oid'];?>" />
        	    <label>
        	     &nbsp;&nbsp;<strong>名称/型号：</strong> <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="#">订单管理</a> &#8250;&#8250; <a href="order_gifts_product.php?ID=<? echo $in['oid'];?>">订单赠品管理</a> &#8250;&#8250; <a href="#">添加赠品到订单</a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
<!-- tree --> 

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="order_add_gifts_product.php?oid=<? echo $in['oid'];?>">商品分类</a></strong></div>  	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="分类"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
		echo ShowTreeMenu($sortarr,0,$in['oid']);
	?>	
</ul>
 </div>
<!-- tree -->
       	  </div>

        <div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
				  <td width="8%" class="bottomlinebold">&nbsp;行号</td>
				  <td width="16%" class="bottomlinebold">&nbsp;编号/货号</td>
                  <td  class="bottomlinebold">名称</td>                  
				  <td width="14%" class="bottomlinebold" align="right">单价(元)</td>
                  <td width="12%" class="bottomlinebold" align="right">颜色&nbsp;&nbsp;</td>
				  <td width="12%" class="bottomlinebold" align="right">规格&nbsp;&nbsp;</td>                                    
                  <td width="12%" class="bottomlinebold" align="right">订购&nbsp;&nbsp;</td>
                </tr>
     		 </thead>       		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['sid']))
	{
		$sarray = $db->get_col("select SiteID from ".DATATABLE."_order_site where ParentID=".$in['sid']." and CompanyID=".$_SESSION['uinfo']['ucompany']." order by SiteID asc");
		if(!empty($sarray))
		{
			$sinid      = implode(",", $sarray);
			$sitesdata = $db->get_col("select SiteID from ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and ParentID IN (".$sinid.")");
			if(!empty($sitesdata))  $sinid = $sinid.",".implode(",",$sitesdata);
			$sinid  = $in['sid'].",".$sinid;
			$sqlmsg  .= " and SiteID in ( ".$sinid." ) ";
		}else{
			$sqlmsg  .= " and SiteID = ".$in['sid']." ";
		}
	}elseif(empty($in['kw'])){
		$sqlmsg .= ' and CommendID=8 ';
	}

	if(!empty($in['kw']))  $sqlmsg .= " and (Name like binary '%%".$in['kw']."%%' or Coding like binary '%%".$in['kw']."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  and FlagID=0 ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid'],"oid"=>$in['oid']);        
	
	$datasql   = "SELECT ID,SiteID,Name,Coding,Price1,Price2,Price3,Units,Casing,Color,Specification FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  and FlagID=0 ORDER BY OrderID DESC, ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td >&nbsp;<? echo $n++;?></td>	
				  <td >&nbsp;<? echo $lsv['Coding'];?></td>
                  <td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank"><? echo $lsv['Name'];?></a></td>                  
				  <td  class="TitleNUM">¥ <? 	echo $lsv[$cinfo['ClientSetPrice']]." / ".$lsv['Units'];  ?></td>
                  <td align="right">
				  <?
				  echo '<select name="color_'.$lsv['ID'].'" id="color_'.$lsv['ID'].'" style="width:50px;">';	
				  if(!empty($lsv['Color']))
					{
						$colorarr = @explode(",", $lsv['Color']);						
						foreach($colorarr as $cvar)
						{
							echo '<option value="'.$cvar.'">'.$cvar.'</option>';
						}
					}
					echo '</select>';
				  ?>&nbsp;
				  </td>
				  <td align="right">
				  <?
				  echo '<select name="spec_'.$lsv['ID'].'" id="spec_'.$lsv['ID'].'" style="width:50px;">';	
				  if(!empty($lsv['Specification']))
					{
						$specarr = @explode(",", $lsv['Specification']);
						
						foreach($specarr as $svar)
						{
							echo '<option value="'.$svar.'">'.$svar.'</option>';
						}					
					}
					echo '</select>';
				  ?>&nbsp;
				  </td>             
                  
                  <td align="center"><a onclick="javascript:addtocart_gifts('<? echo $lsv['ID'];?>','<? echo $oinfo ['OrderID'];?>','<? echo $cinfo['ClientID'];?>');" href="javascript:void(0);" title="包装：<? echo $lsv['Casing'];?>"><strong>&#8250; 添加到订单</strong></a></td>
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
       			     <td align="right"><? echo $page->ShowLink('order_add_gifts_product.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
				<div align="right"><input type="button" value="下一步，订单赠品管理" class="bluebtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='order_gifts_product.php?ID=<? echo $oinfo['OrderID'];?>'" /></div>
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
  <div id="tip" onclick="show_hide('hide','tip')">...</div>  
</body>
</html>
<?
 	function ShowTreeMenu($resultdata,$p_id,$oid) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				if($var['ParentID']=="0")
				{
					$frontMsg  .= '<li><a href="order_add_gifts_product.php?oid='.$oid.'&sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="order_add_gifts_product.php?oid='.$oid.'&sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$oid);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>