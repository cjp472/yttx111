<script type="text/javascript">
// 手机号码验证
function checkMobile(strMobile){
	var length = strMobile.length;
	var mobile = /^((1[3|5|8][0-9]{1})+\d{8})$/;
	return length == 11 && mobile.test(strMobile);
}

function closeCoupon(){
	$('#couponcode-box').css('display','none');
	$('#couponcode-box-mini').css('display','block');
	$.get("home.php?close_code=1");
}

function openCoupon(){
	$('#couponcode-box').css('display','block');
	$('#couponcode-box-mini').css('display','none');
	$.get("home.php?close_code=2");
}

function modifyMobile(){ 
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });

	if(!$('#modify-mobile').val()){ 
		$.blockUI({ message: "<p>手机号不能为空！</p>" });
		$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
	}else if(!checkMobile($('#modify-mobile').val())){ 
		$.blockUI({ message: "<p>手机号格式不正确！</p>" });
		$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
	}else{
		$.post("home.php?m=edit_mobile_save&mobile="+$('#modify-mobile').val(),
			function(data){
				data = $.trim(data);
					if(data=="ok")
					{
						$.blockUI({ message: "<p>保存成功!</p>" });
						setTimeout("window.location.reload();",500);
					}else{
						$.blockUI({ message: "<p>保存不成功！</p>" });
						$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
					}			
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}
</script>

<style type="text/css">
#couponcode-box {
	background: url(../images/banner_code.jpg);width:100%; height:150px;margin:5px 0px;
}
	#couponcode-box .couponcode-content {
		width:560px;height:80px;position:relative;left:525px;top:22px;
	}

	#couponcode-box .couponcode-title {
		color:#606060;font-weight:bold;width:390px;
	} 
	#couponcode-box .couponcode-special {
	    color:#f11c54;
	}

	#couponcode-box .couponcode-button{
		background:#f11c54;color:#FFF;display:inline-block;width:82px;padding:2px 4px 2px 12px;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius:5px;margin:5px 0px;
	}

	#couponcode-box .couponcode-button:hover{
		background-color:#d5093f;
		color:#FFF;
		text-decoration:none;
	}

	#couponcode-box .couponcode-submit {
		color:#606060;line-height:25px;
	}
		#couponcode-box .couponcode-submit input[type="text"]{
			border:1px solid #cacaca;padding:3px 4px;width:90px;
		}

		#couponcode-box .couponcode-submit input[type="button"]{
			border:1px solid #f11c54;padding:2px 6px;background:#f11c54;color:#FFF;
		}
			#couponcode-box .couponcode-submit input[type="button"]:hover{
				background:#d5093f;
			}

	#couponcode-box .couponcode-del{
		position:relative;
		left:555px;
		bottom:110px;
		cursor:pointer;
		font-weight:bold;
		color:#f11c54;
	}
		#couponcode-box .couponcode-del:hover{
			color:#d5093f;
		}

	#couponcode-box .couponcode-erwei{ 
		position:relative;
		left:402px;
		bottom:125px;
	}

#couponcode-box-mini {
	background: #fce767;width:100%; height:28px;margin:5px 0px;padding:7px 0 0px 0px;
}
	#couponcode-box-mini .couponcode-title{ padding-left:15px;font-weight:bold;}
	#couponcode-box-mini .couponcode-special {
	    color:#f11c54;
	}
</style>

<div id="couponcode-box" <?php if($sessionCode):?>style="display:none;"<?php endif;?>>
	<div class="couponcode-content">
		<div class="couponcode-title">分享我的优惠码 <span class="couponcode-special"><?php echo $codedata['Code'];?></span> 邀请朋友使用医统天下，即可获得 <span class="couponcode-special">20%现金</span> 反馈，购买者可获 <span class="couponcode-special">八折优惠</span>！惠人又利己，快来参与吧！
		</div>
		<a href="<?php echo $strUrl;?>" class="couponcode-button" target="_blank">查看活动详情</a>
		<div class="couponcode-submit">填写手机号码以便我们将奖金反馈给您 <input type="text" name="modify-mobile" id="modify-mobile" value="<?php echo $codedata['Mobile'];?>" /> <input type="button" id="newbutton" name="newbutton" onclick="modifyMobile();" value="确定" /></div>
		<div class="couponcode-del" onclick="closeCoupon();">x</div>
		<div class="couponcode-erwei"><img width="100" height="100" src="http://www.dhb.hk/index.php?f=code2&code=<?php echo $codedata['Code'];?>" /></div>
	</div>
</div>
<div id="couponcode-box-mini" <?php if(!$sessionCode):?>style="display:none;"<?php endif;?>>
	<div class="couponcode-title">分享我的优惠码 <span class="couponcode-special"><?php echo $codedata['Code'];?></span> 邀请朋友使用医统天下，即可获得 <span class="couponcode-special">20%现金</span> 反馈，购买者可获 <span class="couponcode-special">八折优惠</span>！惠人又利己，快来参与吧！&nbsp;&nbsp;<a href="javascript:void(0);" onclick="openCoupon();" class="couponcode-special">[展开查看详情]</a>
	</div>
</div>