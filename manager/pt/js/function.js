/**
 * 模拟PHP获取get参数
 * @param url 传入的URL
 * @param p 分隔符
 * @returns {{}}
 */
function $_GET(url,p){
    if(p == '') p =='?';
    var u = url.split(p);
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = '';
            for(var k=1;k< j.length;k++){
                get[j[0]] += j[k]+"=";
            }
            get[j[0]] = trim(get[j[0]],'=');
        }
        return get;
    } else {
        return {};
    }
}
//去除字符串两边空格的函数
//参数：mystr传入的字符串
//参数：str 左右两边需要去除的字符
//返回：字符串mystr
function trim(mystr,str){
    while ((mystr.indexOf(str)==0) && (mystr.length>1)){
        mystr=mystr.substring(1,mystr.length);
    }//去除前面空格
    while ((mystr.lastIndexOf(str)==mystr.length-1)&&(mystr.length>1)){
        mystr=mystr.substring(0,mystr.length-1);
    }//去除后面空格
    if (mystr==str){
        mystr="";
    }
    return mystr;
}
/**
 *
 * @param param 需要生成的一个二维码的数据，当想要生成URL时一定要带http:// 否则只是文本格式来显示
 * @returns {boolean}
 */
function createWeixin(param ,size){
    $(".dhbCreateWeixin").children().remove();
    $(".dhbCreateWeixin").qrcode({
        // render method: 'canvas', 'image' or 'div'
        render: 'image',
        "size": size,
        "color": "#3a3",
        "text": param
    });
    $(".dhbBox").show();
    return false;
}
/**
 * 联系人
 */

function addcontact()
{
	var name = $("#ContactName").val();
	var job = $("#ContactJob").val();
	var phone = $("#ContactPhone").val();
	var mobile = $("#ContactMobile").val();
	var qq = $("#ContactQQ").val();
	var email = $("#ContactEmail").val();
	var companyid = $("#savecontact").attr('cdata');

	if(name == ''){
		$.blockUI({ message: "<p>请输入联系人</p>" }); 
	}
	else if(mobile == '' && phone == ''){
		$.blockUI({ message: "<p>电话和手机至少输入一个</p>" }); 
	}
	else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_use.php",
			{m:"save", ID:$('#savecontact').attr('data'),CompanyID: companyid,ContactName:name,ContactJob:job,ContactPhone:phone,ContactMobile:mobile,ContactQQ:qq,ContactEmail:email},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					location.reload(true);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000); 
}

