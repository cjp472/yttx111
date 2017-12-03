$(function() {
	$(".btn_sale_submit").click(function(){
        //提交销售员信息
        var fm = $("#open_sale_fm");
        $.post("do_sale.php",fm.serialize(),function(data){
            data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>操作成功!</p>'
                });
                setTimeout(function(){
                    window.location.reload();
                },710);
            } else {
                $.blockUI({
                    message : '<p>'+data+'</p>'
                });
            }
        },'text');
    });

	/**
     * @desc 显示销售员信息
     */
	 $(".showSale").click(function(){
        var sid = $(this).data('sid');
        var name = $(this).data('name');
        var dept = $(this).data('dept');
        var phone = $(this).data('phone');
        var flag = $(this).data('flag');
        var remark = $(this).data('remark');
        var ct = $("#windowContent2");

        ct.find("input[name='ID']").val(sid);
        
        ct.find("input[name='SaleName']").val(name);
        ct.find("select[name='SaleDepartment']").val(dept);
        ct.find("input[name='SalePhone']").val(phone);
        ct.find("select[name='SaleFlag']").val(flag);
        ct.find("textarea[name='Remark']").html(remark);
        
        $.blockUI({
            message : $("#windowForm"),
            css: {
                top: '15%'
            }
        });
    });

	 /**
     * @desc 删除销售员信息
     */
	 $(".delSale").click(function(){
        var sid = $(this).data('sid');
        
        if(sid != 'undefined' && sid !='0')
    	{
        	if(confirm('确认删除吗?'))
        	{
        		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
        		$.post("do_sale.php",
        			{m:"delete", ID: sid},
        			function(data){
        			data = Jtrim(data);
        				if(data == "ok"){
        					$.blockUI({ message: "<p>删除成功!</p>" }); 
        					setTimeout(function(){
        	                    window.location.reload();
        	                },710);
        				}else{
        					$.blockUI({ message: "<p>"+data+"</p>" }); 
        				}					
        			}		
        		);
        	}else{
        		return false;
        	}
        	window.setTimeout($.unblockUI, 1000);
    	}

    });
});

function addSale(){
	var ct = $("#windowContent2");
	ct.find("input[name='ID']").val('');
    
    ct.find("input[name='SaleName']").val('');
    ct.find("select[name='SaleDepartment']").val('');
    ct.find("input[name='SalePhone']").val('');
    ct.find("textarea[name='Remark']").html('');
    
	$.blockUI({
        message : $("#windowForm"),
        css: {
            top: '15%'
        }
    });
}

function showClient(sid)
{
	$('#windowContent2').html('<iframe src="sale_client.php?m=showClient&kw='+sid+
			'" width="100%" marginwidth="0" height="470" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	
	$.blockUI({
        message : $("#windowForm"),
        css: {
            top: '10%'
        }
    }); 
}

function showDetail(m,sid)
{
	$('#windowContent2').html('<iframe src="sale_detail.php?m='+m+'&kw='+sid+
	'" width="100%" marginwidth="0" height="470" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');

	$.blockUI({
		message : $("#windowForm"),
		css: {
		    top: '10%'
		}
	}); 
}

var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
	obj.style.background="#edf3f9";
}
function outStyle(obj)
{
    obj.style.background=old_bg;
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function closewindowui()
{
	$.unblockUI();
}
