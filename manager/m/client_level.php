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
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>     
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">药店级别设置</a> </div>
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
<br style="clear:both;" />
</div>
<!-- tree -->
</div>

<div id="sortright">
<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
<?
		$valuearr = get_set_arr('clientlevel');
		if(empty($valuearr) || count($valuearr) < 9)
		{
?>
	 <table width="98%" border="0" cellspacing="8" cellpadding="0">
		<tr>
			 <td width="50%"  align="right"><font color=red>不同种类的商品，可设置不同的药店级别分类。</font>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="newbutton" id="newbutton" value="新增级别分类" class="button_2" onclick="window.location.href='client_level.php?m=new'" style="margin-right:20px;" /></td>
		 </tr>
    </table>	
	<? }?>
	<fieldset title="“*”为必填项！" class="fieldsetstyle">
	<legend>药店级别</legend>
	<?
		if(!empty($valuearr))
		{
			if(count($valuearr, COUNT_RECURSIVE)==count($valuearr))
			{
				$levelarr['A'] = $valuearr;
				$levelarr['A']['id']   = "A";
				$levelarr['A']['name'] = "方式A";
				$levelarr['isdefault']   = "A";
			}else{
				$levelarr = $valuearr;
			}
			$levelarr1 = $levelarr;
			$fruit = array_pop($levelarr1);
			$str1 = ord($fruit['id']); 
			$str1++;
			$str2 = chr($str1);
		}else{
			$str2 = 'A';
			$levelarr = null;
		}
	?>
		<table width="98%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" >
		<tr>
		<td valign="top">
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#cccccc" class="inputstyle">
				 <tr>
       			     <td align="left" bgcolor="#efefef"  width="70%" class="font14" title="级别分类">级别分类</td>
					 <td align="left" bgcolor="#efefef" > 操作 </td>
     			 </tr>
			<?
			if(!empty($levelarr))
			{
				foreach($levelarr as $ke=>$va)
				{
					if($ke=="isdefault") continue;
			?>
				 <tr>
       			     <td align="left" bgcolor="#ffffff" class="bold"><? echo $va['id']."、".$va['name'];?><? if($levelarr['isdefault']==$va['id']) echo '&nbsp;&nbsp;(默认分级)'?></td>
					 <td align="left" bgcolor="#ffffff" >  &nbsp;&nbsp;<a href="client_level.php?m=edit&id=<? echo $va['id'];?>">修改</a>   &nbsp;&nbsp;|&nbsp;&nbsp;    <a href="javascript:void(0)" onclick="delete_level('<? echo $va['id'];?>');">删除</a> </td>
     			 </tr>

				 <tr>
       			     <td align="left" bgcolor="#ffffff" >
					 <ul>
		<?
				foreach($va as $k=>$v)
				{	
					if($k=="id" || $k=="name") continue;
						echo '<li>'.substr($k,6)."、".$v.'</li>'; 
				}
			?>					
					</ul>
					 </td>
					 <td align="left" bgcolor="#ffffff" >  &nbsp;&nbsp; </td>
     			 </tr>
			<?
				}
			}
			?>
			</table>
			
			</td>
			<td width="50%" valign="top">
			<?
			if($in['m']=="edit" && !empty($in['id']))
			{
			?>
              <table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
				 <tr>
       			     <td align="left" bgcolor="#FFFEF5" width="25%" class="font14" title="级别分类">级别分类 <? echo $in['id'];?>：</td>
					 <td align="left" bgcolor="#FFFEF5">  <input type="text" name="data_LevelName" id="data_LevelName" value="<? echo $levelarr[$in['id']]['name'];?>" />&nbsp;<input type="hidden" name="data_LevelID" id="data_LevelID" value="<? echo $in['id'];?>" /><span class="red">*</span></td>
     			 </tr>

			<?php
			$var = $levelarr[$in['id']];
			for($i=1;$i<11;$i++){
			
			?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4"><? echo $i;?></div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_LevelName_<? echo $i;?>" id="data_LevelName_<? echo $i;?>" value="<? echo $var['level_'.$i];?>" />
                    <? if($i==1){?><span class="red">*</span><? }?></label></td>
                </tr>
			  <?
			  }

			 ?>

                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4">&nbsp;&nbsp;</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    默认分类：<input type="checkbox" name="data_isdefault" id="data_isdefault" value="<? echo $in['id'];?>" <? if($levelarr['isdefault']==$in['id']) echo 'checked="checked"';?> style="width:18px;" />
                    </label></td>
                </tr>
              </table>
              <table width="92%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right"><input type="button" name="newbutton" id="newbutton" value=" 保 存 " class="button_1" onclick="savesettype('clientlevel');" style="margin-right:20px;" /></td>
     			 </tr>
     			 <tr>
       			     <td  align="center"><font color=red>没有的级别，保留输入框为空， 最多 10 级，最少 1 级。</font></td>
     			 </tr>
              </table>
			<? }elseif($in['m']=="new"){?>
              <table width="92%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
				 <tr>
       			     <td align="left" bgcolor="#FFFEF5" width="32%" class="font14" title="级别分类">级别分类 <? echo $str2;?>：</td>
					 <td align="left" bgcolor="#FFFEF5">  <input type="text" name="data_LevelName" id="data_LevelName" value="分类<? echo $str2;?>" />&nbsp;<input type="hidden" name="data_LevelID" id="data_LevelID" value="<? echo $str2;?>" /><span class="red">*</span> </td>
     			 </tr>
			  <?
				for($i=1;$i<11;$i++)
				{
				  $keyname = 'level_'.$i;
			 ?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4"><? echo $i;?></div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_LevelName_<? echo $i;?>" id="data_LevelName_<? echo $i;?>" value="" />
                    <? if($i==1){?><span class="red">*</span><? }?></label></td>
                </tr>
				<? }?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4">&nbsp;&nbsp;</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    默认分级：<input type="checkbox" name="data_isdefault" id="data_isdefault" value="<? echo $str2;?>" <? if($str2=="A") echo 'checked="checked"';?> style="width:18px;" />
                    </label></td>
                </tr>
              </table>
              <table width="92%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right"><input type="button" name="newbutton" id="newbutton" value=" 保 存 " class="button_1" onclick="savesettype('clientlevel');" style="margin-right:20px;" /></td>
     			 </tr>
     			 <tr>
       			     <td  align="center"><font color=red>没有的级别，保留输入框为空， 最多 10 级，最少 1 级。</font></td>
     			 </tr>
              </table>
			<? }?>
			</td>			
		</tr>
		</table>
			</fieldset>
              <INPUT TYPE="hidden" name="referer" value ="" >
        </form>

        </div>              
          </div>    
        <br style="clear:both;" />

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>