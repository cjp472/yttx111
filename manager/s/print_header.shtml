<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script language=javascript src="js/print.js?v=<? echo VERID;?>"></script>
<style type="text/css">
<!--
-->
</style>
</head>
 
<body> 

	<table width="100%" align="center" border="0" cellspacing="1" cellpadding="1"> 
	  <tr height="38"> 
		<td style="border-bottom:#eeeeee solid 1px;"> 
		  <table border="0" cellspacing="0" cellpadding="0" width="96%"> 
			<tr>			  
				<td style="font-weight:bold; font-size:16px; color:#cc0000; padding-left:10px;">&nbsp;
				<?php
					$ptitle = array(
						'print_order' => '订单',
						'print_return' => '退单',
						'print_consignment' => '发货单',
						'print_storage' => '出库单',
					);
					echo $ptitle[$_GET['u']];
				?> - 打印
				</td> 
			  <td width="70" align="center"> 
				<table width="60" align="center" border="0" cellspacing="0" cellpadding="0" onMouseOver="this.style.backgroundColor='#B5BACE';this.style.border='1px solid #08246B';" onMouseOut="this.style.backgroundColor='';this.style.border='0px';"> 
				  <tr> 
					<td align="center" onclick="frm_print_view();" style="cursor:pointer;"><img src="img/icon-activate.gif" border=0 align="absmiddle">&nbsp; 预 览</td> 
				  </tr> 
				</table> 
			  </td> 		  

			  <td width="70" align="center"> 
				<table width="60" align="center" border="0" cellspacing="0" cellpadding="0" onMouseOver="this.style.backgroundColor='#B5BACE';this.style.border='1px solid #08246B';" onMouseOut="this.style.backgroundColor='';this.style.border='0px';"> 
				  <tr> 
					<td align="center" onclick="frm_print();" style="cursor:pointer;"><img src="img/print.gif" border=0 align="absmiddle">&nbsp;打 印</td> 
				  </tr> 
				</table> 
			  </td>
 
			  
			  <td width="70" align="center"> 
				<table width="60" align="center" border="0" cellspacing="0" cellpadding="0" onMouseOver="this.style.backgroundColor='#B5BACE';this.style.border='1px solid #08246B';" onMouseOut="this.style.backgroundColor='';this.style.border='0px';"> 
				  <tr> 
					<td align="center" onclick="frm_excel();" style="cursor:pointer;"><img src="img/excel.gif" border=0 align="absmiddle">&nbsp;导 出</td> 
				  </tr> 
				</table> 
			  </td>
 			  
			  <td width="70" align="center"> 
				<table width="60" align="center" border="0" cellspacing="0" cellpadding="0" onMouseOver="this.style.backgroundColor='#B5BACE';this.style.border='1px solid #08246B';" onMouseOut="this.style.backgroundColor='';this.style.border='0px';"> 
				  <tr> 
					<td align="center" onclick="frm_reload();" style="cursor:pointer;"><img src="img/reload.gif" border=0 align="absmiddle">&nbsp;刷 新</td> 
				  </tr> 
				</table> 
			  </td> 
			  
			  <td width="70" align="center"> 
				<table width="60" align="center" border="0" cellspacing="0" cellpadding="0" onMouseOver="this.style.backgroundColor='#B5BACE';this.style.border='1px solid #08246B';" onMouseOut="this.style.backgroundColor='';this.style.border='0px';"> 
				  <tr> 
					<td align="center" onclick="frm_close();" style="cursor:pointer;"><img src="img/close.gif" border=0 align="absmiddle">&nbsp;关 闭</td> 
				  </tr> 
				</table> 
			  </td>
			  <td width="10"></td> 
			</tr> 
		  </table> 
		</td> 
	  </tr> 
	</table>
 
</body> 
</html>