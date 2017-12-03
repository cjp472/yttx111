
function checknewpwd(){
	var userpwd=document.getElementById("userpwd").value;
	var newuserpwd=document.getElementById("newuserpwd").value;
	if(userpwd!=newuserpwd){
		$('#newuserpwd').attr('pass','0');
		$('#newuserpwd').attr('msg','两次密码输入不一致');
		
		$("#warning").html('两次密码输入不一致');
		$("#warning").show();
		return;
	}else{
		$('#newuserpwd').attr('pass','1');
		$('#newuserpwd').attr('msg','');
		
		$("#warning").hide();	
		$("#warning").html('');
	}
}

function checkpwd(){
	var userpwd=document.getElementById("userpwd").value;
	if(userpwd==null || userpwd=='' || userpwd==undefined){
		$('#userpwd').attr('pass','0');
		
		$("#warning").html('密码不能为空');
		$("#warning").show();
		return;
	}
	var regpwd=/^[a-zA-Z0-9]{3,18}$/;
	if(!regpwd.test(userpwd)){
		$('#userpwd').attr('pass','0');
		$('#userpwd').attr('msg','密码为3~18位的英文、数字组成');
		$("#warning").html('密码为3~18位的英文、数字组成');
		$("#warning").show();
		return;
	}else{
		$('#userpwd').attr('pass','1');
		$('#userpwd').attr('msg','');
		$("#warning").hide();	
		$("#warning").html('');
	}
}

/* 验证手机 */
function checkmob(){
	var mobile=document.getElementById("mobile").value;
	var regmobile=/^1\d{10}$/g;
	if(!regmobile.test(mobile)){
		$('#mobile').attr('pass','0');
		$('#mobile').attr('msg','未查询出该手机号码');
		
		$("#warning").html('未查询出该手机号码');
		$("#warning").show();
		
		return ;
	}else{
		$.post("do_password_recovery.php",{m:"checkmobile", Mobile: mobile},function(data){
			data = Jtrim(data);
			if(data == "ok"){
				$('#mobile').attr('pass','1');
				
				if($('#code').attr('pass') == '0'){
					$("#warning").html($('#code').data('msg'));
					$("#warning").show();
				}else{
					$("#warning").hide();
					$("#warning").html('');
				}
			}
			else{
				$('#mobile').attr('pass','0');
				$('#mobile').attr('msg','未查询出该手机号码');
				
				$("#warning").html("未查询出该手机号码");
				$("#warning").show();
			}
		});		
	}
}

