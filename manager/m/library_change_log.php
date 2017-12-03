<?php 
$menu_flag = "inventory";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="library_change_log.php">
        	    <label>
        	     &nbsp;&nbsp;名称/型号/拼音码： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="library.php">调整库存</a> &#8250;&#8250; <? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        <div id="sortleft">
<!-- tree --> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value="库存调整" class="redbtn" onclick="javascript:window.location.href='library.php'" /> </div> 
<hr style="clear:both;" />
<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="library_change_log.php">所有商品</a></strong></div>  	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="分类"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
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
		<div class="bottomlinebold" align="left"><strong>&nbsp;库存调整记录</strong></div>
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">&nbsp;行号</td>
				  <td width="12%" class="bottomlinebold">&nbsp;编号/货号</td>
                  <td  class="bottomlinebold">&nbsp;商品名称</td> 
				  <td width="10%" class="bottomlinebold" >&nbsp;调整前库存</td>				  
				  <td width="10%" class="bottomlinebold" >&nbsp;调整后库存</td>
				  <td width="12%" class="bottomlinebold" >&nbsp;操作员</td>				  
                  <td width="16%" class="bottomlinebold" >&nbsp;时间</td>
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
						$sinid  = implode(",", $sarray);
						$sinid  = $in['sid'].",".$sinid;
						$sqlmsg  .= " and i.SiteID in ( ".$sinid." ) ";
					}else{
						$sqlmsg  .= " and i.SiteID = ".$in['sid']." ";
					}
				}
				if(!empty($in['kw']))  $sqlmsg .= " and (i.Name like binary '%%".$in['kw']."%%' or i.Coding like '%%".$in['kw']."%%' or i.Pinyi like '%%".strtoupper($in['kw'])."%%' ) ";

				$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_library_change_log c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
				$page = new ShowPage;
				$page->PageSize = 30;
				$page->Total   = $InfoDataNum['allrow'];
				$page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
				
				$datasql   = "SELECT c.*,i.Name,i.Coding FROM ".DATATABLE."_order_library_change_log c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY c.ChangeID DESC";
				$list_data = $db->get_results($datasql." ".$page->OffSet());
				$n=1;
				if(!empty($list_data))
				{
					if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
					foreach($list_data as $lsv)
					{
			?>
                <tr id="line_<? echo $lsv['ChangeID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td >&nbsp;<? echo $n++;?></td>
				  <td >&nbsp;<? echo $lsv['Coding'];?></td>
                  <td >&nbsp;<a href="product_content.php?ID=<? echo $lsv['ContentID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" ><? echo $lsv['Name'];?></a></td>
                  <td >&nbsp;<? echo $lsv['ChangeOld'];?></td>
				  <td >&nbsp;<? echo $lsv['ChangeNew'];?></td>
				  <td ><? echo $lsv['ChangeUser'];?>&nbsp;</td>
                  <td ><? echo date("Y-m-d H:i",$lsv['ChangeDate']);?></td>                 
				  
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无数据!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td height="30" align="right"><? echo $page->ShowLink('library_change_log.php');?></td>
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
					$frontMsg  .= '<li><a href="library_change_log.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="library_change_log.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
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