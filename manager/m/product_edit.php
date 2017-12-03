<?php 
$menu_flag = "product";
$pope	   = "pope_form";
include_once ("header.php");
$p3 = $p4 = '';
$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

$fieldarr    = get_set_arr('field');
$pointarr    = get_set_arr('point');
$productarr  = get_set_arr('product');
$erparr      = get_set_arr('erp');

$is_erp = erp_is_run($db,$_SESSION['uinfo']['ucompany']);

$isOff = empty($productarr['product_number']) || $productarr['product_number']=="off"; //是否关闭库存
if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$productinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_content_1 c on i.ID=c.ContentIndexID where i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ID=".intval($in['ID'])." limit 0,1");
	if(empty($productinfo['ID'])) exit('此商品不存在，或已经删除!');
	$in['sid'] = $productinfo['SiteID'];
    $spec = $db->get_col("SELECT SpecName FROM ".DATATABLE."_order_specification WHERE SpecType='Specification' AND CompanyID='".$_SESSION['uinfo']['ucompany']."'");
    $color = $db->get_col("SELECT SpecName FROM ".DATATABLE."_order_specification WHERE SpecType='Color' AND CompanyID='".$_SESSION['uinfo']['ucompany']."'");
    $spec = $spec ? $spec : array();
    $color = $color ? $color : array();
    $cur_spec = $productinfo['Specification'] ? explode(',',$productinfo['Specification']) : array();
    $cur_color = $productinfo['Color'] ? explode(',',$productinfo['Color']) : array();
    $cur_spec = array_map('trim',$cur_spec);
    $cur_color = array_map('trim',$cur_color);
    $spec = array_unique(array_merge($spec,$cur_spec));
    $color = array_unique(array_merge($color,$cur_color));

    if(empty($cur_spec)) {
        $stockSpec = array();
    } else {
        $stockSpec = $db->get_col("SELECT ContentSpec FROM ".DATATABLE."_order_inventory_number WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND (OrderNumber<>0 OR ContentNumber<>0) AND ContentID=".$productinfo['ID']." AND ContentSpec IN(".implode(',',SCEncode($cur_spec)).") GROUP BY ContentSpec");// array();
        $stockSpec = $stockSpec ? $stockSpec : array();
    }

    if(empty($cur_color)){
        $stockColor = array();
    } else {
        $stockColor = $db->get_col("SELECT ContentColor FROM ".DATATABLE."_order_inventory_number WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND (OrderNumber<>0 OR ContentNumber<>0) AND ContentID=".$productinfo['ID']." AND ContentColor IN(".implode(',',SCEncode($cur_color)).") GROUP BY ContentColor");// array();
        $stockColor = $stockColor ? $stockColor : array();
    }

    $spec = buildData($spec);
    $color = buildData($color);

	if(!empty($productinfo['Price3']))
	{		
		$productprice = unserialize(urldecode($productinfo['Price3']));
		$p4 = $productprice['clientprice'];
		unset($productprice['clientprice']);
		$p3 = $productprice;
		$p4 = urlencode(serialize($p4));
		$p3 = urlencode(serialize($p3));
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/select2.css" type="text/css"/>

<script src="../scripts/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="../scripts/select2.js" type="text/javascript"></script>
<script src="../scripts/AC_RunActiveContent.js" type="text/javascript"></script>
<script src="js/json2.js" type="text/javascript"></script>
<script src="js/product.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<!-- 
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/core.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/events.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/css.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/coordinates.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/drag.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/dragsort.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/cookies.js"></script>
 -->
 
<style type="text/css">
<!--
#units_div{position:absolute; z-index:19999; zoom:1; width:105px; background-color:#efefef; height:auto; display:none; margin-top:25px; margin-left:47px; font-weight:normal; padding:5px; text-align:left; border:#CCCCCC solid 1px;}
#units_div a{padding:1px 4px 1px 4px; margin:1px 3px; float:left;}
#units_div a:hover{padding:1px 4px 1px 4px; margin:1px 3px; float:left; background-color:#ffffff; }
.editcs li.select2-search-field{border:none;}
input.spec{
    background:none;
    border:1px dashed #CCC;
    font-size:18px;
    color:#CCC;
    font-weight:bold;
    margin-left:0px;
    width:40px;
    height:28px;
}
input.spec:hover{
    background:#CCC;
    color:#000;
    border:1px dashed #000;
}
.select2-container .select2-choice{
	border: #cbcbcd solid 1px !important;
	border-radius: 0;
}
.select2-container .select2-choice .select2-arrow{
	border-left:none !important;
	background:transparent !important;
}
-->
</style>

<script type="text/javascript">

$(function(){
	var data_Nearvalid=$("#data_Nearvalid");
	var data_Farvalid=$("#data_Farvalid");
		data_Nearvalid.datepicker({changeMonth: true,	changeYear: true,beforeShow:checksDate});
		data_Farvalid.datepicker({changeMonth: true,	changeYear: true,beforeShow:checksDate});
		
		data_Nearvalid.change(function(e){
		
			var Near=$(this).val();
			var Farvalid= data_Farvalid.val();
			if(Farvalid !='' && Near !=''){
				var Fdate = new Date(Date.parse(Farvalid.replace(/-/g,"/")));
				Fdate = Fdate.getTime();
				 
				var Ndate = new Date(Date.parse(Near.replace(/-/g,"/")));
				Ndate = Ndate.getTime();
				
				 if(Ndate >= Fdate){
					 alert('近效期不能大于或者等于远效期！');
					 var old_time=$(this).attr('data-old-time');
					 data_Nearvalid.val(old_time);
					 
				}
				
			}
		});
		
		data_Farvalid.change(function(e){
			
			var Far=$(this).val();
			var Nearvalid= data_Nearvalid.val();
			if(Nearvalid !='' && Far !=''){
				var Near_date = new Date(Date.parse(Nearvalid.replace(/-/g,"/")));
				Near_date = Near_date.getTime();
				 
				var Far_date = new Date(Date.parse(Far.replace(/-/g,"/")));
				Far_date = Far_date.getTime();
				
				 if(Near_date >= Far_date){
					 alert('近效期不能大于或者等于远效期！');
					 var old_time=$(this).attr('data-old-time');
					 data_Farvalid.val(old_time);
				}
				
			}
		});
		
		function checksDate(ev){
			var old_date=$(this).val();
				$(this).attr("data-old-time",old_date);
			//alert(old_date);
			
		}
	
	
});	

	var dragsort = ToolMan.dragsort()
	var junkdrawer = ToolMan.junkdrawer()

function bindImgOrder(){
	junkdrawer.restoreListOrder("thumbimg");
	dragsort.makeListSortable(document.getElementById("thumbimg"));
}

$(function() {
	bindImgOrder();
});
</script>
</head>

<body onclick="hideshow('units_div','hide')">   
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
         <input type="hidden" name="update_id" id="update_id" value="<? echo $productinfo['ID'];?>" />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="product.php">商品管理</a> &#8250;&#8250; <a href="#">修改商品</a> </div>
   	        </div>           
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="going('editsave','<? echo $in['sid'];?>')" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='product.php?sid=<? echo $in['sid'];?>'" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			  <legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">                 
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品名称：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_Name" id="data_Name" value="<? echo $productinfo['Name']; ?>" style="width:89%;" />
                    <span class="red">*</span></label></td>
                  <td bgcolor="#FFFFFF"><input name="FlagID" type="checkbox" id="FlagID" value="ok" <?php if($productinfo['FlagID'] == "1") echo ' checked="checked" '; ?>  style="width:18px; height:18px;" title="暂不上架" /> &nbsp; <font color=red>暂不上架请打勾</font></td>
                </tr>
                 <tr>
                  <td width="18%" bgcolor="#F0F0F0"><div align="right">商品分类/品牌：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">
                  <div style="width:280px; float:left; ">
				  <select name="data_SiteID" id="data_SiteID" tabindex="2" style="width:245px;" class="select2">
                    <option value="">⊙ 请选择商品分类</option>
                    <?php 
						$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
						echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					?>
                  </select>
                  <span class="red">*</span>
				  </div>
					<div style="width:280px; float:left; text-align:right; " >
					  <select name="data_BrandID" id="data_BrandID" tabindex="3" style="width:245px; text-align:left;" class="select2">
						<option value="0">⊙ 请选择商品品牌</option>
						<?php
							$bsql   = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by BrandPinYin ASC";
							$bdata = $db->get_results($bsql);
							foreach($bdata as $v)
							{
								if($productinfo['BrandID']==$v['BrandID']) $smsg = 'selected="selected"'; else $smsg = '';
								echo '<option value="'.$v['BrandID'].'" '.$smsg.'>'.substr($v['BrandPinYin'],0,1).' - '.$v['BrandName'].'</option>';
							}
						?>
					  </select>
					</div>
				  </td>
                  <td width="29%" bgcolor="#FFFFFF" >若无分类或品牌，请先点击添加&nbsp;<a href="product_sort.php">[添加分类]</a>&nbsp;&nbsp;<a href="product_brand.php">[添加品牌]</a></td>
                </tr>
                  <tr>
                      <td bgcolor="#F0F0F0"><div align="right">药品规格/批准文号：</div></td>
                      <td bgcolor="#FFFFFF">
                          <label>
                              	规&nbsp;&nbsp;&nbsp;格：<input type="text" name="data_Model" id="data_Model" value="<? echo $productinfo['Model']; ?>" style="width:193px;" />
                          </label>
                          <label>
                              	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              	批准文号：<input type="text" name="data_Appnumber" id="data_Appnumber" value="<? echo $productinfo['Appnumber']; ?>" style="width:185px;" />
                          </label>
                      </td>
                      <td bgcolor="#FFFFFF">&nbsp;</td>
                  </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">编号/条码：</div></td>
                  <td bgcolor="#FFFFFF">
				  <div style="width:280px; float:left; ">
                      <?php if($is_erp && $productinfo['ERP'] == 'T'){
                          ?>
                      <input name="data_Coding" id="data_Coding" tabindex="4" value="<?php echo $productinfo['Coding']; ?>" type="hidden"/>
                      编&nbsp;&nbsp;&nbsp;号：<input type="text"  id="data_Coding" readonly="readonly" disabled="disabled" tabindex="4" value="<? echo $productinfo['Coding']; ?>"   style="width:193px;"  />&nbsp;</div><div style="width:280px; float:left; text-align:right;">条&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="text" name="data_Barcode" id="data_Barcode" tabindex="5" value="<? echo $productinfo['Barcode']; ?>"   style="width:185px;"  />
                    <?php
                      }else{
                          ?>
                          编&nbsp;&nbsp;&nbsp;号：<input type="text" name="data_Coding" id="data_Coding" onblur="check_coding(this,<?php echo $productinfo['ID']; ?>);" tabindex="4" value="<? echo $productinfo['Coding']; ?>"   style="width:193px;"  />&nbsp;</div><div style="width:280px; float:left; text-align:right;">条&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="text" name="data_Barcode" id="data_Barcode" tabindex="5" value="<? echo $productinfo['Barcode']; ?>"   style="width:185px;"  />
                    <?php
                      } ?>
                      </div>
				  </td>
                  <td bgcolor="#FFFFFF">
                      <span id="coding_unique" style="display:none;color:red;">
                        商品编码已存在,请重新输入!
                      </span>
                  </td>
                </tr>
                <tr title="	注：价格的优先级为：指定价 -> 等级价 -> 商品价格（商品价格与药店折扣绑定，指定价和等级价不再打折）">
                  <td bgcolor="#F0F0F0"><div align="right">商品价格：</div></td>
                  <td bgcolor="#FFFFFF">
                      <?php
                      $price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
                      $price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";
                      ?>
                      <div style="width:280px; float:left; " title="<?php echo $price1_name; ?>"><?php echo $price1_name; ?>：<input type="text" name="data_Price1" id="data_Price1" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" tabindex="4" style="width:193px;" value="<? echo $productinfo['Price1']; ?>" /><span class="red">&nbsp;*</span></div>
                      <div style="width:280px; float:left; text-align:right;" title="<?php echo $price2_name; ?>"><?php echo $price2_name; ?>：<input type="text" name="data_Price2" id="data_Price2" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" tabindex="5" style="width:185px;" value="<? echo $productinfo['Price2']; ?>"  /></div>
                  	  <span class="red">&nbsp;*</span>	
                  </td>
                  <td bgcolor="#FFFFFF">
				  <input type="button" name="newbutton3" id="newbutton3" value="等级价" style="width:60px; font-size:12px; height:24px; margin:0 4px;" class="bluebtn" onclick="set_level_price('set_Price3');" title="药店等级价格设置"  />
				  <input type="hidden" name="set_Price3" id="set_Price3" value="<? if(!empty($p3)) echo $p3;?>" />
				  <input type="button" name="newbutton4" id="newbutton4" value="指定价" style="width:60px; font-size:12px; height:24px; margin:0 4px;" class="greenbtn" onclick="set_client_price('set_Price4');" title="指定药店价格设置" />
				  <input type="hidden" name="set_Price4" id="set_Price4" value="<? if(!empty($p4)) echo $p4;?>" /> 
				  </td>
                </tr>
				<tr>
                      <td bgcolor="#F0F0F0"><div align="right">药品效期：</div></td>
                      <td bgcolor="#FFFFFF">
                          <label>
                              	近效期：<input type="text" name="data_Nearvalid" id="data_Nearvalid" data-old-time="" value="<? echo $productinfo['Nearvalid']; ?>" readonly="readonly" style="width:193px;" />
                          </label>
                          <label>
                              	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              	远效期：<input type="text" name="data_Farvalid" id="data_Farvalid" data-old-time="" value="<? echo $productinfo['Farvalid']; ?>" readonly="readonly" style="width:185px;" />
                          </label>
                      </td>
                      <td bgcolor="#FFFFFF">&nbsp;</td>
                  </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">销售单位：</div></td>
                  <td bgcolor="#FFFFFF">
                  
                  <div style="width:122px; float:left; ">
				  <div id="units_div" onclick="hideshow('units_div','hide')"  ><?
					echo '<div><strong>常用单位：</strong></div>';
					$unitsarr = explode('|',$Site_Config['upfile']['units']);
					foreach($unitsarr as $uvar){ echo '<a href="javascript:void(0)" onclick="set_units_val(\''.$uvar.'\')">'.$uvar.'</a>';}
				  ?></div>
				  小单位：<input type="text" name="data_Units" id="data_Units" tabindex="6"  style="width:55px;" value="<? echo $productinfo['Units']; ?>" onmouseover="hideshow('units_div','show')" onfocus="hideshow('units_div','show')" />&nbsp;<span class="red">*</span></div>				  
				  <div style="width:122px; float:left; text-align:right;">大单位：<input type="text" name="data_Casing" id="data_Casing" tabindex="7"  style="width:55px;" value="<? echo $productinfo['Casing']; ?>" /></div>
				  <div style="width:316px; float:left; text-align:right;">换算关系：<input type="text" name="data_Conversion" id="data_Conversion" tabindex="7"  style="width:185px;" value="<? echo $productinfo['Conversion']; ?>" onfocus="this.select();" /></div>
				  
				  </td> 
                  <td bgcolor="#FFFFFF">换算关系，例如：一包装箱=12盒</td>
                </tr>
				 
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品图片：</div></td>
                  <td bgcolor="#FFFFFF" id="show_thumb_mu_img">
				<?php				  
				$imginfo = $db->get_results("SELECT Name,OldName,Path,Size,OrderID FROM ".DATATABLE."_order_resource where IndexID=".$productinfo['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." group by Name order by OrderID asc");
				$out_thumb_msg = '<ul id="thumbimg">';
				if(!empty($imginfo))
				{	
					foreach($imginfo as $upkey=>$upvar)
					{
						$rvar['filename']  = $upvar['Name'];
						$rvar['filepath']  = $upvar['Path'];
						$rvar['filesize']  = $upvar['Size'];
						$rvar['oldname']   = $upvar['OldName'];

						if($productinfo['Picture'] == $rvar['filepath'].'thumb_'.$rvar['filename']) $smsg = 'checked="checked"'; else $smsg='';

						$out_thumb_msg .= '<li id="mu_img_id_'.$upvar['OrderID'].'" _filename="'.$rvar['filename'].'" _filepath="'.$rvar['filepath'].'" _filesize="'.$rvar['filesize'].'" _oldname="'.$rvar['oldname'].'"><a href_="'.RESOURCE_URL.$rvar['filepath'].'img_'.$rvar['filename'].'" target="_blank"><img src="'.RESOURCE_URL.$rvar['filepath'].'thumb_'.$rvar['filename'].'" title="'.$rvar['oldname'].'" width="70" height="70" border="0" /></a><br /><div class="checkbox thumbimg_dd_left" title="设为列表页默认图片"><input name="DefautlImg" type="radio" value="'.($upkey+1).'" '.$smsg.' />默认</div><div class="thumbimg_dd_div" onclick="remove_up_img(\''.$upvar['OrderID'].'\')" title="删除图片">X</div></li>';
					}					
				}else{				  
					if(!empty($productinfo['Picture']))
					{						
						$splitpic = explode("/", $productinfo['Picture']);
						
						$rvar['filename'] = str_replace("thumb_","",array_pop($splitpic));
						$rvar['filepath']  = implode("/", $splitpic)."/";
						$rvar['filesize']    = 0;
						$rvar['oldname']  = '';
						
						$out_thumb_msg .= '<li id="mu_img_id_0" _filename="'.$rvar['filename'].'" _filepath="'.$rvar['filepath'].'" _filesize="'.$rvar['filesize'].'" _oldname="'.$rvar['oldname'].'"><a href="'.RESOURCE_URL.$productinfo['Picture'].'" target="_blank"><img src="'.RESOURCE_URL.$productinfo['Picture'].'" width="70" height="70" border="0" /></a><br /><div class="checkbox thumbimg_dd_left" title="设为列表页默认图片"><input name="DefautlImg" type="radio" value="0" />默认</div><div class="thumbimg_dd_div" onclick="remove_up_img(\'0\')" title="删除图片">X</div></li>';
					}
				}
				$out_thumb_msg .= '</ul>';
				echo $out_thumb_msg;
				?>			  
				  </td>
                  <td bgcolor="#FFFFFF" id="data_Picture_text" title="可批量上传多张图片">
					<img src="img/up_img.jpg" onClick="upload_mu_img('data_Picture');" title="上传商品图片" style="cursor: pointer;" />
					<p>上传的图片可以进行排序，鼠标左键按住不放，进行拖动交换位置！</p>
				  </td>
                </tr>

                  <?php
                    $specReadOnly = $is_erp ? "readonly='readonly'" : "";
                    if($_SESSION['uinfo']['ucompany'] == 471){
                    	$specReadOnly =  ""; //tubo修改，唯品会同步erp但要修改规格
                    }
                  ?>

                <tr style="display: none;">
                  <td bgcolor="#F0F0F0"><div align="right">多属性选项：</div></td>
                  <td bgcolor="#FFFFFF">
					<div>
                        <strong>可选规格：</strong>(同一商品，有多个可选<font color=red>规格</font>。)<br />
                        <input style="width:80%;" type="text" name="Specification" id="Specification" value="" <?php echo $specReadOnly; ?> title="注：添加新的规格时填写，上面已有的请不要重复填写!" />
                        <?php if(empty($specReadOnly)) { ?>
                        <input type="button" data-act="Specification" value=" + " class="bluebtn spec" title="添加规格" />
                        <?php } ?>
					</div>
                      <!-- F 115 -->
					<div>
                        <strong>可选颜色：</strong>(同一商品，有多个可选<font color=red>颜色</font>。)<br/>
                        <input style="width:80%;" type="text" name="Color" id="Color" value="" <?php echo $specReadOnly; ?> title="注：添加新的颜色时填写，上面已有的请不要重复填写!" />
                        <?php if(empty($specReadOnly)) { ?>
                        <input type="button" data-act="Color" value=" + " class="bluebtn spec" title="添加颜色" />
                        <?php } ?>
					</div>
				  </td>
                  <td bgcolor="#FFFFFF"><font color=red>注：对应商品属性有库存的不能删除，只能删除库存为零的属性；如需新增商品属性，请在新增输入框中录入，多个用“,”分隔！</font></td>
                </tr>
              </table>
		</fieldset>

<?php
if(!empty($fieldarr))
{
	if(!empty($productinfo['FieldContent'])) $farr = unserialize($productinfo['FieldContent']);
?>
	<fieldset  class="fieldsetstyle">
		<legend>自定义字段</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
<?php
	foreach($fieldarr as $k=>$v)
	{
?>
				<tr>
                  <td bgcolor="#F0F0F0" width="18%"><div align="right"><? echo $v['name'];?>：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input type="text" name="<? echo $k?>" id="<? echo $k?>" value="<? if(!empty($farr[$k])) echo $farr[$k]; ?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
		<?php
			}	
		?>
				</table>
	</fieldset>
	<?php }?> 
		<fieldset title="详细描述" class="fieldsetstyle">
			<legend>详细描述</legend>
		<script src="../ckeditor/ckeditor.js?v=3"></script>
		<textarea class="ckeditor" cols="80" id="editor1" name="editor1" rows="10">
			<?php

				//$stringContent = html_entity_decode($productinfo['Content'], ENT_QUOTES,'UTF-8');
				echo $initialValue = str_replace('http://resource.dhb.hk/',RESOURCE_URL,$productinfo['Content']);

			?>
		</textarea>


        </fieldset>            
            
		<fieldset title="设置" class="fieldsetstyle">
			<legend>设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle" >                
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">关联商品：</div></td>
                  <td ><label>
                    <input name="Relation" type="hidden" id="Relation" value=""   />
					<div style="height:100px; width:450px; padding:4px; float:left; ">
					<select name="selectrelation"  id="selectrelation" size="8" style="height:100px; width:440px; padding:4px; float:left; " >
					<?php
					$relationdata = $db->get_results("select ID,Name from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." AND FlagID=0  AND ID IN(select FID FROM ".DATATABLE."_order_relation where CompanyID=".$_SESSION['uinfo']['ucompany']." and SID=".$productinfo['ID']." union select SID FROM ".DATATABLE."_order_relation where CompanyID=".$_SESSION['uinfo']['ucompany']." and FID=".$productinfo['ID'].")");
					if(!empty($relationdata))
					{
						foreach($relationdata as $var)
						{
							echo '<option value="'.$var['ID'].'">'.$var['Name'].'</option>';
						}
					}
					?>
					</select>
					</div>
					<div style="height:100px; width:80px; margin:6px 0; padding:4px; float:left; ">
						<div ><input name="r1" id="relation_r1" type="button"  value="删除" onClick="del_relation();" class="bluebtn" title="删除选中的商品"  style="width:80px; height:24px; font-size:12px;" /></div>
						<div style="margin-top:8px;"><input name="r2"  id="relation_r2" type="button"  value="清空" onClick="clear_relation();" class="bluebtn" title="清除所有商品" style="width:80px; height:24px; font-size:12px;"  /></div>
						<div style="margin-top:8px;"><input name="r4"  id="relation_r4" type="button" value="选择" onClick="set_relation();" class="bluebtn" title="选择关联的商品" style="width:80px; height:24px; font-size:12px;"  /></div>
					</div>
                 </label></td>
                  <td >(关联的商品，会出现在关联商品订购页面)</td>
                </tr>	
				
				<tr>
                  <td width="18%" bgcolor="#F0F0F0"><div align="right">关键词(TAG)：</div></td>
                  <td width="55%"><label>
                    <input name="ContentKeywords" type="text" id="ContentKeywords" value="<? echo $productinfo['ContentKeywords'];?>" style="width:87%;"  />
                  </label></td>
                  <td >(多个以<font color=red>空格</font>分隔,最多10个)</td>
                </tr>      

                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">排序、包装：</div></td>
                  <td >
				  <label style="width:280px; float:left; ">
                   排序权重：<input name="data_OrderID" type="text" id="data_OrderID" value="<? echo $productinfo['OrderID']; ?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();" title="排序依据，数字大的靠前" style="width:200px;"  />
                  </label>
				  <label style="width:280px; float:left; ">
                    整包装出货数量：<input name="Package" type="text" id="Package" value="<? echo $productinfo['Package']; ?>"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();" style="width:168px;" />
                 </label>
				  </td>
                  <td >订购数必需为此数量的整倍数</td>
                </tr>

                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">库存预警：</div></td>
                  <td >
				  <label style="width:280px; float:left; ">
                    库存下限：<input name="data_LibraryDown" type="text" id="data_LibraryDown" value="<? echo $productinfo['LibraryDown']; ?>"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" onfocus="this.select();" style="width:200px;" />
                 </label>
					<label style="width:280px; float:left; ">
                    库存上限：<input name="data_LibraryUp" type="text" id="data_LibraryUp" value="<? echo $productinfo['LibraryUp']; ?>"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" onfocus="this.select();" style="width:203px;" />
                 </label>
				 </td>
                  <td > </td>
                </tr>

	            <tr <? if(empty($pointarr) || $pointarr['pointtype']!="3") echo 'style="display:none;"'; ?>>
                  <td  bgcolor="#F0F0F0"><div align="right">积分：</div></td>
                  <td ><label>
                    <input name="ContentPoint" type="text" id="ContentPoint" value="<? echo $productinfo['ContentPoint']; ?>"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();" />
                 </label></td>
                  <td > (请录入整数)</td>
                </tr>

                <tr <? if(empty($productarr['deduct_type']) || $productarr['deduct_type']=="off") echo 'style="display:none;"'; ?> >
                  <td  bgcolor="#F0F0F0"><div align="right">业务提成比例：</div></td>
                  <td ><label>
                    <input name="Deduct" type="text" id="Deduct" value="<? echo $productinfo['Deduct']; ?>"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="5" onfocus="this.select();" style="width:87%;" /> %
                 </label></td>
                  <td > (客情官提成金额 = 成交价 * 提成比例%)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品类型：</div></td>
                  <td class="checkbox">&nbsp;
				  <input name="data_CommendID" type="radio" value="0" <? if($productinfo['CommendID']=="0") echo 'checked="checked"';?> /> 默认&nbsp;&nbsp; 
				  <input name="data_CommendID" type="radio" value="1" <? if($productinfo['CommendID']=="1") echo 'checked="checked"';?> /> 预售<!-- 推荐 -->&nbsp;&nbsp;
				  <input name="data_CommendID" type="radio" value="2" <? if($productinfo['CommendID']=="2") echo 'checked="checked"';?> title="特价商品不执行药店折扣" /> 特价&nbsp;&nbsp;
				  <input name="data_CommendID" type="radio" value="3" <? if($productinfo['CommendID']=="3") echo 'checked="checked"';?> /> 控销<!-- 新款 -->&nbsp;&nbsp;
				  <input name="data_CommendID" type="radio" value="4" <? if($productinfo['CommendID']=="4") echo 'checked="checked"';?> /> 热销&nbsp;&nbsp;
				  <input name="data_CommendID" type="radio" value="8" <? if($productinfo['CommendID']=="8") echo 'checked="checked"';?>  /> 赠品&nbsp;&nbsp;
				  <input name="data_CommendID" type="radio" value="9" <? if($productinfo['CommendID']=="9") echo 'checked="checked"';?> /> 缺货</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">屏蔽药店：</div></td>
                  <td >
                    <input name="Shield" type="hidden" id="Shield" value=""   />
					<div style="height:100px; width:450px; padding:4px; float:left; ">
					<select name="selectshield"  id="selectshield" size="8" style="height:100px; width:440px; padding:4px; float:left; " >
					<?php
					$shielddata = $db->get_results("select c.ClientID,c.ClientCompanyName from ".DATATABLE."_order_shield s left join ".DATATABLE."_order_client c on s.ClientID=c.ClientID where s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.ContentID=".$productinfo['ID']."");
					if(!empty($shielddata))
					{
						foreach($shielddata as $var)
						{
							if(!empty($var['ClientID'])) echo '<option value="'.$var['ClientID'].'">'.$var['ClientCompanyName'].'</option>';
						}
					}
					?>
					</select>
					</div>
					<div style="height:100px; width:80px; padding:4px; float:left; ">
						<div ><input name="b1" type="button"  value="删除" onClick="del_client();" class="bluebtn" title="删除选中的药店"  style="width:80px; height:24px; font-size:12px;" /></div>
						<div style="margin-top:8px;"><input name="b2" type="button"  value="清空" onClick="clear_client();" class="bluebtn" title="清除所有药店" style="width:80px; height:24px; font-size:12px;"  /></div>
						<div style="margin-top:8px;"><input name="b4" type="button" value="选择" onClick="set_shield_client();" class="bluebtn" title="选择您要屏蔽的药店" style="width:80px; height:24px; font-size:12px;"  /></div>
					</div>
                 </td>
                  <td >(被屏蔽的药店将看不到此商品)</td>
                </tr>
              </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid2" value="保 存" onclick="going('editsave','<? echo $in['sid'];?>')" />
			<input name="backproductid" type="button" class="button_3" id="backproductid2" value="返 回" onclick="javascript:window.location.href='product.php?sid=<? echo $in['sid'];?>'" />
			</div>

        </div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">  
		<script type="text/javascript">
		AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0','width','300','height','300','src','../plugin/SWFUpload/swfupload/swfupload','quality','high','pluginspage','http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash','movie','../plugin/SWFUpload/swfupload/swfupload' ); //end AC code
		</script>
		</div>
	</div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript" src="../scripts/select2_locale_zh-CN.js"></script>
<script type="text/javascript">
<?php

$colormsg = json_encode($color); 
if(empty($colormsg)) $colormsg = '[]';

$specmsg = json_encode($spec); 
if(empty($specmsg)) $specmsg = '[]';
?>
    var colorTags = <?php echo $colormsg; ?>;
    var specTags = <?php echo $specmsg; ?>;

    //选中、有库存的项把locked加上
    var colorSel = <?php echo json_encode(buildData($cur_color,true,$stockColor)); ?>;
    var specSel = <?php echo json_encode(buildData($cur_spec,true,$stockSpec)); ?>;

    String.prototype.in_array = function(arr){
        for(var i = 0, k=arr.length;i < k;i++){
            if(this==arr[i]){
                return true;
            }
        }
        return false;
    }
    function set_specification_spec(data){
        specSel.push({id:data,text:data});
        specTags.push({id:data,text:data});
        initSpec();
        $("#Specification").select2("data",specSel);
    }

    function set_specification_color(data){
        colorSel.push({id:data,text:data});
        colorTags.push({id:data,text:data});
        initColor();
        $("#Color").select2("data",colorSel);
    }

    function initSpec(){
        $("#Specification").select2({
            width:480,
            multiple:true,
            //data:specTags,
            closeOnSelect:false
            ,query:function(query){
                var data = {results:[]};
                var exists = []; //FIXME　数据本身不应该重复
                $(specTags).each(function(){
                    if(query.term.length==0 || this.text.toUpperCase().indexOf(query.term.toUpperCase()) >= 0){
                        if($.inArray(this.text,exists) == -1) {
                            data.results.push({id:this.id,text:this.text});
                            exists.push(this.text);
                        }
                        /*if(!this.text.in_array(exists)){
                            data.results.push({id:this.id,text:this.text});
                            exists.push(this.text);
                        }*/
                    }
                });
                query.callback(data);
            }
        });

    }
    function initColor(){
        $("#Color").select2({
            width:480,
            multiple:true,
            //data:colorTags,
            closeOnSelect:false
            ,query:function(query){
                var data = {results:[]};
                var exists = []; //FIXME　数据本身不应该重复
                $(colorTags).each(function(){
                    if(query.term.length==0 || this.text.toUpperCase().indexOf(query.term.toUpperCase()) >= 0){
                        if(-1 == $.inArray(this.text,exists)) {
                            data.results.push({id:this.id,text:this.text});
                            exists.push(this.text);
                        }
                        /*if(!this.text.in_array(exists)){
                            data.results.push({id:this.id,text:this.text});
                            exists.push(this.text);
                        }*/
                    }
                });
                query.callback(data);
            }
        });
    }
    //554
    $(function(){
        $("input[data-act='Specification'],input[data-act='Color']").click(function(){
            var SpecType = $(this).data('act');
            var title = SpecType == 'Specification' ? '规格管理' : '颜色管理';
            $('#windowContent').html('<iframe src="product_specification.php?SpecType='+SpecType+'" width="500" marginwidth="0" height="470" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
            $("#windowtitle").html(title);
            $.blockUI({
                message: $('#windowForm'),
                css:{
                    width: '500px',height:'500px',top:'10%'
                }
            });
            $('#windowForm').css("width","500px");
            $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
        });
        initColor();
        initSpec();

        $("#Color").select2('data',colorSel);
        $("#Specification").select2("data",specSel);
    });

</script>
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";		
		if($p_id=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >┠-".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
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
			$rchar = $char.str_repeat("&nbsp;", (4-strlen($char)));
		}
		return $rchar;
	}

    function SEncode($str){
        $fp = array('+','/','=','_');
        $rp = array('-','|','DHB',' ');
        return str_replace($fp,$rp,base64_encode($str));
    }

    function SCEncode($arr){
        if(is_string($arr)){
            $arr = explode(',',$arr);
        }
        foreach($arr as $k=>$v){
            $arr[$k] = "'" . SEncode($v). "'";
        }
        return $arr;
    }

/**
 * @desc 生成下拉框数据
 * @param array $specification 目标数据(一维数组)
 * @param bool $locked 是否检测添加locked
 * @param array $stock locked检测数组
 * @return array
 */
    function buildData($specification,$locked=false,$stock=array()){
        $data = array();
        foreach($specification as $val){
            $source_val = $val;
            $val = html_entity_decode(trim($val),ENT_QUOTES,'UTF-8');
            $temp = array(
                'id'=>$val,
                'text'=>$val
            );
            if($locked && in_array(SEncode($source_val),$stock)){
                $temp['locked'] = true;
            }
            $data[] = $temp;
        }
        return $data;
    }


?>