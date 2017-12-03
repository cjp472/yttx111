<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");

$basenumber = 500;

$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and FlagID=0 ");

if(empty($InfoDataNum['allrow'])) exit('数据为空！');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:25px; padding:2px;}
.tc tbody tr td{ background: #ffffff;  height:25px; padding:2px;}
.bluebtn {
    background: #3366CC; color: #FFF; font-weight: bold; font-size: 12px;  padding: .2em .3em !important; padding: .1em .2em; cursor: pointer; height:24px;
}
.darkbtn {
    background: #666666; color: #FFF;font-weight: bold; font-size: 12px; padding: .2em .3em !important; padding: .1em .2em; height:24px; cursor: pointer;
}
input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
-->
</style>
</head>

<body>

<div style="width:100%; height:340px; overflow:auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
        	  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">               
                <thead>
				<tr>
				  <td width="30%" >导出库存数据：</td><td width="40%"><? echo '共 '.$InfoDataNum['allrow'].' 条数据';?></td><td width="30%">&nbsp;操作</td>				  
                </tr>
				</thead>
				 <? 
				 $line = ceil($InfoDataNum['allrow']/$basenumber);
				 for($i=0;$i<$line;$i++)
				 {
				 ?>
				   <tr>
					<td height="30"><strong>&nbsp;&nbsp;数 据：</strong></td>
					<td ><?	
					 $beginn = ($basenumber*$i+1);
					 $endn  = $basenumber*($i+1);
					 if($endn > $InfoDataNum['allrow']) $endn = $InfoDataNum['allrow'];
					 echo '从第 '.$beginn.' 条 到 '.$endn .' 条';?></td>
					<td ><a href="inventory_all_excel.php?b=<? echo $i;?>" target="_blank" > 导出数据 </a></td>
				   </tr>
				<? }?>			
              </table>			
              </form>
       	  </div>
       
</body>
</html>