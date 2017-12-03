    <div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
        <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>    	
        <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
	</div>
    
    <div id="copyright"><span class="leftdiv">&nbsp;&nbsp;&nbsp;&nbsp;
	
	</span></div>

<script language="JavaScript" type="text/javascript"> 
<!--
if(typeof(jQuery) == "undefined") document.write('<script src="../scripts/jquery.min.js" type="text/javascript"></script>');
document.write('<script src="../scripts/jquery.messager.js" type="text/javascript"></script>');
function refresh_message()
{
	$.post("do_message.php?rid=<? echo rand(1000,9999);?>",
		{m:"refresh"},
			function(data){
				if(data=="isouttime")
				{
					alert('登陆超时或您的帐号在别的地方登陆了，请重新登陆！');
					top.window.location.href='/index.html';
				}		
		}		
	);
}
//window.setInterval("refresh_message()", 300000);
-->
</script>
<link href="../scripts/select2/select2.min.css" rel="stylesheet" />
<script src="../scripts/select2/select2.min.js"></script>
<script src="../scripts/select2/zh-CN.js"></script>
    <script>
        $(function(){
            if($(".select2").length >0){
                $(".select2").select2();
            }
        });
    </script>