<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
if(empty($in['selectid'])) $in['selectid'] = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
a{text-decoration:none; color:#33a676; }

td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
.redbtn {
     background-color:#ff9f36;  color: #FFF;  border:#ff9f36 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}


.bluebtn {
   background-color:#47c4d7;  color: #FFF;  border:#47c4d7 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}

-->
</style>
</head>

<body>
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
          <tr>
            <form id="forms" name="forms" method="post" action=""><td width="7%" nowrap="nowrap"><strong>&nbsp;快速查询：</strong></td>
            <td width="17%" height="24" nowrap="nowrap">
              <label>
                <input type="text" name="kw" id="kw" size="20" value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();" />
              </label>           
            </td>
            <td width="30%" nowrap="nowrap">            
				 <select name="sid" id="sid" style="width:200px;" class="select2" >
                    <option value="">⊙ 所有商品分类</option>
                    <? 
					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					?>
                  </select>                  
            </td>
            <td width="46%"><label>
              <input name="button3" type="submit" class="bluebtn" id="button3" value="搜索" />
            </label></td> 
            </form>
          </tr>
        </table>
	<div style="width:100%; height:360px; overflow:auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
			  <input type="hidden" name="selectid" id="selectid" value="<? if(!empty($in['selectid'])) echo $in['selectid'];?>" />
        	  <table width="96%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
                  <td width="5%" bgcolor="#efefef" align="center" title="全选/取消"><input type="checkbox" name="chkall" id="chkall" value="" onclick="CheckAll(this.form);" /></td>
                  <td bgcolor="#efefef" >名称</td>
                  <td width="18%" bgcolor="#efefef" >编号/货号</td>				  
                  <td width="10%" align="right" bgcolor="#efefef" >可用库存</td>
                  <td width="10%" align="right" bgcolor="#efefef" >实际库存</td> 
				  <td width="8%" align="center" bgcolor="#efefef" >单位</td>
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

	if(!empty($in['kw']))  $sqlmsg .= " and (ID=".intval($in['kw'])." or Name like binary '%%".$in['kw']."%%' or Coding like '%%".$in['kw']."%%' or Pinyi like '%%".strtoupper($in['kw'])."%%' ) ";
	if(!empty($in['kw']))  $sqlmsg2 .= " and (i.ID=".intval($in['kw'])." or i.Name like binary '%%".$in['kw']."%%' or i.Coding like '%%".$in['kw']."%%' or i.Pinyi like '%%".strtoupper($in['kw'])."%%' ) ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and FlagID=0 ");
	$page = new ShowPage;
    $page->PageSize = 12;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw'],"sid"=>$in['sid'],"selected"=>$in['selectid']);        
	
	$datasql   = "SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Units,i.Casing,n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and i.FlagID=0 ORDER BY i.OrderID DESC, i.ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
     foreach($list_data as $lsv)
	 {
		 $lsv['Name'] = str_replace('"',"“",$lsv['Name']);
?>
                <tr id="line_<? echo $lsv['ID'];?>"  >
                   <td height="22" align="center" bgcolor="#FFFFFF" title="<? echo $lsv['ID'];?>"><input type="checkbox" name="selectedPID[]" id="selectp_<? echo $lsv['ID'];?>" value="<? echo $lsv['ID'];?>" /></td>
                  <td bgcolor="#FFFFFF" ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
                  <td bgcolor="#FFFFFF" ><? echo $lsv['Coding'];?>&nbsp;</td>           
                  
                  <td align="right" bgcolor="#FFFFFF"><? if(empty($lsv['OrderNumber'])) echo "0"; else echo $lsv['OrderNumber'];?></td>
                  <td align="right" bgcolor="#FFFFFF" ><? if(empty($lsv['ContentNumber'])) echo "0"; else echo $lsv['ContentNumber'];?>&nbsp;</td>
				  <td align="center" bgcolor="#FFFFFF"><? echo $lsv['Units'];?>&nbsp;</td>
              </tr>
<? } }else{?>
     		  <tr>
       				 <td height="30" colspan="8" align="center" bgcolor="#FFFFFF">无符合条件的商品!</td>
   			  </tr>
<? }?>
 				</tbody>                
              </table>


              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
                 	 <td height="35"><label>
                 	   <input type="button" name="button" id="button" value="选中" class="redbtn" onclick="add_select_product();" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value="取消" class="bluebtn"  onclick="parent.closewindowui()" />
               	     </label></td>
       			     <td  align="right"><? echo $page->ShowLink('select_storage_product.php');?></td>
     			 </tr>
          </table>
        <link rel="stylesheet" href="css/select2.css" type="text/css" />
    <script src="../scripts/select2.min.js" type="text/javascript"></script>
	<script src="../scripts/select2_locale_zh-CN.js" type="text/javascript"></script>
    <script>
        $(function(){
            if($(".select2").length >0){
                $(".select2").select2();
            }
        });
    </script>   
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠";
		$selectmsg = "";
		
		if($var['ParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." > ".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>
