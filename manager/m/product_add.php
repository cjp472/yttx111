<?php 
$menu_flag = "product";
$pope	   = "pope_form";
include_once ("header.php");

if(erp_is_run($db,$_SESSION['uinfo']['ucompany'])) {
    exit('新增商品档案,请重ERP通过接口同步至DHB!谢谢!' . "<a href='javascript:;' onclick='history.back()'>返回</a>");
}

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}

$fieldarr    = get_set_arr('field');
$pointarr    = get_set_arr('point');
$productarr  = get_set_arr('product');
$specification = $db->get_results("SELECT SpecName,SpecType FROM ".DATATABLE."_order_specification WHERE CompanyID='".$_SESSION['uinfo']['ucompany']."'");
$spec = array();
$color = array();
foreach($specification as $val){
    if($val['SpecType']=='Color'){
        $color[] = trim($val['SpecName']);
    }else if($val['SpecType']=='Specification'){
        $spec[] = trim($val['SpecName']);
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
</script>
</head>

<body onclick="hideshow('units_div','hide')">
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
		
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="product.php">商品管理</a> &#8250;&#8250; <a href="product_add.php">新增商品</a> </div>
   	        </div>
            
            <div class="rightdiv sublink">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="going('save','<? echo $in['sid'];?>')" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
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
                    <input type="text" name="data_Name" id="data_Name" tabindex="2" style="width:554px;" />
                    <span class="red">*</span></label></td>
                  <td  bgcolor="#FFFFFF">&nbsp;<label>
                    <input name="FlagID" type="checkbox" id="FlagID" value="ok" style="width:18px; height:18px;" title="暂不上架" /> &nbsp; <font color=red>暂不上架请打勾</font>
                  </label></td>
                </tr>
                 <tr>
                  <td width="18%" bgcolor="#F0F0F0"><div align="right">商品分类/品牌：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">
                  <div style="width:280px; float:left; ">
				  <select name="data_SiteID" id="data_SiteID" tabindex="2" style="width:245px;" class="select2">
                    <option value="">⊙ 请选择商品分类</option>
                    <? 
					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					?>
                  </select>
                  <span class="red">*</span>
				  </div>
					<div style="width:280px; float:left; text-align:right; ">
					  <select name="data_BrandID" id="data_BrandID" tabindex="3" style="width:245px; text-align:left;" class="select2">
						<option value="0">⊙ 请选择商品品牌</option>
						<?php
							$bsql   = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by BrandPinYin ASC";
							$bdata = $db->get_results($bsql);

							foreach($bdata as $v)
							{
								echo '<option value="'.$v['BrandID'].'">'.substr($v['BrandPinYin'],0,1).' - '.$v['BrandName'].'</option>';
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
				  编&nbsp;&nbsp;&nbsp;号：<input type="text" name="data_Coding" id="data_Coding" onblur="check_coding(this,null);" tabindex="4"  style="width:194px;"  />&nbsp;</div><div style="width:280px; float:left; text-align:right;">条&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="text" name="data_Barcode" id="data_Barcode" tabindex="5"  style="width:185px;"  /></div>
				  </td>
                  <td bgcolor="#FFFFFF">
                      <span id="coding_unique" style="display:none;color:red;">
                        商品编码已存在,请重新输入!
                      </span>
                  </td>
                </tr>
                <tr title="	注：价格的优先级为：指定价 -> 等级价 -> 商品价格（商品价格与药店折扣绑定，指定价和等级价不再打折）">
                  <td bgcolor="#F0F0F0"><div align="right">商品价格：</div></td>
                  <td bgcolor="#FFFFFF" title="价格1和价格2至少要输入一个">
                      <?php
                        $price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
                        $price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";
                      ?>
                      <div style="width:280px; float:left; " title="<?php echo $price1_name; ?>"><?php echo $price1_name; ?>：<input type="text" name="data_Price1" id="data_Price1" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" tabindex="4" style="width:193px;" /></div>
                      <div style="width:280px; float:left; text-align:right;" title="<?php echo $price2_name; ?>"><?php echo $price2_name; ?>：<input type="text" name="data_Price2" id="data_Price2" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" tabindex="5" style="width:185px;"  /></div>
                  	  <span class="red">&nbsp;*</span>	
                  </td>
                  <td bgcolor="#FFFFFF">
				  <input type="button" name="newbutton3" id="newbutton3" value="等级价" style="width:60px; font-size:12px; height:24px; margin:0 4px;" class="bluebtn" onclick="set_level_price('set_Price3');" title="药店等级价格设置" /><input type="hidden" name="set_Price3" id="set_Price3" />
				  <input type="button" name="newbutton4" id="newbutton4" value="指定价" style="width:60px; font-size:12px; height:24px; margin:0 4px;" class="greenbtn" onclick="set_client_price('set_Price4');" title="指定药店价格设置" /><input type="hidden" name="set_Price4" id="set_Price4" />
				  </td>
                </tr>
				<tr>
                      <td bgcolor="#F0F0F0"><div align="right">药品效期：</div></td>
                      <td bgcolor="#FFFFFF">
                          <label>
                              	近效期：<input type="text" name="data_Nearvalid" id="data_Nearvalid" value="<? echo $productinfo['Nearvalid']; ?>" readonly="readonly" data-old-time="" style="width:193px;" />
                          </label>
                          <label>
                              	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              	远效期：<input type="text" name="data_Farvalid" id="data_Farvalid" value="<? echo $productinfo['Farvalid']; ?>" readonly="readonly" data-old-time="" style="width:185px;" />
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
				  <div style="width:124px; float:left; text-align:right;">大单位：<input type="text" name="data_Casing" id="data_Casing" tabindex="7"  style="width:55px;" value="<? echo $productinfo['Casing']; ?>" /></div>
				  <div style="width:314px; float:left; text-align:right;">换算关系：<input type="text" name="data_Conversion" id="data_Conversion" tabindex="7"  style="width:185px;" value="<? echo $productinfo['Conversion']; ?>" onfocus="this.select();" /></div>
				  
				  </td> 
                  <td bgcolor="#FFFFFF">换算关系，例如：一包装箱=12盒</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品图片：</div></td>
                  <td bgcolor="#FFFFFF" id="show_thumb_mu_img"><ul id="thumbimg"></ul>&nbsp;</td>
                  <td bgcolor="#FFFFFF" id="data_Picture_text" title="可批量上传多张图片">
					<img src="img/up_img.jpg" onClick="upload_mu_img('data_Picture');" title="上传商品图片" style="cursor:pointer;" />
					<p>上传的图片可以进行排序，鼠标左键按住不放，进行拖动交换位置！</p>
				  </td>
                </tr>

              </table>
	</fieldset>
<?
if(!empty($fieldarr))
{
?>
	<fieldset  class="fieldsetstyle">
		<legend>自定义字段</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
<?
	foreach($fieldarr as $k=>$v)
	{
?>
				<tr>
                  <td bgcolor="#F0F0F0" width="18%"><div align="right"><? echo $v['name'];?>：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input type="text" name="<? echo $k?>" id="<? echo $k?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<?
	}	
?>
				</table>
	</fieldset>
<? }?>   
	<fieldset title="详细描述" class="fieldsetstyle">
		<legend>详细描述</legend>
		<script src="../ckeditor/ckeditor.js?v=3"></script>
		<textarea class="ckeditor" cols="80" id="editor1" name="editor1" rows="10">
		
		</textarea>


        </fieldset>            
            
		<fieldset title="设置" class="fieldsetstyle">
			<legend>设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" >
			  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">关联商品：</div></td>
                  <td ><label>
                    <input name="Relation" type="hidden" id="Relation" value=""   />
					<div style="height:100px; width:450px; padding:4px; float:left; ">
					<select name="selectrelation"  id="selectrelation" size="8" style="height:100px; width:440px; padding:4px; float:left; " >
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
                    <input name="ContentKeywords" type="text" id="ContentKeywords" value="" style="width: 87%;" />
                  </label></td>
                  <td >(多个以<font color=red>空格</font>分隔,最多10个)</td>
                </tr>

                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">排序、包装：</div></td>
                  <td ><label style="width:280px; float:left; ">
                    排序权重：<input name="data_OrderID" type="text" id="data_OrderID" value="500"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();" title="排序依据，数字大的靠前" style="width:200px;" />
                 </label>
				 <label style="width:280px; float:left; ">
                    整包装出货数量：<input name="Package" type="text" id="Package" value="0"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();"  style="width:168px;" />
                 </label>
				 </td>
                  <td > (订购数必需为此数量的整倍数)</td>
                </tr>
	
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">库存预警：</div></td>
                  <td >
				  <label style="width:280px; float:left; ">
                    库存下限：<input name="data_LibraryDown" type="text" id="data_LibraryDown" value="0"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" onfocus="this.select();" style="width:200px;" />
                 </label>
					<label style="width:280px; float:left; ">
                    库存上限：<input name="data_LibraryUp" type="text" id="data_LibraryUp" value="0"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" onfocus="this.select();" style="width:203px;" />
                 </label>
				 </td>
                  <td > </td>
                </tr>

	            <tr <?php if(empty($pointarr) || $pointarr['pointtype']!="3") echo 'style="display:none;"'; ?>>
                  <td  bgcolor="#F0F0F0"><div align="right">积分：</div></td>
                  <td ><label>
                    <input name="ContentPoint" type="text" id="ContentPoint" value="0"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" onfocus="this.select();" />
                 </label></td>
                  <td > (请录入整数)</td>
                </tr>	
                <tr <? if(empty($productarr['deduct_type']) || $productarr['deduct_type']=="off") echo 'style="display:none;"'; ?> >
                  <td  bgcolor="#F0F0F0"><div align="right">业务提成比例：</div></td>
                  <td ><label>
                    <input name="Deduct" type="text" id="Deduct" value="0" style="width:87%;" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="5" onfocus="this.select();" /> %
                 </label></td>
                  <td > (客情官提成金额 = 成交价 * 提成比例%)</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品类型：</div></td>
                  <td class="checkbox" id="product_commendtype">&nbsp;
                      <input name="data_CommendID" type="radio" value="0" checked="checked"  /> 默认&nbsp;&nbsp; 
                      <input name="data_CommendID" type="radio" value="1"  /> 预售<!-- 推荐 -->&nbsp;&nbsp;
                      <input name="data_CommendID" type="radio" value="2"  title="特价商品不执行药店折扣" /> 特价&nbsp;&nbsp;
                      <input name="data_CommendID" type="radio" value="3"  /> 控销<!-- 新款 -->&nbsp;&nbsp;
                      <input name="data_CommendID" type="radio" value="4" /> 热销&nbsp;&nbsp;
                      <input name="data_CommendID" type="radio" value="8"  /> 赠品&nbsp;&nbsp;
                      <input name="data_CommendID" type="radio" value="9"  /> 缺货
                    </td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">屏蔽药店：</div></td>
                  <td ><label>
                    <input name="Shield" type="hidden" id="Shield" value=""   />
					<div style="height:100px; width:450px; padding:4px; float:left; ">
					<select name="selectshield"  id="selectshield" size="8" style="height:100px; width:440px; padding:4px; float:left; " >
					</select>
					</div>
					<div style="height:100px; width:80px; padding:4px; margin:6px 0; float:left; ">
						<div ><input name="b1" id="shield_b1" type="button"  value="删除" onClick="del_client();" class="bluebtn" title="删除选中的药店"  style="width:80px; height:24px; font-size:12px;" /></div>
						<div style="margin-top:8px;"><input name="b2"  id="shield_b2" type="button"  value="清空" onClick="clear_client();" class="bluebtn" title="清除所有药店" style="width:80px; height:24px; font-size:12px;"  /></div>
						<div style="margin-top:8px;"><input name="b4"  id="shield_b4" type="button" value="选择" onClick="set_shield_client();" class="bluebtn" title="选择您要屏蔽的药店" style="width:80px; height:24px; font-size:12px;"  /></div>
					</div>
                 </label></td>
                  <td >(被屏蔽的药店将看不到此商品)</td>
                </tr>
              </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">			
			<input name="saveproductid" type="button" class="button_1" id="saveproductid2" value="保 存" onclick="going('save','<? echo $in['sid'];?>')" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid2" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid2" value="返 回" onclick="history.go(-1)" />
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

<script type="text/javascript">
<?php
function hed($str) {
    return html_entity_decode($str,ENT_QUOTES,"UTF-8");
}

$color = array_map("hed",$color);

$colormsg = json_encode($color); 
if(empty($colormsg)) $colormsg = '[]';

$spec = array_map("hed",$spec);
$specmsg = json_encode($spec); 
if(empty($specmsg)) $specmsg = '[]';
?>
    var colorTags = <?php echo $colormsg; ?>;
    var specTags = <?php echo $specmsg; ?>;

    function set_specification_spec(data){
        var spec = $("#data_Specification").val() ? $("#data_Specification").val().split(',') : [];
        spec.push(data);
        $("#data_Specification").val(spec.join(','));
        specTags.push(data);
        initSpec();
    }

    function set_specification_color(data){
        var color  = $("#data_Color").val() ? $("#data_Color").val().split(',') : [];
        color.push(data);
        $("#data_Color").val(color.join(','));
        colorTags.push(data);
        initColor();
    }

    function initSpec(){
        $("#data_Specification").select2({
            placeholder:'请选择规格',
            width:480,
            tags:specTags,
            closeOnSelect:false
        });
    }
    function initColor(){
        $("#data_Color").select2({
            placeholder:'请选择颜色',
            width:480,
            tags:colorTags,
            closeOnSelect:false
        });
    }

    //554
    $(function(){
        $("input[data-act='Specification'],input[data-act='Color']").click(function(){
            var SpecType = $(this).data('act');
            var title = SpecType == 'Specification' ? '规格管理' : '颜色管理';
            //$('#windowContent').html('<iframe src="specification_pop_add.php?SpecType='+SpecType+'" width="500" marginwidth="0" height="420" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
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
    });

</script>
</body>
</html>
<?
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
			$rchar = $char.str_repeat(" -", (4-strlen($char)));
		}
		return $rchar;
	}
?>