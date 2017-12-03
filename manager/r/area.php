<?php 
include_once ("header.php");
$menu_flag = "manager";

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

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

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

		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250;<a href="area.php">地区管理</a> </div>
          </div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	  <div id="sortleft">
<!-- tree --> 

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong>地区分类</strong>&nbsp;&nbsp;(<a href="area.php" title="新增地区后，刷新后方能看到效果。">刷新分类</a>)</div>  	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="地区"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName,AreaAbout FROM ".DATABASEU.DATATABLE."_order_city ORDER BY AreaParent asc,AreaOrder DESC,AreaID ASC ");
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
                  <select name="data_AreaParent" id="data_AreaParent">
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
                    <input type="text" name="data_AreaName" id="data_AreaName" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地区描述：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_AreaAbout" cols="70" rows="4" id="data_AreaAbout" ></textarea></td>
                </tr>
            </table>

</fieldset>
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_sort();" />

			</div>

            
<fieldset  class="fieldsetstyle">
			<legend>修改地区（先点击左边相应的地区）</legend>
			<table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上级地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <input type="hidden" name="edit_AreaID" id="edit_AreaID" value=""/>
                  <select name="edit_AreaParent" id="edit_AreaParent">
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
                    <input type="text" name="edit_AreaName" id="edit_AreaName" />
                    <span class="red">*</span></label></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地区描述：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="edit_AreaAbout" cols="70" rows="4" id="edit_AreaAbout" ></textarea></td>
                </tr>
            </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveeditid" type="button" class="button_1" id="saveeditid" value="保 存" onclick="do_save_edit_sort();" />
			<input name="savedelid" type="button" class="button_3" id="savedelid" value="删 除" onclick="do_delete_sort();" />
			</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
      </form>
        </div>              
          </div>    
        <br style="clear:both;" />

    </div>    
<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	
				$frontMsg2  = "";
				//$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
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
			if($var['AreaParent'] == $p_id)
			{
				if($var['AreaParent'] == "0")
				{
					$frontMsg  .= '<li><a href="#editname" onclick="set_edit_sort(\''.$var['AreaID'].'\',\''.$var['AreaParent'].'\',\''.$var['AreaName'].'\',\''.$var['AreaAbout'].'\');" ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="#editname" onclick="set_edit_sort(\''.$var['AreaID'].'\',\''.$var['AreaParent'].'\',\''.$var['AreaName'].'\',\''.$var['AreaAbout'].'\');" >'.$var['AreaName'].'</a>';
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