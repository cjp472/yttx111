<?php 
$menu_flag = "product";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/product.js?v=43<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>         

		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="locationl"><a name="editname" id="editname"></a><strong>当前位置：</strong><a href="product.php">商品管理</a> &#8250;&#8250; <a href="product_import.php">批量导入</a> </div>
          </div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>批量导入商品</legend>
			<?php if(!erp_is_run($db,$_SESSION['uinfo']['ucompany'])) { ?>
				<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" action="do_upload.php?m=uploadcontentexcel" target="exe_iframe" onsubmit="alert_uploading();" >
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="center" >
                 <!--<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">商品分类：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                  <select name="data_SiteID" id="data_SiteID" class="select2" style="width:544px;">
                    <option value="0">⊙ 请选择您要导入的商品分类</option>
                    <?php
/*					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteNO,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					*/?>
                  </select>
                  <span class="red">*</span></label></td>
				  <td></td>
                </tr>            -->
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">导入EXCEL 表格：</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                   <input type="file" name="import_file" id="import_file" />&nbsp;
                    <span class="red">*</span></label></td>
					<td><a href="<?php echo RESOURCE_URL.'example/content.xls'?>" target="exe_iframe">下载 商品导入模板 Excel 文件格式</a> </td>
                </tr>
                  <tr>
                      <td bgcolor="#F0F0F0"><div align="right">导入模式：</div></td>
                      <td bgcolor="#FFFFFF">
                          <label style="display:inline;">
                              <input type="radio" name="model" value="append" checked="checked" style="height:auto;width:30px;" />追加
                          </label>
                          <label style="display:inline;">
                              <input type="radio" name="model" value="cover" style="height:auto;width:30px;"/>覆盖
                          </label>
                      </td>
                  </tr>

            </table>
			<div align="center"><input name="saveproductsort" type="submit" class="button_1" id="saveproductsort" value=" 上传验证 "  /></div>
			</form>
			<?php }else { ?>
         	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Erp用户请通过接口同步新增商品资料
         	<?php } ?>
		</fieldset>


		<br style="clear:both;" />
		<fieldset  id="yz_div" style="display:none;">
		 <legend>再次验证导入的商品数据</legend>
          <form id="MainFormImport" name="MainFormImport" method="post" action="" target="exe_iframe" >
              <div style="width:1148px;overflow-x:auto;">
                  <div style="width:2500px;">
                      <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
                          <thead>
                          <tr height="28">
                              <td width="3%" bgcolor="#efefef"  >序号</td>
                              <td width="3%" bgcolor="#efefef" align="center">验证</td>
                              <td width="3%" bgcolor="#efefef" align="center">删除</td>
                              <td width="5%" bgcolor="#efefef" >编号</td>
                              <td width="10%" bgcolor="#efefef" >名称</td>
                              <td width="6%" bgcolor="#efefef" >条码</td>
                              <td width="3%" bgcolor="#efefef" >价格1</td>
                              <td width="3%" bgcolor="#efefef" >价格2</td>
                              <td width="2%" bgcolor="#efefef" >单位</td>
                              <td width="3%" bgcolor="#efefef" >包装</td>
                              <td width="8%" bgcolor="#efefef" >颜色</td>
                              <td width="6%" bgcolor="#efefef" >规格</td>
                              <td width="5%" bgcolor="#efefef">型号</td>
                              <td width="5%" bgcolor="#efefef">分类</td>
                              <td width="2%" bgcolor="#efefef" >排序</td>
                              <td width="4%" bgcolor="#efefef">品牌</td>
                              <td width="4%" bgcolor="#efefef">关键词</td>
                              <td width="3%" bgcolor="#efefef">商品类型</td>                              
                              <td width="2%" bgcolor="#efefef">自定义字段1</td>
                              <td width="2%" bgcolor="#efefef">自定义字段2</td>
                              <td width="2%" bgcolor="#efefef">自定义字段3</td>
                              <td width="2%" bgcolor="#efefef">自定义字段4</td>
                              <td width="2%" bgcolor="#efefef">自定义字段5</td>
                              <td width="2%" bgcolor="#efefef">自定义字段6</td>
                              <td width="2%" bgcolor="#efefef">自定义字段7</td>
                              <td width="2%" bgcolor="#efefef">自定义字段8</td>
                              <td width="2%" bgcolor="#efefef">自定义字段9</td>
                              <td width="2%" bgcolor="#efefef">自定义字段10</td>
                              <td width="2%" bgcolor="#efefef">整包装出货数</td>
                          </tr>
                          </thead>
                          <tbody id="showimportdata">

                          </tbody>
                      </table>
                  </div>
              </div>

              <input name="log_id" id="log_id" value="" type="hidden"/>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	 
          <table width="98%" border="0" cellspacing="0" cellpadding="0" >
     		<tr>
       			<td width="50%"><font color=red>只能导入验证通过的商品数据</font></td>
				<td  align="right"><input type="button" name="importtocart" class="button_2" id="importtocart" value=" 提交商品数据 " onclick="subinportcontent();" /></td>			
			</tr>
          </table>

		</fieldset>



		 
        </div>
        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠-";
		$selectmsg = "";
		
		if($var['ParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-2);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

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