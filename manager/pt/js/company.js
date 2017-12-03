/**
 * 新增线下转账记录
 * @returns {boolean}
 */
function do_companyorder_pay()
{
	var ct = $("#windowForm");
	var pid = ct.find("input[name='payID']").val();
	var account = ct.find("select[name='account']").val();
    var remark = ct.find("textarea[name='remark']").val();
	if(account == "")
	{
		$.blockUI({ message: "<p>请选择转入账号!</p>" });
        return false;
	}
    $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
    $.post("do_company.php",
        {m:"do_companyorder_pay", ID: pid, account: account,remark:remark},
        function(data){
            data = Jtrim(data);
            if(data == "ok"){
                $.blockUI({ message: "<p>保存成功!</p>" });
                $('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
            }else{
                $.blockUI({ message: "<p>"+data+"</p>" });
                $('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
            }
        }
    );
	window.setTimeout($.unblockUI, 2000); 
}

//执行开通
function do_companyorder_status(pid)
{
	if(confirm('确认开通吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_company.php",
			{m:"do_companyorder_status", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}					
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000); 
}

/**
 * 执行确认并开通
 * @param order_id
 * @param type
 * @author hxtgirq
 * @since 2015-08-14
 */
function sureOpenForm(order_id,type) {
    if(type == 'renewals') {
        type = 'product';
    }
    var win = $("#windowForm_" + type);
    $.post("do_company.php",{
        m:'get_order_info',
        order_id:order_id
    },function(data){
        if(data.stream_info == null) {
            $.blockUI({
                message : '<p>请先添加付款信息!</p>'
            });
            return false;
        }

        var line = data.stream_info.trade_no;
        switch(data.stream_info.pay_away) {
            case 'alipay' : line = '线上支付宝'; break;
            case 'allinpay' : line = '线上通联支付'; break;
            default:
                line = data.stream_info.trade_no;
                break;
        }
        win.find("input[name='order_id']").val(data.order_info.id);
        win.find("[data-company]").html(data.company_info.CompanyName);
        win.find("[data-total]").html(data.order_info.total);
        win.find("[data-integral]").html(data.order_info.integral);
        win.find("[data-account]").html(line);

        win.find("[data-surplus-sms]").html(data.company_info.CS_SmsNumber + " 条");
        switch(data.order_info.type) {
            case 'renewals': //系统续费和系统开通走同一个流程
            case 'product':
                win.find("[data-time]").html((data.order_info.data.buy_time || 0 ) + ' 年');
                win.find("[data-gift-time]").html((data.order_info.data.gift_time || 0 ) + " 个月");
                win.find("[data-user-number]").html(data.company_info.CS_Number);
                win.find("[data-old-end-date]").html(data.company_info.CS_EndDate);
                break;
            case 'sms':
                win.find("[data-sms]").val(data.order_info.data.buy_sms);
                break;
            case 'erp':
                win.find("[data-time]").html(data.order_info.data.buy_time + ' 年');
                break;
            case 'weixin':
                win.find("[data-time]").html(data.order_info.data.buy_time + ' 次');
                break;
            default:
                break;
        }
        win.find("[data-input-sms]").val(data.order_info.data.gift_sms);
        win.find("[data-result-end-date]").val(data.company_info.result_end_date);//.datepicker({changeMonth: true,	changeYear: true});
        win.find("[data-result-end-date]").datetimepicker();
        $("#ui-datepicker-div").css({
            'z-index' : 9999
        });
        win.find("[data-remark]").html(data.order_info.remark);

        $.blockUI({
            message : win ,
            css : {
                top:'15%'
            }
        });

    },'json');

}

/**
 * 提交确认并开通请求
 * @param type (product,sms,weixin,erp)
 */
function sureAndOpen(type) {
    var fm = $("#line_fm_" + type);
    if(confirm("确认到账立即开通吗?")) {
        $.post(fm.attr('action'),fm.serialize(),function(data){
            data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>确认到账并开通操作成功!</p>'
                });
                setTimeout(function(){
                    location.reload();
                    $.unblockUI();
                },710);
            } else {
                $.blockUI({
                    message : '<p>'+data+'</p>'
                });
            }
        },'text');
    }
}

function do_companyinvoice_edit()
{
	var ct = $("#windowContent");
	var pid = ct.find("input[name='invoiceID']").val();
	var account = ct.find("input[name='invoice_no']").val();
	var to_time = ct.find("input[name='to_time']").val();
	if(account == "")
	{
		$.blockUI({ message: "<p>请输入开票票号！</p>" });
	}
	else if(to_time == "")
	{
		$.blockUI({ message: "<p>请输入开票日期！</p>" });
	}
	else if(confirm('确认开票吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_company.php",
			{m:"do_companyinvoice_edit", ID: pid, account: account, to_time: to_time},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_invoice.php');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_invoice.php');
				}					
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000); 
}

/**
 * 显示线下转账表单
 * @param pid
 * @param company
 * @param total
 */
function showPay(pid,company,total)
{
	var ct = $("#windowForm");
	ct.find("span[data-company]").html(company);
	ct.find("span[data-total]").html(total);
	ct.find("select[name='account']").val('');
	ct.find("input[name='payID']").val(pid);
    $.blockUI({
        message : $("#windowForm"),
        css : {
            top:'15%'
        }
    });
}

/**
 * 显示订单支付信息
 * @param pid
 * @param company
 */
function showPayInfo(pid,company) {
    var ct = $("#windowForm_info");

    $.post("do_company.php",{
        m:'get_stream_info',
        id:pid
    },function(data){
        if(data == null) {
            $.blockUI({
                message : '<p>当前订单暂无付款信息!</p>'
            });
            return false;
        }
        var line = "";
        switch(data.pay_away) {
            case 'alipay':
                line = "支付宝线上付款";
                break;
            case 'allinpay':
                line = "通联线上支付";
                break;
            default:
                line = data.trade_no;
                break;
        }
        
        var payStatus = '';
        if(data.pay_status == '1'){
        	payStatus = '<span class="title_green_w">已到账</span>&nbsp;(操作人：'+(data.username ? data.username : "自动确认")+')';
        }else{
        	payStatus = '<span class="red">待确认</span>';
        }
        
        ct.find("span[data-pay-status]").html(payStatus);
        ct.find("span[data-company]").html(company);
        ct.find("span[data-total]").html(data.amount);
        ct.find("span[data-line]").html(line);
        ct.find("span[data-pay-time]").html(data.time);
        ct.find("span[data-to-time]").html(data.to_time);
        ct.find("span[data-stream_no]").html(data.stream_no);
        ct.find("span[data-trade_no]").html(data.trade_no);
        ct.find("span[data-remark]").html(data.remark);
        $.blockUI({
            message : $("#windowForm_info"),
            css : {
                top:'15%'
            }
        });
    },'json');

}

function showInvoice(pid,company,total)
{
	var ct = $("#windowContent");
	ct.find("span[data-company]").html(company);
	ct.find("span[data-total]").html(total);
	ct.find("input[name='invoice_no']").val('');
	var myDate = new Date();
	var year = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
	var month = myDate.getMonth()+1;       //获取当前月份(0-11,0代表1月)
	var day = myDate.getDate();        //获取当前日(1-31)

	ct.find("input[name='to_time']").val(year+'-'+month+'-'+day);
	ct.find("input[name='invoiceID']").val(pid);
    $.blockUI({ message : $("#windowForm")});
}

function closewindowui()
{
	$.unblockUI();
}