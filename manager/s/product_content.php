<?php 
$menu_flag = "";
$pope	   = "";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$productinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_content_1 c on i.ID=c.ContentIndexID where i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ID=".intval($in['ID'])." limit 0,1");
	$in['sid'] = $productinfo['SiteID'];
}
if(empty($productinfo['ID'])) exit('此商品不存在，或已经删除!');
$valuearrf = get_set_arr('field');
$pointarr  = get_set_arr('point');
$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>      
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
         <input type="hidden" name="update_id" id="update_id" value="<? echo $productinfo['ID'];?>" />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">商品管理</a> &#8250;&#8250; <a href="#">商品详细</a> </div>
   	        </div>            
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul></ul></div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset class="fieldsetstyle">
			<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">商品分类：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? 
					$sortarr = $db->get_row("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".$productinfo['SiteID']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo $sortarr['SiteName'];
					?></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">商品名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $productinfo['Name'];?></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">拼音码：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $productinfo['Pinyi'];?></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">编号/货号：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $productinfo['Coding']; ?>  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><?php echo $price1_name; ?>：</div></td>
                  <td bgcolor="#FFFFFF">¥ <? echo $productinfo['Price1']; ?>&nbsp;元/<? echo $productinfo['Units']; ?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><?php echo $price2_name; ?>：</div></td>
                  <td bgcolor="#FFFFFF">¥ <? echo $productinfo['Price2']; ?>&nbsp;元/<? echo $productinfo['Units']; ?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品包装：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $productinfo['Casing']; ?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0" height="60"><div align="right">商品属性：</div></td>
                  <td bgcolor="#FFFFFF">
				    <div><strong>可选规格：</strong>
			          <? echo $productinfo['Specification']; ?></div>
                   <div> <strong>可选颜色：</strong>
                    <? echo $productinfo['Color']; ?></div>
                  </td>
                  <td bgcolor="#FFFFFF">
                  <div></div></td>
                </tr>

<?

if(!empty($valuearrf))
{
	if(!empty($productinfo['FieldContent'])) $farr = unserialize($productinfo['FieldContent']);
	foreach($valuearrf as $k=>$v)
	{
?>
				<tr>
                  <td bgcolor="#F0F0F0" ><div align="right"><? echo $v['name'];?>：</div></td>
                  <td bgcolor="#FFFFFF" ><? if(!empty($farr[$k])) echo $farr[$k]; ?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<?
	}	
}
?>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品图片：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2">
				<?php				  
				$imginfo = $db->get_results("SELECT Name,Path FROM ".DATATABLE."_order_resource where IndexID=".$productinfo['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." order by OrderID asc");

				if(!empty($imginfo))
				{
					foreach($imginfo as $imgvar)
					{
						echo '<a href="'.RESOURCE_URL.$imgvar['Path'].'img_'.$imgvar['Name'].'" target="_blank"><img src="'.RESOURCE_URL.$imgvar['Path'].'img_'.$imgvar['Name'].'" style="max-width:800px" border=0 hspace="8" vspace="8"  /></a><br />';
					}
				}else{
				  if(!empty($productinfo['Picture'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$productinfo['Picture']).'" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$productinfo['Picture']).'" style="max-width:800px" border=0 hspace="8" vspace="8"  /></a>';
				}
				?>
				  </td>
                </tr>
              </table>
			</fieldset>

			<fieldset class="fieldsetstyle">
			<legend>详细描述</legend>
			<?php
				echo $stringContent = html_entity_decode($productinfo['Content'], ENT_QUOTES,'UTF-8');
			?>
            </fieldset>           
            
			<fieldset class="fieldsetstyle">
			<legend>设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle" >
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">搜索关键词(TAG)：</div></td>
                  <td width="55%"><label>
                    <? echo $productinfo['ContentKeywords'];?>
                  </label></td>
                  <td width="29%"></td>
                </tr>    
			<? if(!empty($pointarr) || $pointarr['pointtype']=="3"){?>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">积分：</div></td>
                  <td width="55%"><strong>
                   <? echo $productinfo['ContentPoint']; ?>
                  </strong></td>
                  <td width="29%"></td>
                </tr>
			<? }?>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>
                  <td width="55%"><strong>
                   <? echo $productinfo['OrderID']; ?>
                  </strong></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">整包装出货数量：</div></td>
                  <td width="55%"><strong>
                   <? echo $productinfo['Package']; ?>
                  </strong></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品类型：</div></td>
                  <td >&nbsp;
          <? if($productinfo['CommendID']=="0") echo '默认';?>
          <? if($productinfo['CommendID']=="1") echo '推荐';?>
          <? if($productinfo['CommendID']=="2") echo '特价';?>
          <? if($productinfo['CommendID']=="3") echo '新款';?>
          <? if($productinfo['CommendID']=="4") echo '热销';?>
          <? if($productinfo['CommendID']=="9") echo '缺货';?></td>
                  <td>&nbsp;</td>
                </tr>

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">屏蔽药店：</div></td>
                  <td width="55%">				  
					<?
					$shielddata = $db->get_results("select c.ClientID,c.ClientCompanyName from ".DATATABLE."_order_shield s left join ".DATATABLE."_order_client c on s.ClientID=c.ClientID where s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.ContentID=".$productinfo['ID']."");
					if(!empty($shielddata))
					{
						$n=1;
						foreach($shielddata as $var)
						{
							if(!empty($var['ClientCompanyName'])) echo '<dd>'.$n++.' 、 '.$var['ClientCompanyName'].'</dd>';
						}
					}
					?>				  
				  </td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">人 气：</div></td>
                  <td >&nbsp;<strong><? echo $productinfo['Count'];?></strong></td>
                  <td>&nbsp;</td>
                </tr>
              </table>
           </fieldset>
		   <br style="clear:both;" />
            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>
			<br style="clear:both;" />
        	</div>
          
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

</body>
</html>