function savecontacts()
{
	var name = $("#ContactName").val();
	var job = $("#ContactJob").val();
	var phone = $("#ContactPhone").val();
	var mobile = $("#ContactMobile").val();
	var qq = $("#ContactQQ").val();
	var email = $("#ContactEmail").val();
	var companyid = $("#CompanyID").val()?$("#CompanyID").val():$("#savecontact").attr('cdata');

	if(name == '')
	{
		$.blockUI({ message: "<p>请输入联系人</p>" }); 
	}
	else if(phone == '' && mobile == '')
	{
		$.blockUI({ message: "<p>电话和手机至少输入一个</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_use.php",
			{m:"save", ID:$('#savecontact').attr('data'),CompanyID: companyid,ContactName:name,ContactJob:job,ContactPhone:phone,ContactMobile:mobile,ContactQQ:qq,ContactEmail:email},
			function(data){
			data = Jtrim(data);
				if(data == "ok")
				{
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					if($('#savecontact').attr('cdata')!=='')
					{
						history.go(-1);
					}
					else
					{
						$("#CompanyID").val("").trigger("change");
						$("#Job").val("").trigger("change");
						$('#resetcompanyid').click();
					}
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000); 
}

function delcontact(id)
{
	var companyid = $("#savecontact ").attr('cdata');
	if(id =='' || companyid == '')
	{
		$.blockUI({ message: "<p>参数错误，请刷新重试</p>" }); 
	}
	else
	{
		if(confirm('确认删除吗?'))
		{
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_use.php",
				{m:"delete", CompanyID: companyid,ContactID:id},
				function(data){
					data = Jtrim(data);
					if(data == "ok")
					{
						$.blockUI({ message: "<p>删除成功!</p>" }); 
						location.reload(true);
					}
					else
					{
						$.blockUI({ message: "<p>"+data+"</p>" }); 
					}					
				}		
			);
		}
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

function editcontact(id)
{
	if(id =='')
	{
		$.blockUI({ message: "<p>参数错误，请刷新重试</p>" }); 
	}
	else
	{
		$.post("do_use.php",
			{m:"edit", ContactID:id},
			function(data){
				var rs = eval('(' + data + ')');
				if(data !== null && rs.ID !== null)
	    		{
					$('#ContactName').val(rs.ContactName);
					$('#ContactJob').val(rs.ContactJob);
					$('#ContactPhone').val(rs.ContactPhone);
					$('#ContactMobile').val(rs.ContactMobile);
					$('#ContactQQ').val(rs.ContactQQ);
					$('#ContactEmail').val(rs.ContactEmail);
					$('#savecontact').attr('data',rs.ID);
					$("#savecontact").attr('cdata',rs.CompanyID);
					window.location.href='#top';
	    		}
				else
				{
					$.blockUI({ message: "<p>参数错误，请刷新重试</p>" }); 
				}
			}		
		);
	}
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000); 
}

function resets()
{
	$("input:text").val('');
	$("textarea").val('');
	$("#RecordDate").val($("#RecordDate").attr('tdata'));
	$("input:radio").removeAttr('checked');
	$("#CompanyID").val("").trigger("change");
	$("#Job").val("").trigger("change");
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
}


/**
 * 回访
 */
function addvisit(vid)
{
	if($("#RecordDate").val() == '')
	{
		$.blockUI({ message: "<p>请输入回访时间！</p>" }); 
	}
	else if($("#ContactName").val() == '')
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" }); 
	}
	else if($("#ContactPhone").val() == '')
	{
		$.blockUI({ message: "<p>请输入联系人电话！</p>" }); 
	}
	else if($("#VisitGeneral").val() == '')
	{
		$.blockUI({ message: "<p>请输入回访简情！</p>" }); 
	}
	else if($('input[name="ContactT"]:checked').val() == null)
	{
		$.blockUI({ message: "<p>请选择回访方式！</p>" }); 
	}
	else if($('input[name="UseFlag"]:checked').val() == null)
	{
		$.blockUI({ message: "<p>请选择回访状态！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val() ? $('#CompanyID').val() : $('#addcontact').attr('cdata');
		$.post("do_use.php",
			{m:"save_visit",ID:vid, CompanyID:companyid,RecordDate:$('#RecordDate').val(),ContactJob:$('#ContactJob').val(),ContactName:$('#ContactName').val(),ContactPhone:$('#ContactPhone').val(),ContactQQ:$('#ContactQQ').val(),ContactEmail:$('#ContactEmail').val(),VisitGeneral:$('#VisitGeneral').val(),VisitContent:$('#VisitContent').val(),ContactType:$('input[name="ContactT"]:checked').val(),UseFlag:$('input[name="UseFlag"]:checked').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
					if(vid !== '')
					{
						history.go(-1);
					}
					else
					{
						$('#resetcompanyid').click();
					}
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

/**
 * 删除订单相关数据
 * 
 **/
function delOrderInfo(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else if($('#OrderType').val() == '')
	{
		$.blockUI({ message: "<p>请选择要删除的数据类型！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		$.post("do_delete.php",
			{m:"del_orderinfo",cid:companyid,OrderType:$('#OrderType').val(),SDate:$('#SDate').val(),EDate:$('#EDate').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

/**
 * 清空库存
 **/
function delStorage(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		$.post("do_delete.php",
			{m:"del_storage",cid:companyid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}

/**
 * 删除商品
 **/
function delProducts(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else if($('input[name="Products"]:checked').val() == null)
	{
		$.blockUI({ message: "<p>请选择要删除的商品！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		var producttype = $('input[name="Products"]:checked').val();
		
		$.post("do_delete.php",
			{m:"del_product",cid:companyid,ptype:producttype},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}

/**
 * 删除商品分类
 **/
function delProductCategory(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		
		$.post("do_delete.php",
			{m:"del_productsite",cid:companyid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}

/**
 * 删除经销商
 **/
function delClient(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else if($('input[name="Client"]:checked').val() == null)
	{
		$.blockUI({ message: "<p>请选择要删除的经销商！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		var client = $('input[name="Client"]:checked').val();
		
		$.post("do_delete.php",
			{m:"del_Client",cid:companyid,ctype:client},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI,3000);
}

/**
 * 删除经销商地区
 **/
function delClientArea(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		
		$.post("do_delete.php",
			{m:"del_clientarea",cid:companyid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}

/**
 * 删除信息
 **/
function delInformation(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else if($('input[name="Info"]:checked').val() == null)
	{
		$.blockUI({ message: "<p>请选择要删除的信息！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		var info = $('input[name="Info"]:checked').val()
		
		$.post("do_delete.php",
			{m:"del_Info",cid:companyid,infotype:info},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}

/**
 * 删除商品分类
 **/
function delInfoCategory(){
	if($('#CompanyID').val() == '')
	{
		$.blockUI({ message: "<p>请选择客户！</p>" }); 
	}
	else
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		var companyid = $('#CompanyID').val();
		
		$.post("do_delete.php",
			{m:"del_InfoCategory",cid:companyid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({message: "<p>操作成功!</p>" }); 
					window.setTimeout($.unblockUI, 1000); 
				}
				else
				{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);
}
