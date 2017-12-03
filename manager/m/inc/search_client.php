<!--
<div style="height:40px; clear:both;">
	<div style="z-index:999; position:absolute; margin-top:32px; margin-left:274px; width:30px; text-align:center; color:#fff; font-weight:bold; padding:1px; cursor: pointer; display:none; border:0px solid #ccc;" id="close_return" onclick="close_show_client();" title="关闭"> <img src="./img/CloseIcon.png" alt="关闭" width="25" /></div>
	<div style="z-index:998; position:absolute;margin-top:30px; margin-left:8px; width:300px; overflow:auto; height:280px; line-height:24px; color:#333; background-color:#fff; border:1px solid #ccc; display:none;" id="ckw_return">
	
	</div>
	
	 
	<div style="width:180px; float:left; height:24px; padding-left:4px;"><input type="text" name="ckw" id="ckw" class="inputline" value="<?php if(!empty($in['ckw'])) echo $in['ckw'];?>"  onkeydown="enterSumbit()" onfocus="this.select();" style="width:119px;" /></div>
	<div style="position:relative">
	<div style="width:40px; float:left; height:24px; padding-top:2px;"><input name="csbtn" type="button" class="mainbtn" id="csbtn" value=" 搜 " onclick="show_client()" style="position:absolute;top:1.5px;left:130px;"/></div></div>
	 
</div>
<?php $filename= substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/')+1);?>
<input type="hidden" name="tourl" id="tourl" value="<?php echo $filename;?>" />
-->
<script type="text/javascript">
function show_client()
{
	$('#ckw_return').html('正在查询，请稍候...');
	$('#ckw_return').show();
	$('#close_return').show();
	if($('#ckw').val() == ''){
		$('#ckw_return').html('<p align="center"><br /><br />请输入药店名称、联系人或编号查询!</p>');
	}else{
	$.post("do_client.php", {m:"search_client",ckw: $('#ckw').val(),lurl:$("#tourl").val()},
		function(data){
			if(data.backtype == "ok")
			{					
				$('#ckw_return').html(data.htmldata);
			}else{
				$('#ckw_return').html('<p align="center"><br /><br />无符合条件的数据!</p>');
			}
		},"json");
	}
}

function close_show_client()
{
	$('#ckw_return').hide();
	$('#close_return').hide();
	$('#ckw').val('');
}

function enterSumbit(){
    var event = arguments.callee.caller.arguments[0] || window.event;//消除浏览器差异 
	var lurl = $("#tourl").val();
    if (event.keyCode == 13){  
       show_client();  
    }  
}
</script>