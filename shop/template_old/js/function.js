/*********/
var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
	obj.style.background="#f7fbff";
}
function outStyle(obj)
{
    obj.style.background=old_bg;
}

function show_hide(ty,idname)
{
	if(ty == "show")
	{
		$("#"+idname).animate({opacity: 'show'}, 'slow');
	}else{
		$("#"+idname).animate({opacity: 'hide'}, 'slow');
	}
}

function control(obj, sType) 
{
	var oDiv = document.getElementById(obj);
	if (sType == 'show') { oDiv.style.display = 'block';}
	if (sType == 'hide') { oDiv.style.display = 'none';}
}

function change_show_type(ty)
{
	document.getElementById("t").value = ty;
	document.getElementById("changetypeform").submit();
	//window.location.href = 'list.php?t='+ty;
}


function change_img_over(idn)
{
	var imgPlus1        = new Image();
	var imgPlus2        = new Image();
	imgPlus1.src        = 'template/default/img/list_type_1.jpg';
	imgPlus2.src        = 'template/default/img/list_type_3.jpg';
	
	if(idn == "1")
	{
		var oDiv  = document.getElementById("imglist_imgid");
		oDiv.src  = imgPlus1.src;
	}else{
		var oDiv  = document.getElementById("textlist_imgid");
		oDiv.src  = imgPlus2.src;
	}
}

function change_img_out(idn)
{
	var imgMinus3        = new Image();
	var imgMinus4        = new Image();
	imgMinus3.src        = 'template/default/img/list_type_0.jpg';
	imgMinus4.src        = 'template/default/img/list_type_2.jpg';
	
	if(idn == "1")
	{
		var oDiv2  = document.getElementById("imglist_imgid");
		oDiv2.src  = imgMinus3.src;
	}else{
		var oDiv2  = document.getElementById("textlist_imgid");
		oDiv2.src  = imgMinus4.src;
	}
}

function hideshow(divid)
{
	$("#"+divid).animate({opacity: 'hide'}, 'slow');
}

function Trim(str)
{ 
	return str.replace(/^\s*|\s*$/g,""); 
}

function show_all(ty)
{
	if(ty=='ALL')
	{
		$("#leibei_id").css('height','auto');
		$("#allbutton").html('<span onclick="show_all(\'NO\');">收起 ∧ </span>');
	}else{
		$("#leibei_id").css('height','55px');
		$("#allbutton").html('<span onclick="show_all(\'ALL\');">展开 ∨ </span>');
	}
}

$(document).ready(function(e) {

	$('.ser_online em').toggle(function () {
		$('.ser_online').addClass("open");
		$.cookie('set_onlinekf','open');
	},
	function () {
		$('.ser_online').removeClass("open");
		$.cookie('set_onlinekf','close');
	});

	if($.cookie('set_onlinekf')!='open')
	{
		$('.ser_online').removeClass("open");
	}else{
		$('.ser_online').addClass("open");
	}

});


jQuery.cookie=function(name,value,options){  
    if(typeof value!='undefined'){  
        options=options||{};  
        if(value===null){  
            value='';  
            options.expires=-1;  
        }  
        var expires='';  
        if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){  
             var date;  
            if(typeof options.expires=='number'){  
                date=new Date();  
                date.setTime(date.getTime()+(options.expires * 24 * 60 * 60 * 1000));  
             }else{   
                date=options.expires;  
            }  
            expires=';expires='+date.toUTCString();  
         }  
        var path=options.path?';path='+options.path:'';  
        var domain=options.domain?';domain='+options.domain:'';  
        var secure=options.secure?';secure':'';  
        document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');  
     }else{  
        var cookieValue=null;  
        if(document.cookie&&document.cookie!=''){  
            var cookies=document.cookie.split(';');  
            for(var i=0;i<cookies.length;i++){  
                var cookie=jQuery.trim(cookies[i]);  
                if(cookie.substring(0,name.length+1)==(name+'=')){  
                    cookieValue=decodeURIComponent(cookie.substring(name.length+1));  
                    break;  
                }  
            }  
        }  
        return cookieValue;  
    }  
};

