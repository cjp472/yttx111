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
<script src="../../shop/plugin/layer/layer.js" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>    
 <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">起订金额设置</a></div>
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
			
			<div id="oldinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="ptype" id="ptype" value="<?php echo $in['m'];?>" />
			<fieldset  class="fieldsetstyle">		
			<legend>起订金额设置</legend>
            <br style="clear:both;" />	
		
			<table width="700" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="left">
				<tr>
					
				</tr>
				 
				<?php
				$sql="select OrderAmount from ".DATABASEU.DATATABLE."_order_company where CompanyID =".$_SESSION['uinfo']['ucompany'];
				$res=$db->get_row($sql);;
				$OrderAmount=$res['OrderAmount'];
				
				?>
				<tr>
					
					<td><strong>起订金额设置：</strong>&nbsp;<input type="text" size="2" value="<?php echo $OrderAmount;?>" name="OrderAmount" id="OrderAmount" style="width:200px;" />(元)<span class="red">*</span>  &nbsp;&nbsp;&nbsp;&nbsp;<span class="red">客户订单的最小金额（如果为0则表示不限制）</span></td>
				</tr>
				
				<tr>
					<td height="34"><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="do_Order_Amount();" style="margin-left:120px; width:88px; height:25px;" /></td>
				</tr>
			
			</table>

			<br style="clear:both;" />
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
			
		
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>
    <script type="text/javascript">

	//最小金额
	function do_Order_Amount(){
		var OrderAmount=$("#OrderAmount").val();
		var preg=/^\d+$/;
		if(!preg.test(OrderAmount)){
			layer.open({
				title:'信息提示',
			    content: '请输入正确的整数金额，0 表示订单金额不受限制' //这里content是一个普通的String
			});
			return false;
			
		}
		
		
		$.post("do_consignment.php?m=do_Order_Amount", $("#MainForm").serialize(),
				function(data){
					if(data.status == 1){
						//墨绿深蓝风
						layer.alert(data.message, {
							closeBtn: 0,
							title : '信息提示',
						}, function(){
							window.location.reload();
						});
						
					}else{
						//墨绿深蓝风
						layer.alert(data.message, {
							title : '信息提示',
							closeBtn: 0
						});
					}				
				},'json'		
		);
	
		
	}

	
	</script>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
