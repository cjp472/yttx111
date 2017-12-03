<?php 
$menu_flag = "forum";
$pope	   = "pope_view";
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
<script src="js/forum.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="forum.php">客服</a> &#8250;&#8250; <a href="#">交流工具</a></div>
   	        </div>       
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div ><br />
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="forum_tool.php" title="交流工具">新增交流工具：</a></strong></div>
<ul>
	 <form id="MainForm2" name="MainForm2" method="post" action="" target="exe_iframe" >

	<li><strong>类型：</strong></li>
	<li>
		<select name="ToolType" id="ToolType" style="width:160px;">
           <option value="QQ">⊙ Q Q</option>
		   <option value="BQQ">⊙ 企业QQ</option>
		   <option value="WW">⊙ 阿里旺旺</option>
		   <option value="OTHER">⊙ 其他</option>
		</select>
	</li>
	<li><strong>名称：</strong></li>
	<li><input name="ToolName" id="ToolName" type="text" onfocus="this.select();" style="width:160px;" /></li>
	<li><strong>帐号：</strong></li>
	<li><input name="ToolNO" id="ToolNO" type="text" onfocus="this.select();" style="width:160px;" /></li>
	<li><input name="replybuttom" id="replybuttom" value=" 添 加 " type="button" class="button_1" onclick="SubmitTool()" /></li>
	</form>
</ul>
<br style="clear:both;" />

</div>
<!-- tree -->   
       	</div>

		<div id="sortright">

		<div class="warning">QQ在线状态需要到 QQ官网先开通！开通地址：<a href="http://wp.qq.com" target="_blank">http://wp.qq.com</a> &nbsp;&nbsp;&nbsp;&nbsp; (最多能显示 5 个) </div>

		<div class="line" id="edittools" style="display:none;">
			<form id="MainForm3" name="MainForm3" enctype="multipart/form-data" method="post" target=""  action="">
			<input type="hidden" name="edit_ToolID" id="edit_ToolID" value=""/>
			<fieldset  class="fieldsetstyle">
				<legend>修改信息</legend>
				<table width="100%" border="0" cellspacing="4" cellpadding="0">   
					<tr>
						<td><strong>类型：</strong></td>
						<td>
						<select name="edit_ToolType" id="edit_ToolType">
						   <option value="QQ">⊙ QQ</option>
						   <option value="BQQ">⊙ 企业QQ</option>
						   <option value="WW">⊙ 阿里旺旺</option>
						   <option value="OTHER">⊙ 其他</option>		   
						</select>
						</td>
						<td><strong>名称：</strong></td>
						<td><input name="edit_ToolName" id="edit_ToolName" type="text"onfocus="this.select();" /></td>
						<td><strong>帐号：</strong></td>
						<td><input name="edit_ToolNO" id="edit_ToolNO" type="text" onfocus="this.select();" /></td>
						<td><input name="editbuttom" id="editbuttom" value="保存" type="button" class="bluebtn" onclick="SubmitEditTool()" /></td>
						<td><input name="editbuttomcacel" id="editbuttomcacel" value="取消" type="button" class="bluebtn" onclick="CancelEditTool()" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
			

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr> 
                  <td width="12%" class="bottomlinebold">行号</td>
                  <td width="12%" class="bottomlinebold">类型</td>
                  <td  class="bottomlinebold">名字</td>
                  <td width="20%" class="bottomlinebold" >帐号</td>
				  <td width="14%" class="bottomlinebold"> 在线状态</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?php
	$sqlmsg = '';
	if(!empty($in['ty']))
	{
		$sqlmsg = " and ToolType='".$in['ty']."' ";
	}


	$datasql   = "SELECT * FROM ".DATATABLE."_order_tool where ToolCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY ToolID DESC";
	$list_data = $db->get_results($datasql);
	if(!empty($list_data))
	{
		$n=1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ToolID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td height="32"><? echo $n++;?></td>
				  <td ><? echo $lsv['ToolType'];?></td>
                  <td ><? echo $lsv['ToolName'];?>&nbsp;</td>
                  <td class="font12"><? echo $lsv['ToolNO'];?></td> 
				  <td>
				  <?php if($lsv['ToolType'] == "QQ") {?>
					<a target="blank" href="tencent://message/?uin=<?php echo $lsv['ToolNO'];?>&amp;Site=dhb.hk&amp;Menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=1:<?php echo $lsv['ToolNO'];?>:4" alt="点击发送消息给对方" /></a>
				  <?php }elseif($lsv['ToolType'] == "BQQ"){ ?>
				  <?php if(!empty($lsv['ToolCode'])){
					echo $lsv['ToolCode'];
				  }else{?>
					<a href="http://wpa.qq.com/msgrd?V=1&Uin=<?php echo $lsv['ToolNO'];?>&Exe=QQ&Site=c.dhb.hk&Menu=yes" target="_blank" > <img src="http://im.bizapp.qq.com:8000/zx_qq.gif" border="0" alt="点击发送消息给对方" /></a>
					<?php }?>
				  <?php }elseif($lsv['ToolType'] == "WW"){ ?>
					<a target="_blank" href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo $lsv['ToolNO'];?>&siteid=cntaobao&s=1&charset=utf-8" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?php echo $lsv['ToolNO'];?>&site=cntaobao&s=1&charset=utf-8" alt="点击这里给我发消息" /></a>
				  <?php }?>
				  </td>
                  <td align="center"><a href="javascript:void(0);" onclick="do_set_edit_tool(<? echo "'".$lsv['ToolID']."','".$lsv['ToolType']."','".$lsv['ToolName']."','".$lsv['ToolNO']."'";?>);" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_tool('<? echo $lsv['ToolID'];?>');" ><span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
<?php } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的信息!</td>
       			 </tr>
<?php }?>
 				</tbody>                
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