//后台检测
function backendAction(){
	//检查支付是否完成
	var url = "my.php?m=execute&jp=backend_check&"+Math.random();
	setInterval(function(){$.getJSONP(url, backend_check)}, 1500);
}

function backend_check(info){
	if(info) window.location = 'yjffront.php?m=show';
}

//后台检测
function backendActionOrder(){
	//检查开户是否完成
	window.setInterval("refresh_all()",2000);
}

function refresh_all()
{
	$.post(
			"order.php",
			{
				m: "setaccount"
			},
			function(data){
				data = Trim(data);
				if(data=="ok"){
					window.location.reload();//刷新当前页面.
				}
			}
		)
}


/********************* 易极付 ************************************/
//获取对账记录
window.pageSize = 2;
function getPayWithdraw(index, initPage){
	var sdate = $("input[name='sdate']").val(),
		edate = $("input[name='edate']").val();
	
/*	var sdate = '2014-08-07',
		edate = '2014-09-07';*/
	
	var drawLoad = $("#draw-load");
		drawLoad.css('visibility','visible');
		
	var currPage = index || 1;

		$.post(
				"my.php",
				{
					m: "getSearInfo",
					sdate: sdate,
					edate: edate,
					pageSize: window.pageSize,
					currPage: currPage
				},
				function(msg){
					drawLoad.css('visibility', 'hidden');
					
					//如果使用JSON分页
//					$("body").data(msg);
					
					//设置头信息
					$("#total-count").html(msg.count);				//总记录数
					$("#total-amounts").html(msg.amounts.toFixed(2));			//本金总金额
					$("#total-amountsIn").html(msg.amountsIn.toFixed(2));		//总金额
					$("#total-charges").html(msg.charges.toFixed(2));			//总手续费
					
					if(msg == null) return false;
					
					//设置消息体(这块看起来有点眼花，要优化下)
					var sbody	= '';
					for(var i in msg.withdrawInfos){
						
						sbody += '<tr>';
						sbody += '<td width="80" class="border-none">提现时间：</td>';
						sbody += '<td width="264" id="log-payTime">'+msg.withdrawInfos[i]['payTime']+'</td>';
						sbody += '<td width="100" class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;提现账户ID：</td>';
						sbody += '<td id="log-userId">'+msg.withdrawInfos[i]['userId']+'</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td class="border-none">提现流水号：</td>';
						sbody += '<td id="log-payNo">'+msg.withdrawInfos[i]['payNo']+'</td>';
						sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;资金账号：</td>';
						sbody += '<td id="log-accountNo">'+msg.withdrawInfos[i]['accountNo']+'</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td class="border-none">资金账号：</td>';
						sbody += '<td id="log-accountNo">'+msg.withdrawInfos[i]['accountNo']+'</td>';
						sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;资金账户名：</td>';
						sbody += '<td id="log-accountName">'+msg.withdrawInfos[i]['accountName']+'</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td class="border-none">银行账号名：</td>';
						sbody += '<td id="log-bankName">'+msg.withdrawInfos[i]['bankName']+'</td>';
						sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;银行卡号：</td>';
						sbody += '<td id="log-bankAccountNo">'+msg.withdrawInfos[i]['bankAccountNo']+'</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td class="border-none">提现金额：</td>';
						sbody += '<td id="log-amout">¥：'+msg.withdrawInfos[i]['amout'].toFixed(2)+'</td>';
						sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;提现手续费：</td>';
						sbody += '<td id="log-charge">¥：'+msg.withdrawInfos[i]['charge'].toFixed(2)+'</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td class="border-none">提现总金额：</td>';
						sbody += '<td id="log-amountIn">¥：'+msg.withdrawInfos[i]['amountIn'].toFixed(2)+'</td>';
						sbody += '<td >&nbsp;</td>';
						sbody += '<td id="log-amountIn">&nbsp;</td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td colspan="4" style="border-bottom:dashed 1px #A3A1A1 !important; height:25px"></td>';
						sbody += '</tr>';
						sbody += '<tr>';
						sbody += '<td colspan="4" style=" border-bottom:none; height:15px"></td>';
						sbody += '</tr>';
					}
					
					$("#log-draw").html(sbody);
					!initPage && initPagination(msg.count);
					
				},
				'json'
			);
}

