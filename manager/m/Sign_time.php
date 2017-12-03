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
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">自动签收时间设置</a></div>
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
			<legend>自动签收时间设置</legend>
            <br style="clear:both;" />	
		
			<table width="700" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="left">
				<tr>
					
				</tr>
				 
				<?php
				$sql="select ExpireDays,AutoCancelTime from ".DATABASEU.DATATABLE."_order_company where CompanyID =".$_SESSION['uinfo']['ucompany'];
				$res=$db->get_row($sql);;
				$ExpireDays=$res['ExpireDays'];
				$hours=$res['AutoCancelTime'];
				if(empty($ExpireDays) || $ExpireDays < 0){
					$ExpireDays=15;
				}
				
				if(empty($hours) || $hours < 0){
					$AutoCancelTime=24*60;
				}else{
					$AutoCancelTime=floor($hours*60);
					if($AutoCancelTime%5 != 0){
						$AutoCancelTime=(($AutoCancelTime+1) % 5) == 0 ? $AutoCancelTime+1 : $AutoCancelTime-1 ;
					}
				}
				
				?>
				<tr>
					
					<td><strong>订单自动签收时间：</strong>&nbsp;<input type="text" size="2" value="<?php echo $ExpireDays;?>" name="out_day" id="out_day" style="width:200px;"  maxlength="2" />(天)<span class="red">*</span>  &nbsp;&nbsp;&nbsp;&nbsp;<span class="red">发货后自动签收时间，默认15天</span></td>
				</tr>
				
				<tr>
					<td height="34"><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="do_Sign_time();" style="margin-left:120px; width:88px; height:25px;" /></td>
				</tr>
			
			</table>

			<br style="clear:both;" />
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
			
			
			<div id="oldinfo" class="line">
			<form id="MainForm2" name="MainForm2" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="ptype" id="ptype" value="<?php echo $in['m'];?>" />
			<fieldset  class="fieldsetstyle">		
			<legend>订单自动取消时间设置</legend>
            <br style="clear:both;" />	
		
			<table width="700" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="left">
				<tr>
					
				</tr>
				 
				<tr>
					
					<td><strong>订单自动取消时间：</strong>&nbsp;<input type="text" size="4" value="<?php echo $AutoCancelTime;?>" name="AutoCancelTime" id="AutoCancelTime" style="width:200px;"  maxlength="4" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />(分钟)<span class="red">*</span><span id="hours">约（<?php echo $hours;?>）小时</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="red">订单自动取消时间，默认24小时</span></td>
				</tr>
				
				<tr>
					<td height="34"><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="do_AutoCancel_time();" style="margin-left:120px; width:88px; height:25px;" /></td>
				</tr>
			
			</table>

			<br style="clear:both;" />
           </fieldset>  
            
			</form>
			
			
			
			
			<br style="clear:both;" />
            </div>
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>
    <script type="text/javascript">

$(function(){
	//分钟转换成小时
	function minutesToHours(){
		var minutes=$("#AutoCancelTime").val();
		if(minutes == '') minutes=0;
		var preg=/^[0-9]{0,}$/;
		if(!preg.test(minutes)) minutes=0;
		minutes=parseInt(minutes);
		var hours=(minutes/60).toFixed(2);
		$("#hours").html("约（"+hours+"）小时");
	}
	$("#AutoCancelTime").bind('input propertychange', function() {
	  minutesToHours();
	});
	
});

	//自动签收
	function do_Sign_time(){
		var outday=$("#out_day").val();
		var preg=/^[1-9]{1}[0-9]{0,}$/;
		if(!preg.test(outday)){
			alert("天数的格式不正确！");
			return false;
			
		}
		if(outday >15){
				alert("天数不能大于15天,请输入1-15之间的数字");
				return false;
		}
		
		$.post("do_consignment.php?m=do_Sign_time", $("#MainForm").serialize(),
				function(data){
					if(data.status == 1){
						window.location.reload();
						alert(data.message);
						
					}else{
						alert(data.message);
					}				
				},'json'		
		);
		
		
		
	}

	//订单自动取消时间设置
	function do_AutoCancel_time(){
		var min=$("#AutoCancelTime").val();
		//alert(min);return;
		if(min == 0){
			alert("格式不正确,请输入5的倍数！");
			return false;
		}
		var preg=/^([1-9][0-9]*)?[05]$/;
		if(!preg.test(min)){
			alert("格式不正确,请输入5的倍数！");
			return false;
			
		}
		if(min >2880){
				alert("自动取消时间不能大于48小时,请输入5-2880之间的数字");
				return false;
		}
		
		$.post("do_consignment.php?m=do_Cancel_time", $("#MainForm2").serialize(),
				function(data){
					//alert(data);return;
					
					if(data.status == 1){
						alert(data.message);
						window.location.reload();
						
					}else{
						alert(data.message);
						
					}				
				},'json'		
		);
		
	}
	
	</script>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