/* 验证页面信息正确性  */
function checkRequire(from,msg){
	var isrequire = true;
	var isright = true;
	/* 验证必填 */
	$('.' + from).find("input[type='text']").each(function(){
		if($(this).val() == ''){
			if(msg!=1){
				$.blockUI({ message: "<p>您有必填项未填写，请验证后再提交！</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
			}		
			isrequire = false;
			return false;
		}		
	});
	$('.' + from).find("input[type='password']").each(function(){
		if($(this).val() == ''){
			if(msg!=1){
				$.blockUI({ message: "<p>您有必填项未填写，请验证后再提交！</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
			}	
			isrequire = false;
			return false;
		}		
	});
	
	/* 验证正确 */
	$('.' + from).find("small").each(function(){
		if($(this).html() != ''){
			if(msg!=1){
				$.blockUI({ message: "<p>您的输入有误，请验证后再提交！</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
			}	
			isright = false;
			return false;
		}		
	});

	if(isrequire && isright)
		return true;
	else
		return false;
}

/* 下一步 */
function nextStep(step){
	setTimeout(function(){
		if($('#mobile').attr('pass') == '0' || $('#mobile').attr('pass') == '-1'){
			$("#warning").html($('#mobile').attr('msg'));
			$("#warning").show();
			
			return false;
		}else if($('#code').attr('pass') == '0' || $('#code').attr('pass') == '-1'){
			$("#warning").html($('#code').attr('msg'));
			$("#warning").show();
			
			return false;
		}else if($('#smscode').attr('pass') == '0' || $('#smscode').attr('pass') == '-1'){
			$("#warning").html($('#smscode').attr('msg'));
			$("#warning").show();
			
			return false;
		}else{
			$('#loginform').attr("action", "password_recovery.php?step="+step).submit();
		}
	},200);
		
}

/* 短信校验码 */
function secondStep(from,to,step){
	var smscode = $('#smscode').val();
    var mobile = $("#mobile").val();
	if(smscode =='')
		$('#smsmessage').html('× 请输入短信校验码');
	else{
		$.post("do_password_recovery.php",{m:"checksms", SMSCode: smscode,mobile:mobile},function(data){
			data = Jtrim(data);
			if(data == "ok"){
				$('#smsmessage').html('');
				nextStep(from,to,step);
			}
			else{
				$('#smsmessage').html('× ' + data);
				$.blockUI({ message: "<p>"+data+"</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);				
			}
		});	
	}	
}

/*
var code; //在全局 定义验证码  
function createCode() {
  code = "";
  var codeLength = 6;//验证码的长度  
  var checkCode = document.getElementById("checkCode");
  var selectChar = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');//所有候选组成验证码的字符，当然也可以用中文的  
  
  for (var i = 0; i < codeLength; i++) {
    var charIndex = Math.floor(Math.random() * 36);
    code += selectChar[charIndex];
  }
  //alert(code);
  if (checkCode) {
    checkCode.className = "code";
    checkCode.value = code;
  }
}
*/
 
/* 验证图片验证码 */
function validate() {
  var inputCode = $('#code').val();
  
  if(inputCode !=''){
	  $.post("do_password_recovery.php",{m:"checkcode", Code: inputCode},function(data){
			data = Jtrim(data);
			if(data == "ok"){
				$('#code').attr('pass','1');
				
				if($('#mobile').attr('pass') == '0'){
					$("#warning").html($('#mobile').attr('msg'));
					$("#warning").show();
				}else{
					$("#warning").hide();
					$("#warning").html('');
				}
			}
			else{
				$('#siimage').click();
				$('#code').attr('pass','0');
				$('#code').attr('msg','识别码输入错误');
				$("#warning").html('识别码输入错误');
				$("#warning").show();
			}
		});	
  }
  else{
	  $('#code').attr('pass','0');
	  $('#code').attr('msg','识别码不能为空');
	  $("#warning").html('识别码不能为空');
	  $("#warning").show();
  }
	  
}

/* 提交验证短信校验码 */
function checksms(){
	var smscode = $('#smscode').val();
	var mobile = $("#mobile").val();
	if(smscode ==''){
		$('#smscode').attr('pass','0');
	  	$('#smscode').attr('msg','请输入短信校验码');
	  
	  	$("#warning").html('请输入短信校验码');
		$("#warning").show();
	}
	else{
		$.post("do_password_recovery.php",{m:"checksms", SMSCode: smscode,mobile:mobile},function(data){
			data = Jtrim(data);
			if(data == "ok"){
				$('#smscode').attr('pass','1');
			  	$('#smscode').attr('msg','');
			  	
			  	$("#warning").hide();
				$("#warning").html('');
			}
			else{
				$('#smscode').attr('pass','0');
			  	$('#smscode').attr('msg','校验码错误');
			  
			  	$("#warning").html('校验码错误');
				$("#warning").show();
			}
		});	
	}
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
}

var sms_timer = null;

(function(){
    var prev = $.cookie("sms_send_time");
    var now = Date.parse(new Date()) / 1000;
    var _this = $("#get_mobile_code");
    if(now - prev < 60) {
        var delay = 60 - (now - prev);
        _this.addClass("disabled").val(delay + "秒后可重新获取");
        sms_timer = setInterval(function(){
            delay--;
            _this.val(delay + "秒后可重新获取");
            if(delay <= 0) {
                clearInterval(sms_timer);
                _this.removeClass("disabled").val("获取手机校验码");
            }
        },1000);
    }
})();

$(function(){
    (function set_nav(){
        var step = get_param('step');
        step = step || 1;
        for(var i=0;i<step;i++) {
            $(".hint li:eq("+i+")").addClass('border-b')
        }
    });
	
	$("#refresh,#code").on('click',function(){
        var url = $("#code").data('src');
        $("#code").attr('src',url + '?sid=' + Math.random());
	});
	
	//点击获取短信验证码
	$("#btnsettime").on('click',function(){
		//验证前面两个
		if($('#mobile').attr('pass') == '0' || $('#mobile').attr('pass') == '-1'){
			$("#warning").html($('#mobile').attr('msg'));
			$("#warning").show();
		}else if($('#code').attr('pass') == '0' || $('#code').attr('pass') == '-1'){
			$("#warning").html($('#code').attr('msg'));
			$("#warning").show();
		}else{
		    //获取短信验证码  间隔60秒
		    var _this = $(this);
		    var _delay = 60;
		    var mobile = $("#mobile").val();
		    
		    if(_this.hasClass('disabled')) {
		        return false;
		    }else
		    	_this.addClass('disabled');
		    
		    if(parseInt(_this.attr('data-count')) >= 10) {
		    	$("#warning").html('您的短信校验码输错超过十次');
				$("#warning").show();
		        return false;
		    }
		    
			$.post("do_password_recovery.php",{
		        m:'send_sms',
		        mobile:mobile
		    },function(sms_code){
		    	data = Jtrim(sms_code);
		    	if(!/^\d{6}$/.test(sms_code)) {
		            $("#warning").html(sms_code);
					$("#warning").show();
		            
		            _this.removeClass("disabled");
		            return false;
		        }
		        //console.log(sms_code);//FIXME:: 删除
		    	$("#warning").hide();
		    	$("#warning").html('');
				
		        clearInterval(sms_timer);
		        $.cookie("sms_send_time",Date.parse(new Date()) / 1000,{path:'/'});
		        sms_timer = setInterval(function(){
		            _delay--;
		            _this.val(_delay + "秒后可重新获取");
		            if(_delay <= 0) {
		                clearInterval(sms_timer);
		                _this.removeClass("disabled");
		                _this.val("获取手机校验码");
		            }
		        },1000);
		    },'text');
		}
	});
	
	/* 提交密码重置 */
	$('#pawsubmit').on('click',function(){
		//验证前面两个
		if($('#userpwd').attr('pass') == '0' || $('#userpwd').attr('pass') == '-1'){
			$("#warning").html($('#userpwd').attr('msg'));
			$("#warning").show();
		}else if($('#newuserpwd').attr('pass') == '0' || $('#newuserpwd').attr('pass') == '-1'){
			$("#warning").html($('#newuserpwd').attr('msg'));
			$("#warning").show();
		}
		else{
			$.post("do_password_recovery.php",{
                m:"changepaw",
                Password: $('#userpwd').val(),
                NPassword: $("#newuserpwd").val(),
                mobile : $("input[name='mobile']").val()
            },function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$("#warning").html('密码重置成功，正在为您跳转...');
					$("#warning").show();
					setTimeout("window.location.href = 'index.php'",2000);//
				}
				else if(data == "wrong"){
					$("#warning").html('输入的密码不合法，请重新输入！');
					$("#warning").show();
				}
				else{
					$("#warning").html('密码重置 失败，请重试！');
					$("#warning").show();
				}
			});	
		}
	});
	
	
	 $("input[data-back-src]").each(function(){ 
	        var _this = $(this);
	        _this.css({
	            'background-image' : 'url(' + _this.attr('data-back-src') + ')',
	            'background-repeat' : 'no-repeat'
	        });
	        
	        _this.bind('input propertychange',function(){
	        	var css = _this.css('background-image');
	        	
	            if(_this.val() == "" && css =='none') {
	            	_this.css({
	                    'background-image' : 'url('+_this.attr('data-back-src')+')'
	                });
	            } 
	            if(_this.val() != "" && css !='none'){
	            	_this.css({
	                    'background-image' : 'none'
	                });
	            }
	        });
	    });
});

