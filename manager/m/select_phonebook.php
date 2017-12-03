<?php 
$menu_flag = "sms";
$pope	       = "pope_view";
include_once ("header.php");
$sidarr = null;
if(empty($in['selectid']) || $in['selectid']=="undefined")
{
	$in['selectid'] = '';
}
$sidarr = explode(";",$in['selectid']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function sub_add_phonebook()
{
		$('#b4').attr("disabled","disabled");
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php?m=sub_add_phonebook", $("#MainForm").serialize(),
			function(data){
				data.backtype = Jtrim(data.backtype);
				if(data.backtype == "ok")
				{
					$.blockUI({ message: "<p>提交成功!</p>" });
					parent.set_add_phonebook(data.htmldata);
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					window.setTimeout($.unblockUI, 1000); 
					$('#b4').attr("disabled","");
				}else{
					$.blockUI({ message: "<p>"+data.backtype+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					$('#b4').attr("disabled","");
				}	
		},"json");
}

function sub_add_sort()
{
		$('#b1').attr("disabled","disabled");
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php?m=sub_add_sort", $("#MainFormLevel").serialize(),
			function(data){
				data.backtype = Jtrim(data.backtype);
				if(data.backtype == "ok")
				{
					$.blockUI({ message: "<p>提交成功!</p>" });
					parent.set_add_phonebook(data.htmldata);
					window.setTimeout(parent.closewindowui(), 1000);
				}else{
					$.blockUI({ message: "<p>"+data.backtype+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					$('#b1').attr("disabled","");
				}		
		},"json");
}

function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if (e.name != 'chkall' && e.name !='copy')       e.checked = form.chkall.checked; 
	}
}
</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
form{margin:0; padding:0;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
.leftlevel{width:100%; height:380px; overflow:auto;}
.leftlevel dt{font-weight:bold; margin-left:2px; height:28px; clear:both; color:#333333;}
.leftlevel li{ height:24px; clear:both; list-style-type:none; list-style:none;}
.locationli{background:#277DB7; color:#ffffff; padding:2px;}
-->
</style>
</head>

<body> 
		<table width="98%" border="0" cellpadding="4" cellspacing="0" bgcolor="#ffffff"> 
		<tr>
		<td width="30%" valign="top">
		<form id="MainFormLevel" name="MainFormLevel" method="post" action="" target="" >
		<div class="leftlevel">
		<dt style="margin-top:4px;"><input name="selectclientphonebook" value="client" type="checkbox" /><a href="select_phonebook.php?stype=client"> 药店通讯录</a></dt>
		<hr style="clear:both;" />
		<dt ><a href="select_phonebook.php">联系人分组：</a></dt>
		<?
		$sinfo = $db->get_results("SELECT SortID,SortName FROM ".DATATABLE."_order_phonebook_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
		foreach($sinfo as $ke=>$va)
		{
			echo '<li><input name="selectsort[]" value="'.$va['SortID'].'" type="checkbox" /> <a href="select_phonebook.php?sid='.$va['SortID'].'" >'.$va['SortName'].'</a></li>';
		}
		?>
		<div style="margin:4px; height:30px; clear:both; float:left;"><input name="b1" id="b1" type="button" value="提交选中的分组 " onClick="sub_add_sort();" class="bluebtn" title="提交您选择联系人分组" style="width:120px;" /></div>
		</div>
		</form>
		</td>
		<td valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <form id="FormSearch" name="FormSearch" method="post" action="select_phonebook.php"><tr>
       				 <td width="22%" ><strong>姓名/手机：</strong></td>
					 <td width="30%"  height="30" ><input type="text" name="kw" id="kw" style="width:100px;" /></td>   	
					 <td width="30%"  ><select name="stype" id="stype" >
						<option value="client" selected="selected">药店联系人</option>
						<option value="phonebook"> 通讯录联系人 </option>
					</select></td>   	
   			         <td > <input name="searchbutton" type="submit" class="bluebtn" id="searchbutton" value="搜 索" /></td>
     			 </tr>
				 </form>
              </table>
		<form id="MainForm" name="MainForm" method="post" action="" target="" >
        	  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
                  <td width="8%"  bgcolor="#efefef" >选择</td>
                  <td width="22%" bgcolor="#efefef" >联系人</td>
				  <td width="28%" bgcolor="#efefef" >手机</td>
				  <td bgcolor="#efefef">公司</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$sqlmsg = '';
if($in['stype']=="client")
{
	if(!empty($in['kw']))  $sqlmsg .= " and (ClientTrueName like binary '%%".$in['kw']."%%' or ClientCompanyName like binary '%%".$in['kw']."%%' or ClientCompanyPinyi like binary '%%".strtoupper($in['kw'])."%%' or ClientMobile like binary '%%".$in['kw']."%%' ) ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientMobile!='' ".$sqlmsg." and ClientFlag=0 ");
	$page = new ShowPage;
    $page->PageSize = 10;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw'],"selectid"=>$in['selectid'],"stype"=>$in['stype']);      
	
	$datasql   = "SELECT ClientID,ClientTrueName,ClientMobile,ClientCompanyName FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientMobile!='' ".$sqlmsg."  and ClientFlag=0  ORDER BY ClientID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		 {
?>
                <tr id="line_<? echo $lsv['ClientID'];?>"  >
				   <td bgcolor="#FFFFFF" height="22"><input name="selectphonebook[]" value="<? echo $lsv['ClientMobile'];?>" type="checkbox" <? if (in_array($lsv['ClientMobile'], $sidarr)) {	echo 'checked="checked"  disabled="disabled"';}?> /></td>
                  <td bgcolor="#FFFFFF" ><? echo $lsv['ClientTrueName'];?></td>
				  <td bgcolor="#FFFFFF" ><? echo $lsv['ClientMobile'];?></td>
				  <td bgcolor="#FFFFFF" ><? echo $lsv['ClientCompanyName'];?></td>
              </tr>
<? } }else{ ?>
     		  <tr>
       				 <td height="30" colspan="4" align="center" bgcolor="#FFFFFF">无符合条件的内容!</td>
   			  </tr>
<?
}

}else{
	$in['stype'] = "phonebook";
	if(!empty($in['sid']))
	{
		$sqlmsg .= " and PhoneSort = ".intval($in['sid'])." ";
	}else{
		$in['sid'] = '';
	}
	if(!empty($in['kw']))
	{
		$in['kw'] = trim($in['kw']);
		$sqlmsg .= " and (PhoneName like binary '%%".$in['kw']."%%' or PhoneNumber like binary '%%".$in['kw']."%%') ";
	}else{
		$in['kw'] = '';
	}

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 12;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw'],"sid"=>$in['sid'],"selectid"=>$in['selectid'],"stype"=>$in['stype']);        
	
	$datasql   = "SELECT PhoneID,PhoneName,PhoneNumber,PhoneBranch FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  ORDER BY PhoneID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		 {
?>
                <tr id="line_<? echo $lsv['PhoneID'];?>"  >
				   <td bgcolor="#FFFFFF" height="22"><input name="selectphonebook[]" value="<? echo $lsv['PhoneNumber'];?>" type="checkbox" <? if (in_array($lsv['PhoneNumber'], $sidarr)) {	echo 'checked="checked"  disabled="disabled"';}?> /></td>
                  <td bgcolor="#FFFFFF" ><? echo $lsv['PhoneName'];?></td>
				  <td bgcolor="#FFFFFF" ><? echo $lsv['PhoneNumber'];?></td>
				  <td bgcolor="#FFFFFF" ><? echo $lsv['PhoneBranch'];?></td>
              </tr>
<? } }else{ ?>
     		  <tr>
       				 <td height="30" colspan="4" align="center" bgcolor="#FFFFFF">无符合条件的内容!</td>
   			  </tr>
<? }}?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="10%"  height="30" class="selectinput" >&nbsp;<input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="18%" >全选/取消</td>
   			       <td ><input name="b4" id="b4" type="button" value=" 提 交 " onClick="sub_add_phonebook();" class="bluebtn" title="提交您选择联系人"  />
				   </td>
     			 </tr>
              </table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right" height="30"><? echo $page->ShowLink2('select_phonebook.php');?></td>
     			 </tr>
          </table>
			</td>
		</tr>
		</table>
         </form>     
       	  

       
</body>
</html>