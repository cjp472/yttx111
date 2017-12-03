<?php 
$menu_flag = "inventory";
$pope	       = "pope_audit";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="library.php">
        	    <label>
        	     &nbsp;&nbsp;名称/型号/拼音码： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="library.php">调整库存</a> &#8250;&#8250; <a href="product.php"><? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
        <div id="sortleft">
<!-- tree --> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value="库存调整记录" class="bluebtn" onclick="javascript:window.location.href='library_change_log.php'" /> </div> 
<hr style="clear:both;" />
<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong><a href="library.php">所有商品</a></strong></div>  	  
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
		<!--
		<div class="warning">
				小提示：调整库存后，可用库存将会和实际库存一致。在调整库存之前先处理含有该商品未发货的订单。
		</div>
		-->
		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >	
		<!--
		<div class="bottomlinebold" align="right"><input type="button" name="savbutton" id="savbutton" value="保 存" class="button_1" onclick="save_library_input_mul_number()" /></div> -->
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td width="32%" class="bottomlinebold">名称</td>
				  <td class="bottomlinebold" >规格/颜色(库存)</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['sid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
	if(!empty($in['kw']))  $sqlmsg .= " and (i.Name like '%".$in['kw']."%' OR CONCAT(i.Pinyi, i.Coding, i.Barcode) like '%".$in['kw']."%') ";

	//yangmm 2017-11-28 代理商只能看到自己商品的信息
	$userid=$_SESSION['uinfo']['userid'];
	$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
	if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
	if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";	
	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']."  and i.FlagID=0 ".$sqlmsg." ");

	$datasql   = "SELECT i.ID,i.SiteID,s.SiteName,i.OrderID,i.CommendID,i.Name,i.Coding,i.Price1,i.Price2,i.Units,i.Color,i.Specification FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and i.FlagID=0  ".$sqlmsg." ORDER BY i.OrderID DESC, i.ID DESC";
	
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);
	$list_data = $db->get_results($datasql." ".$page->OffSet());

	$n=1;
	if(!empty($list_data))
	{
		foreach($list_data as $var)
		{			
			$idarr[] = $var['ID'];
		}
		$idmsg = implode(",", $idarr);
		$sqllv = "SELECT ContentID,ContentColor,ContentSpec,ContentNumber FROM ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$idmsg.") order by ContentID DESC";
		$numdata = $db->get_results($sqllv);
		$snarr  = null;
		if(!empty($numdata))
		{
			foreach($numdata as $nvar)
			{
				$snarr[$nvar['ContentID'].'_'.$nvar['ContentSpec'].'_'.$nvar['ContentColor']] = $nvar['ContentNumber'];
			}
		}

		$sqlall = "SELECT ContentID,ContentNumber FROM ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$idmsg.") order by ContentID DESC";
		$numalldata = $db->get_results($sqlall);
		$snallarr  = null;
		if(!empty($numalldata))
		{
			foreach($numalldata as $nallvar)
			{
				$snallarr[$nallvar['ContentID']] = $nallvar['ContentNumber'];
			}
		}
		$n = 1;
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
             <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                <td valign="top"><? echo $n++;?></td>
                <td valign="top">
				编号：<? echo $lsv['Coding'];?>  &nbsp;&nbsp;&nbsp;&nbsp;(单位：<? echo $lsv['Units'];?>)<br />
				<a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
				<td >					
				<?php 
					if(empty($lsv['Color']) && empty($lsv['Specification']))
					{ 
						if(empty($snallarr[$lsv['ID']])) $snallarr[$lsv['ID']] = 0;
 				?>
				<input name="storage_number[]" onfocus="this.select();" onblur="change_lib_num('<? echo $lsv['ID'];?>',<? echo $snallarr[$lsv['ID']];?>)" type="text" id="<? echo $lsv['ID'];?>" size="6" value="<? echo $snallarr[$lsv['ID']];?>" /><input name="storage_number_id[]" type="hidden" value="<? echo $lsv['ID'];?>" />
				<? }else{ ?>
				<table  border="0" cellpadding="1" cellspacing="1" bgcolor="#eeeeee" class="tc">               
				<tbody>
				<tr>
				  <td width="50" align="center" title="库存" style="background-color:#eed97c;"><strong id="total_<? echo $lsv['ID'];?>"><? echo $snallarr[$lsv['ID']];?></strong></td>
				  <?
					$tdmsg = '';
					$carr     = null;
					if(empty($lsv['Color']))
					{
						$tdmsg = '<td style="background-color:#efefef;"><strong>&nbsp;</strong></td>';
						$tdnumber = 1;
						$carr[]   = str_replace($fp,$rp,base64_encode("统一")); 
					}else{
						if(strpos($lsv['Color'], ","))
						{
							$in_color_arr = explode(",", $lsv['Color']);
							foreach($in_color_arr as $cvar)
							{
								$tdmsg  .=  '<td style="background-color:#efefef;">'.$cvar.'</td>';
								$carr[]  = str_replace($fp,$rp,base64_encode($cvar));
							}
							$tdnumber = count($in_color_arr);
						}else{
							$tdmsg = '<td style="background-color:#efefef;">'.$lsv['Color'].'</td>';
							$tdnumber = 1;
							$carr[]   = str_replace($fp,$rp,base64_encode($lsv['Color'])); 
						}						
					}
					echo $tdmsg;
				  ?>
                </tr>
				  <?
					$trmsg = '';
					$slinet = 0;
					$llinet = null;
					if(empty($lsv['Specification']))
					{
						$trmsg .= '<tr><td style="background-color:#efefef;"><strong>&nbsp;</strong></td>';
						$basecode = str_replace($fp,$rp,base64_encode("统一"));

						for($i=0;$i<$tdnumber;$i++)
						{
							$akey = $lsv['ID'].'_'.$basecode.'_'.$carr[$i];
							$jskey = str_replace("|","DHK",$akey);
							if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
							$slinet = $slinet + $sv;
							$llinet[$i] = $llinet[$i] + $sv;
							$trmsg .= '<td ><input name="storage_number[]" onfocus="this.select();" onblur="change_lib_num(\''.$jskey.'\','.$sv.')" type="text" id="'.$jskey.'" size="5" value="'.$sv.'" /> <input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
						}

					}else{
						if(strpos($lsv['Specification'], ","))
						{
							$in_spec_arr = explode(",", $lsv['Specification']);
							foreach($in_spec_arr as $svar)
							{
								$trmsg .= '<tr><td style="background-color:#efefef;">'.$svar.'</td>';
								$basecode = str_replace($fp,$rp, base64_encode($svar));
								$slinet = 0;
								for($i=0;$i<$tdnumber;$i++)
								{
									$akey = $lsv['ID'].'_'.$basecode.'_'.$carr[$i];
									$jskey = str_replace("|","DHK",$akey);
									if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
									$slinet = $slinet + $sv;
									$llinet[$i] = $llinet[$i] + $sv;
									$trmsg .= '<td ><input name="storage_number[]" onfocus="this.select();" onblur="change_lib_num(\''.$jskey.'\','.$sv.')" type="text" id="'.$jskey.'" size="5" value="'.$sv.'" /> <input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
								}
							}
						}else{
							$trmsg .= '<tr><td style="background-color:#efefef;">'.$lsv['Specification'].'</td>';
							$basecode = str_replace($fp,$rp,base64_encode($lsv['Specification']));
							for($i=0;$i<$tdnumber;$i++)
							{
								$akey = $lsv['ID'].'_'.$basecode.'_'.$carr[$i];
								$jskey = str_replace("|","DHK",$akey);
								if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
								$slinet = $slinet + $sv;
								$llinet[$i] = $llinet[$i] + $sv;
								$trmsg .= '<td ><input name="storage_number[]" onfocus="this.select();" onblur="change_lib_num(\''.$jskey.'\','.$sv.')" type="text" id="'.$jskey.'" size="5" value="'.$sv.'" /> <input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
							}
						}
					}
					echo $trmsg;
				  ?>
				   </tbody>
              </table>
			  <? }?>

				  </td> 
               </tr>
<? } }else{ ?>
     			<tr>
       				<td colspan="8" height="30" align="center">此分类暂无符合条件的商品，请选择下级分类或其他分类!</td>
       			</tr>
<? }?>
 				</tbody>                
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <!--
				 <tr>
       			     <td height="30" align="right"><input type="button" name="savbutton2" id="savbutton2" value="保 存" class="button_1" onclick="save_library_input_mul_number()" /></td>
     			 </tr>
				 -->
     			 <tr>
       			     <td height="30" align="right"><? echo $page->ShowLink('library.php');?></td>
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
					$frontMsg  .= '<li><a href="library.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="library.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
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