<?php 
$menu_flag = "system";
include_once ("header.php");
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

<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
    <div id="bodycontent">
    	<div class="lineblank"></div>
         

		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250;<a href="client_area.php">地区管理</a> </div>
          </div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	  <div id="sortleft">
<!-- tree --> 
<div class="leftlist">
<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
</div>
<hr style="clear:both;" />
<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong>地区分类</strong>&nbsp;&nbsp;(<a href="client_area.php" title="新增地区后，刷新后方能看到效果。">刷新分类</a>)</div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		echo ShowTreeMenuList($sortarr,0);
	?>	
</ul>
 </div>
<!-- tree -->  
<div>&nbsp;<br />点击相应的地区<br />在右边进行修改,删除操作</div>
</div>


<div id="sortright">
 <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target=""  action="">
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
		<legend>新增地区</legend>

              <table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上级地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                  <select name="data_AreaParentID" id="data_AreaParentID" class="select2" style="width:78.8%;">
                    <option value="0">⊙ 顶级地区</option>
                    <? 
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
					?>
                  </select>
                  <span class="red">*</span></label></td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">地区名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AreaName" id="data_AreaName" style="width:78%;" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地区描述：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_AreaAbout" cols="70" rows="4" id="data_AreaAbout" style="width:78%;" ></textarea></td>
                </tr>
            </table>

			</fieldset>

            <div class="rightdiv sublink" style="padding-right:20px;">
				<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_sort();" />
			</div>
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>修改地区（先点击左边相应的地区）</legend>
			<table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上级地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <input type="hidden" name="edit_AreaID" id="edit_AreaID" value=""/>
                  <select name="edit_AreaParentID" id="edit_AreaParentID" class="select2" style="width:78.8%;">
                    <option value="0">⊙ 顶级地区</option>
                    <? 
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
					?>
                  </select>
                  <span class="red">*</span></label></td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">地区名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="edit_AreaName" id="edit_AreaName" style="width:78%;" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">拼音码：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="edit_AreaPinyi" id="edit_AreaPinyi" style="width:78%;" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地区描述：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="edit_AreaAbout" cols="70" rows="4" id="edit_AreaAbout" style="width:78%;"></textarea></td>
                </tr>
            </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_sort();" />
			<input name="saveclientid" type="button" class="button_3" id="saveclientid" value="删 除" onclick="do_delete_sort();" />
			</div>
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
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";		
		if($var['AreaParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("--", $layer);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";		$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}	

 	function ShowTreeMenuList($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				if($var['AreaParentID'] == "0")
				{
					$frontMsg  .= '<li><a href="#editname" onclick="set_edit_sort(\''.$var['AreaID'].'\',\''.$var['AreaParentID'].'\',\''.$var['AreaName'].'\',\''.$var['AreaPinyi'].'\',\''.$var['AreaAbout'].'\');" ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="#editname" onclick="set_edit_sort(\''.$var['AreaID'].'\',\''.$var['AreaParentID'].'\',\''.$var['AreaName'].'\',\''.$var['AreaPinyi'].'\',\''.$var['AreaAbout'].'\');" >'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenuList($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>