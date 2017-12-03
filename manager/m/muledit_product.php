<?php 
include_once ("header.php");
$productarr  = get_set_arr('product');
$pointarr    = get_set_arr('point');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>

<script language="javascript">
<!--
function returnNodeID()
{
	if(document.form1.mulType.value == '') {
		alert("请选您要操作的对象!");
		return false;
	}else{
		var inputname  = document.getElementById("mulType").value
		var inputvalue = document.getElementById(inputname).value
		var rv = inputname+'^^'+inputvalue;
        window.parent.apply_muledit(rv);
        window.parent.cancel_muledit();
	}
}

function set_input(sid)
{
	if(sid != '')
	{
		document.getElementById("set_input_line").innerHTML = document.getElementById("show_input_"+sid).innerHTML;
	}
}
//-->
</script>

<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p,span,select{color:#666; font-size:14px; line-height:180%; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; }
input{border:#cbcbcd solid 1px; height:24px; font-size:12px; line-height:150%; color:#333333; VERTICAL-ALIGN: middle; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; width:80%;}
select{line-height:20px; height:24px; margin:4px; padding:2px; width:80%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.redbtn {
     width:60px; background-color:#ff9f36;  color: #FFF;  border:#ff9f36 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
 }

.bluebtn {
   width:60px; background-color:#47c4d7;  color: #FFF;  border:#47c4d7 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}

-->
</style>
</head>

<body>
<table width="92%" border="0" cellspacing="4" align="center" style="margin-left:20px;">
  <form name="form1" method="post" action="" onsubmit="return false;">
    <tr> 
      <td colspan="2" align="left" height="34">
	  	<strong>请选择操作对象:</strong> 
      </td>
    </tr>

    <tr> 
      <td colspan="2" align="left" height="34">
		<select name="mulType" id="mulType" style="padding:2px; line-height:22px;"  onChange="javascript:set_input(this.options[this.selectedIndex].value);" >
			<option value="">⊙ 请选择操作对象</option>
			<option value="BrandID">┠- 商品品牌</option>
			<option value="SiteID">┠- 商品分类</option>
			<option value="Casing">┠- 商品包装</option>
			<option value="CommendID">┠- 商品类型</option>
			<option value="OrderID">┠- 排序权重</option>
			<option value="Package">┠- 整包装出货数量</option>
			<option value="LibraryDown">┠- 库存下限</option>
			<option value="LibraryUp">┠- 库存上限</option>
			<option value="Units">┠- 商品单位</option>
			<?php if(!empty($pointarr) && $pointarr['pointtype']=="3"){?>
			<option value="ContentPoint">┠- 商品积分</option>
			<?php 
			}
			if(!empty($productarr['deduct_type']) && $productarr['deduct_type']=="on"){
			?>
			<option value="Deduct">┠- 商品提成比例</option>
			<?php }?>
		</select>
	 </td>
    </tr>
    <tr> 
      <td colspan="2" align="left" height="34" >
		<strong>请填写您要批量修改对象的值:</strong> 
	 </td>
    </tr>
    <tr> 
      <td colspan="2" align="left" height="34" id="set_input_line">

	 </td>
    </tr>
	<tr>
      <td align="left"> <input name="Submit1" type="button" class="redbtn" onclick="returnNodeID();" value=" 提 交 "> 
      &nbsp;&nbsp;<input name="Submit2" type="button" class="bluebtn" onclick='window.parent.cancel_muledit();' value=" 取 消 "></td>
    </tr>
  </form>
</table>

<div style="display:none;">
	<div id="show_input_BrandID">
		<select name="BrandID" id="BrandID">
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
	  </select>
	</div>
	<div id="show_input_SiteID">
		<select name="SiteID" id="SiteID">
		<option value="">⊙ 所有分类</option>
		<?php
			$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
			echo ShowTreeMenu($sortarr,0,$in['sid'],1);
		?>
	  </select>
	</div>

	<div id="show_input_Casing">
		<input type="text" name="Casing" id="Casing"  />
	</div>
	<div id="show_input_CommendID">
		<select name="CommendID" id="CommendID">
		<?php
			$attarr = array(
            '0'     => '默认',
			'1'		=> '预售',
			'2'		=> '特价',
			'3'		=> '新款',
			'4'		=> '热销',
			'8'		=> '赠品',
			'9'		=> '缺货',
			);
			foreach($attarr as $k=>$v)
			{
				echo '<option value="'.$k.'" >┠- '.$v.'</option>';
			}
		?>
	  </select>
	</div>
	<div id="show_input_OrderID">
		<input type="text" name="OrderID" id="OrderID"  />
	</div>
	<div id="show_input_Package">
		<input type="text" name="Package" id="Package"  />
	</div>
	<div id="show_input_LibraryDown">
		<input type="text" name="LibraryDown" id="LibraryDown"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" />
	</div>
	<div id="show_input_LibraryUp">
		<input type="text" name="LibraryUp" id="LibraryUp"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="10" />
	</div>
	<div id="show_input_Units">
		<input type="text" name="Units" id="Units"  />
	</div>
	<div id="show_input_ContentPoint">
		<input type="text" name="ContentPoint" id="ContentPoint"  />
	</div>
	<div id="show_input_Deduct">
		<input type="text" name="Deduct" id="Deduct"  /> %
	</div>
</div>
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
?>