//创建分页
function initPagination(num_entries) {

	$("#yopenapi-page").pagination(num_entries, {
		num_edge_entries: 1, //边缘页数
		num_display_entries: 5, //主体页数
		items_per_page: window.pageSize, //每页显示1项
		prev_text: "前一页",
		next_text: "后一页",
		callback:createDrawHtml
	});
	
};

//生成提现html
function createDrawHtml(pageIndex, jq){
	
	
	pageIndex && getPayWithdraw(pageIndex, true);
	return false;
	
	
	//json分页处理
	//获取缓存数据
	var msg = $("body").data();
	
	var drawLoad = $("#draw-load");
		drawLoad.css('visibility','visible');
	
	var cFlage	= 0,
		cLength = (msg.withdrawInfos).length,
		start	= window.pageSize*pageIndex; 
	
	//设置头信息
	$("#total-count").html(cLength);				//总记录数
	$("#total-amounts").html(msg.amounts);			//本金总金额
	$("#total-amountsIn").html(msg.amountsIn);		//总金额
	$("#total-charges").html(msg.charges);			//总手续费
	
	//设置消息体(这块看起来有点眼花，要优化下)
	var sbody	= '';
	for(i = start; i < cLength; i++){
		cFlage++;
		if(cFlage > window.pageSize) break;
		
		sbody += '<tr>';
		sbody += '<td width="80" class="border-none">提现时间：</td>';
		sbody += '<td width="264" id="log-payTime">'+msg.withdrawInfos[i]['payTime']+'</td>';
		sbody += '<td width="100" class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;提现账户ID：</td>';
		sbody += '<td id="log-userId">'+msg.withdrawInfos[i]['userId']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td class="border-none">提现流水号：</td>';
		sbody += '<td id="log-payNo">'+msg.withdrawInfos[i]['payNo']+'</td>';
		sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;资金账号：</td>';
		sbody += '<td id="log-accountNo">'+msg.withdrawInfos[i]['accountNo']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td class="border-none">资金账号：</td>';
		sbody += '<td id="log-accountNo">'+msg.withdrawInfos[i]['accountNo']+'</td>';
		sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;资金账户名：</td>';
		sbody += '<td id="log-accountName">'+msg.withdrawInfos[i]['accountName']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td class="border-none">银行账号名：</td>';
		sbody += '<td id="log-bankName">'+msg.withdrawInfos[i]['bankName']+'</td>';
		sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;银行卡号：</td>';
		sbody += '<td id="log-bankAccountNo">'+msg.withdrawInfos[i]['bankAccountNo']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td class="border-none">提现金额：</td>';
		sbody += '<td id="log-amout">'+msg.withdrawInfos[i]['amout']+'</td>';
		sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;提现手续费：</td>';
		sbody += '<td id="log-charge">'+msg.withdrawInfos[i]['charge']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td class="border-none">提现总金额：</td>';
		sbody += '<td id="log-amountIn">'+msg.withdrawInfos[i]['amountIn']+'</td>';
		sbody += '<td class="border-none">&nbsp;&nbsp;&nbsp;&nbsp;提现总金额：</td>';
		sbody += '<td id="log-amountIn">'+msg.withdrawInfos[i]['amountIn']+'</td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td colspan="4" style="border-bottom:dashed 1px #A3A1A1 !important; height:25px"></td>';
		sbody += '</tr>';
		sbody += '<tr>';
		sbody += '<td colspan="4" style=" border-bottom:none; height:15px"></td>';
		sbody += '</tr>';
	}
	
	$("#log-draw").html(sbody);
	
	//页面重定位
	var offset = $("#location").offset(),
		top = offset.top;
		
		setTimeout(function(){$(window).scrollTop(top);}, 2);
		setTimeout(function(){drawLoad.css('visibility', 'hidden');}, 200);
}





