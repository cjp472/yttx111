<?php 
$menu_flag = "sms";
$pope	       = "pope_view";
include_once ("header.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/sms.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="sms_phonebook.php">
        		<tr>
					<td width="120" align="center" nowrap="nowrap"><strong>姓名/电话/单位：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="#">通讯录</a> </td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">

		 <div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增联系人 " class="button_2" onclick="javascript:window.location.href='sms_phonebook_add.php'" /></div>
         <hr style="clear:both;" />

<div class="leftlist"> 
<div ><strong><a href="sms_phonebook_client.php">- 药店通讯录</a></strong></div>

<hr style="clear:both;" />
<div ><strong><a href="sms_phonebook.php">联系人分组</a></strong></div>
	<ul>
			<?
			$sinfo = $db->get_results("SELECT SortID,SortName,SortOrder FROM ".DATATABLE."_order_phonebook_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
			for($i=0;$i<count($sinfo);$i++)
			{
				echo '<li>'.($i+1).'、<a href="sms_phonebook.php?sid='.$sinfo[$i]['SortID'].'" >'.$sinfo[$i]['SortName'].'</a></li>';
			}
			?>
	</ul>
</div>
<div style="clear:both;" >&nbsp;</div>
<hr style="clear:both;" />
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 分组管理 " class="button_2" onclick="javascript:window.location.href='sms_phonebook_sort.php'" /></div>

       	  </div>
          <div id="sortright">
<?
	$sqlmsg = '';
	if(!empty($in['sid'])) $sqlmsg  .= " and PhoneSort = ".$in['sid']." ";
	if(!empty($in['kw']))  $sqlmsg .= " and (PhoneName like binary '%%".$in['kw']."%%' or PhoneNumber like binary '%%".$in['kw']."%%' or PhoneBranch like binary '%%".$in['kw']."%%'  ) ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
	
	$datasql   = "SELECT PhoneID,PhoneSort,PhoneName,PhoneNumber,PhoneBranch,PhoneUser FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  ORDER BY PhoneID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  <td width="5%" class="bottomlinebold"><label>&nbsp; </label></td>
                  <td width="8%" class="bottomlinebold">行号</td>
				  <td width="18%" class="bottomlinebold">联系人</td>
				  <td width="20%" class="bottomlinebold">号码</td>
				  <td class="bottomlinebold">单位</td>
                  <td width="12%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>       		
      		<tbody>
<?
	$n=1;
	if(!empty($list_data))
	{
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['PhoneID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['PhoneID'];?>" value="<? echo $lsv['PhoneID'];?>" /></td>
				  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['PhoneName'];?>&nbsp;</td>
				  <td ><? echo $lsv['PhoneNumber'];?>&nbsp;</td>
				  <td ><? echo $lsv['PhoneBranch'];?>&nbsp;</td>       
                  <td align="center"><a href="#" onclick="edit_phonebook_info('<? echo $lsv['PhoneID'];?>');" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_phonebook_delete('<? echo $lsv['PhoneID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="5%"  height="30" class="selectinput">&nbsp;<input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td   class="sublink"><ul><li><a href="javascript:void(0);" onclick="going('phonebook_del');" >批量删除</a></li></ul></td>	     
     			 </tr>
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
       			     <td  align="right"><? echo $page->ShowLink('sms_phonebook.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
      <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">修改联系人信息</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"> <div>
	</div>  
</body>
</html>