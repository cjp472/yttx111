<?php 
$menu_flag = "sms";
$pope	   = "pope_view";
include_once ("header.php");
$sidarr = null;
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
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
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
              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <form id="FormSearch" name="FormSearch" method="post" action="select_template.php"><tr>
       				 <td width="10%" align="center"><strong>搜索：</strong></td>
					 <td width="25%"  height="30" ><input type="text" name="kw" id="kw" style="width:120px;" /></td> 
					 <td width="25%">
    	  <select name="sid" id="sid"  >
		  <option value="">请选择模板分类</option>
			<?
			$sinfo = $db->get_results("SELECT SortID,SortName FROM ".DATATABLE."_order_template_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
			if(!empty($sinfo))
			{
				foreach($sinfo as $phvar)
				{
					echo '<option value="'.$phvar['SortID'].'">'.$phvar['SortName'].'</option>';
				}
			}			
			?>
  	      </select>
					 </td>
   			         <td > <input name="searchbutton" type="submit" class="bluebtn" id="searchbutton" value="搜 索" /></td>
     			 </tr>
				 </form>
              </table>
			<form id="MainForm" name="MainForm" method="post" action="" target="" >
        	  <table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
                  <td width="10%"  bgcolor="#efefef" >行号</td>
                  <td bgcolor="#efefef" >模板内容</td>
				  <td width="12%"  bgcolor="#efefef" align="center" >操作</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['sid']))
	{
		$sqlmsg .= " and TemplateSort = ".intval($in['sid'])." ";
	}else{
		$in['sid'] = '';
	}
	if(!empty($in['kw']))
	{
		$in['kw'] = trim($in['kw']);
		$sqlmsg .= " and TemplateContent like binary '%%".$in['kw']."%%'  ";
	}else{
		$in['kw'] = '';
	}

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_sms_template where TemplateCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 12;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
	
	$datasql   = "SELECT TemplateID,TemplateContent FROM ".DATATABLE."_order_sms_template where TemplateCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg."  ORDER BY TemplateID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
			$lsv['TemplateContent'] = str_replace("'","",$lsv['TemplateContent']);
			$lsv['TemplateContent'] = str_replace('"','“',$lsv['TemplateContent']);
?>
                <tr id="line_<? echo $lsv['TemplateID'];?>"  >
				   <td bgcolor="#FFFFFF" height="22"><? echo $n++;?></td>
                  <td bgcolor="#FFFFFF" ><? echo $lsv['TemplateContent'];?></td>
				  <td align="center" bgcolor="#FFFFFF" >[<a href="javascript:void(0);" onclick="parent.insert_template('<? echo $lsv['TemplateContent'];?>')">插入</a>]</td>
              </tr>
<? } }else{ ?>
     		  <tr>
       				 <td height="30" colspan="3" align="center" bgcolor="#FFFFFF">无符合条件的内容!</td>
   			  </tr>
<? }?>
 				</tbody>                
              </table>

			<table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right" height="30"><? echo $page->ShowLink2('select_template.php');?></td>
     			 </tr>
          </table>

         </form> 
</body>
</html>