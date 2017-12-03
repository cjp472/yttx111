<?php
include_once ("header.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);

$brandID = (int)$in['brandid'];
//读取品牌数据
$datasql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID=".$_SESSION['uinfo']['ucompany']." AND BrandID=".$brandID." Order by BrandID Desc";
$bInfo = $db->get_row($datasql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/product.js?v=<? echo VERID;?>" type="text/javascript"></script>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">var resourceurl = '<?php echo RESOURCE_URL;?>'</script>
<style type="text/css">
#windowForm{
	width:700px;
}
body{
	background-image:none;
	width:auto;
}
#bodycontent{
	width:auto;
}
#edit_BrandName{
	width: 200px;
}
.brand-info-edit{

}
.brand-info-edit tr{
	height:35px;
}
.to-line{
	border-bottom: 1px dashed #dbdbdb;
    margin-top: 10px;
}
#store-logo{
	margin-left: 100px;
    width: 400px;
    height: 30px;
    padding-left: 5px;
    padding-right: 5px;
}
.fieldsetstyle{
	margin-left: 0;
    margin-right: 0;
    width:auto;
    height: 240px;
}
.bluebtn{
	background-image:none;
	background-color:#3574B2;
	height:35px;
	line-height:35px;
}
.bluebtn:hover{
	background-image:none;
	background-color:#5081B2;
	color:#fff;
}
.fieldsetstyle ul{
	margin: 20px auto;
    padding: 0;
}
.fieldsetstyle ul li{
	border: 1px solid #dbdbdb;
    float: left;
    height: 37px;
    margin-top: 5px;
    margin-right: 6px;
}
.fieldsetstyle ul li img{
	margin:3px;;
	width:80px;
	height:32px;
}
#search-logo-box img{
	cursor:pointer;
}
</style>
</head>

<body>

    <div id="bodycontent" style="padding:10px;margin-left: 0px;">
    	<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
    		  <INPUT TYPE="hidden" name="update_id" id="update_id" value ="<?php echo $brandID;?>" />
    		  <INPUT TYPE="hidden" name="brand_logo" id="brand_logo" value ="<?php echo $bInfo['Logo'];?>" />
          	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="brand-info-edit">
                <tr>
                  <td class="bold" width="10%">品牌编号:</td>
                  <td width="24%" ><input type="text" name="edit_BrandNO" id="edit_BrandNO" value="<?php echo $bInfo['BrandNO'];?>" /></td>
				  <td class="bold" width="10%" >品牌名称:</td>  
				  <td width="32%"><input type="text" name="edit_BrandName" id="edit_BrandName" value="<?php echo $bInfo['BrandName'];?>" /></td>         
                  <td><input type="checkbox" name="IsIndex"  value="1" <?php if($bInfo['IsIndex']) echo 'checked';?>/> 首页推荐</td>
                </tr>
                <tr>
                  <td class="bold" width="10%">品牌Logo:</td>
                  <td width="24%" id="brand-logo-box">
                  <?php if($bInfo['Logo']){?>
                  <img src="<?php echo RESOURCE_URL.$bInfo['Logo'];?>" width="80" height="32" />
                  <?php }else{?>
                  	&nbsp;&nbsp;请上传
                  <?php }?>
                  
                  </td>
				  <td class="bold" width="10%" >&nbsp;</td>  
				  <td width="32%"><input type="button" value="上传Logo" -class="bluebtn" onclick="set_brand_logo()" /></td>         
                  <td>&nbsp;</td>
                </tr>
              </table> 
              <br />
              <input type="button" value ="保存" onclick="do_edit_brand();" class="button_2" style="margin-left: 0px;" /> 
    	</form>
    	<div class="to-line"></div>
    	<fieldset class="fieldsetstyle">		
			<legend>品牌库</legend>
			<input type="text" name="store-logo" id="store-logo" placeholder="输入药企关键字检索Logo"  />
			
			<input type="button" name="store-logo" class="mainbtn" id="store-logo-search" style="height:33px;" value="搜 索" />
			<div id="search-logo-box"></div>
		</fieldset> 
    	
    </div>
    
    <div id="windowForm" style="width:500px;">
		<div class="windowHeader">
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"></div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
$(document).ready(function(){
	var box = $('#search-logo-box'),
		button = $('#store-logo-search');

	//搜索
	$('#store-logo-search').bind('click', function(){
		var _key = $('#store-logo').val();
		if(_key == ''){
			alert('请输入查询关键字');
			setTimeout(function(){
				$('#store-logo').focus();
			}, 100);
			return false;
		}

		button.val('搜索中...');
		$.post('do_product.php', 
			{
				m : 'search_branch',
				key : _key
			},function(msg){
				button.val('搜 索');
				if($.trim(msg) == ''){
					box.html('<p align="center">没有找到相关数据...</p>');
				}else{
					var li = '';
					var n=0;
					for(var i in msg){
						if(n ==0){
							li += '<li><img src="'+resourceurl+msg[i]['Logo']+'" onclick="set_search_logo(\''+msg[i]['Logo']+'\')" /></li>';
						}else{
							li += '<li class="lists" style="display:none;" ><img src="'+resourceurl+msg[i]['Logo']+'" onclick="set_search_logo(\''+msg[i]['Logo']+'\')" /></li>';
						}
						
						n++;
					}
					li +='<span id="more" style="cursor:pointer;line-height:50px;" >查看更多</span>';
					box.html('<ul>'+li+'</ul>');
					$("#more").click(function(){
						var list=$(".lists");
						var is_hidden=list.is(":hidden");
						if(is_hidden){
							$(this).html("关闭");
						}else{
							$(this).html("查看更多");
						}
						list.toggle();
					});
				}
				
				
		}, 'json');
	});
	
});
</script>
</body>
</html>