<?php 
$menu_flag = "notice_list";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT * FROM ".DATATABLE."_pay_notice_type where ID=".intval($in['sid'])." limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/product.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
    <div id="bodycontent">
    <div class="lineblank"></div>   
        <div class="bline" >
       	  <div id="sortleft">
<!-- tree --> 
<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong>提示信息分类</strong>&nbsp;&nbsp;(<a href="notice_type.php" title="新增分类后，刷新后方能看到效果。">刷新分类</a>)</div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT * FROM ".DATATABLE."_pay_notice_type ORDER BY id asc ");
		echo ShowTreeMenuList($sortarr,0);
	?>	
</ul>
 </div>
<!-- tree -->   
       	  </div>


<div id="sortright">
 <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target=""  action="">
 <input type="hidden" name="types" id="types" value="1">
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>新增分类</legend>
       
              <table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                          
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">分类名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_SiteName" id="data_SiteName" style="width:77%;" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">模版类型：</div></td>
                  <td bgcolor="#FFFFFF">
				  
				  <select name="add_notice_view_type" id="add_notice_view_type" class="select2" style="width:77%;">
                    
                   <option value="1">⊙ 通道提示</option>
				   <option value="2">⊙ 平台公告</option>
				   <option value="3">⊙ 常见问题解答</option>
                  </select>
				  </td>
                </tr>
             
            </table>

</fieldset>
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductsort" type="button" class="button_1" id="saveproductsort" value="保 存" onclick="do_save_pay_type();" />
			</div>
            
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>修改分类</legend>
			<table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上级分类：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <input type="hidden" name="edit_SiteID" id="edit_SiteID" value=""/>
                  <select name="edit_ID" id="edit_ID" class="select2" style="width:77%;">
                   
                    <? 
					echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					?>
                  </select>
                  <span class="red">*</span></label></td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">分类名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="edit_SiteName" id="edit_SiteName" style="width:77%;" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
				  <td bgcolor="#F0F0F0"><div align="right">模版类型：</div></td>
                 
                  <td bgcolor="#FFFFFF">
					<select name="save_notice_view_type" id="save_notice_view_type" class="select2" style="width:77%;">
                    
                   <option  value="1">⊙ 通道提示</option>
				   <option  value="2">⊙ 平台公告</option>
				   <option  value="3">⊙ 常见问题解答</option>
					</select>
				  </td>
                </tr>
             
            </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductsort" type="button" class="button_1" id="editproductsort" value="保 存" onclick="do_edit_pay_type();" />
			<input name="saveproductsort" type="button" class="button_3" id="delproductsort" value="删 除" onclick="do_delete_type();" />
			</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
      </form>
        </div>              
          </div>    
        <br style="clear:both;" />
    </div>
  

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
<script type="text/javascript">
	$("#edit_ID").change(function(){
		var view_type=$(this).find("option:selected").attr("data-view");
		$("#save_notice_view_type").find("option ").attr("selected", false);
		$("#save_notice_view_type").find("option[value='"+view_type+"']").attr("selected",true);
	});
	$(function(){
		
		var type=$("#edit_ID").find("option:selected").attr("data-view");
		$("#save_notice_view_type").find("option ").attr("selected", false);
		var asd=$("#save_notice_view_type").find("option[value='"+type+"']").attr("selected",true);
	})
		
</script> 
</body>
</html>
<?
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";
					
		foreach($resultdata as $key => $var)
		{
				if($var['id'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['id']."' data-view='".$var['view_type']."' ".$selectmsg." >".$frontTitleMsg.$var['name']."</option>";	

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

 	function ShowTreeMenuList($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			
					$frontMsg  .= '<li><a href="#editname" ><strong>'.$var['name'].'</strong></a>';
				$frontMsg .= '</li>';
			
		}		
		return $frontMsg;
	}
?>