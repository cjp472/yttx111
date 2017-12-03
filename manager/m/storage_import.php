<?php 
$menu_flag = "inventory";
$pope	   = "pope_form";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link href="css/showpage.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tbody tr td{ background: #ffffff;}
-->
</style>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	function alert_uploading()
	{
		$.blockUI({ message: "<p>正在上传，请稍候......</p>" });	

		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="storage_input.php">导入库存</a> </div>
   	        </div>            
			
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

		<fieldset class="fieldsetstyle">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" action="do_inventory.php?m=add_implode_storage_save" target="exe_iframe" onsubmit="alert_uploading();" >
			<legend>导入库存</legend>
			<table width="100%" border="0" cellspacing="0" cellpadding="4">
			 
			  <tr>    
				<td width="10%">Excel文件：</td>
				<td ><label>
				  <input type="file" name="import_storagge_file" id="import_storagge_file" style="width:300px;" />&nbsp;(注：Excel文件格式只保留编号，库存两格 <a href="<?php echo RESOURCE_URL;?>example.xls" target="_blank">格式示例</a>)</td>
			  </tr>
			  <tr>
				<td>说明：</td>
				<td ><label>
				  <textarea name="ImportAbout" id="ImportAbout" cols="94%" rows="3"></textarea>
				</label></td>
				</tr>
				<INPUT TYPE="hidden" name="referer" value ="" >
       
			</table>
        <div class="rightdiv sublink" style="padding-right:20px; text-align:right;"><input type="submit" name="newbutton" id="newbutton" value=" 导 入 " class="redbtn"  /></div>
		 </form>
        </fieldset>

		<fieldset class="fieldsetstyle">
			<legend>导入记录</legend>			
		    <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
              <thead>
              <tr>
                <td width="6%" bgcolor="#efefef">ID</td>
                <td width="30%"  bgcolor="#efefef">数据文件</td>
                <td width="14%" bgcolor="#efefef">时间</td>
                <td width="12%" bgcolor="#efefef">操作员</td>
                <td align="center" bgcolor="#efefef">说明</td>
              </tr>
              </thead>
              <tbody >
<?
	$n=1;
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_import where ImportCompany = ".$_SESSION['uinfo']['ucompany']." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_import where ImportCompany = ".$_SESSION['uinfo']['ucompany']."  ORDER BY ImportID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
              <tr>
                <td  bgcolor="#ffffff"><? echo $n++;?></td>
                <td  bgcolor="#ffffff"><a href="<? echo RESOURCE_URL.$lsv['ImportExcelFile'];?>" target="_blank"><? echo $lsv['ImportExcel'];?></a></td>
                <td  bgcolor="#ffffff"><? echo date("Y-m-d H:i",$lsv['ImportDate']);?></td>
                <td  bgcolor="#ffffff"><? echo $lsv['ImportUser'];?></td>
                <td  bgcolor="#ffffff"><? echo $lsv['ImportAbout'];?></td>
              </tr>
<? }}?>
              </tbody>
            </table>
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td height="30" align="right"><? echo $page->ShowLink('storage_import.php');?></td>
     			 </tr>
             </table>
		</fieldset>
            
        </div>        
        <br style="clear:both;" />

    </div>    


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>