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
<script type="text/javascript">
function Jtrim(str){ 

	return str.replace(/^\s*|\s*$/g,"");  

} 

function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}
	
function CheckAll(form,nameflag)
{
	var lenghtflag = nameflag.length;
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if(e.name != undefined && e.type=="checkbox")
		{
			var nameend = e.name.substring(0,5);
			tmpa = eval("form."+nameflag+".checked")
			if (e.name != 'chkall' && nameflag == nameend) e.checked = tmpa;
		}
	}
}

function do_save_user()
{
	document.MainForm.referer.value = document.location;
	var regpwd=/^1\d{10}$/g;

	if($('#data_UserName').val()=="" || $('#data_UserPass').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号和密码！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入管理员姓名！</p>" });
	
	}else if($('#data_UserMobile').val()==""){
		$.blockUI({ message: "<p>请输入管理员移动电话！</p>" });
		
	}else if(!regpwd.test($('#data_UserMobile').val())){
		$.blockUI({ message: "<p>请输入合法的移动电话！</p>" });
		
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system_made.php?m=content_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('user_made.php'), 5000);
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserPass")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>此登陆帐号已使用，请换名再试!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == 'okmobile'){
					$.blockUI({ message: "<p>请输入正确的移动电话号码，以便使用找回密码功能!</p>" });
					$("#data_UserMobile")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == 'oksite'){
					$.blockUI({ message: "<p>请选择商品分类!</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>&nbsp;&nbsp;当前位置：</strong><a href="user.php">帐号管理</a> &#8250;&#8250; <a href="user_add_made.php">新增帐号</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_user();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">            
			
			<legend>登陆信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">          
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆帐号：</div></td>
                  <td width="55%"><label>
                    <input name="data_UserName" type="text" id="data_UserName" value="<? echo $_SESSION['uc']['CompanyPrefix'];?>-"  maxlength="18" />
                    <span class="red">*</span></label></td>
                  <td width="29%">可以是数字、字母、下划线(3-18位)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登陆密码：</div></td>
                  <td><input name="data_UserPass" type="text" id="data_UserPass" value=""  maxlength="18" />
                  <span class="red">*</span></td>
                  <td>可以是数字、字母、下划线(3-18位)</td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
             
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">姓名：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_UserTrueName" id="data_UserTrueName" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_UserMobile" id="data_UserMobile" />
                    <span class="red">*</span></label></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">部门职位：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_UserPhone" id="data_UserPhone" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">销售商品分类：</div></td>
                  <td bgcolor="#FFFFFF">
				   <div style="" id="show_sort_sel">
					<?php
					$topsort = $db->get_results("SELECT SiteID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and ParentID=0 ORDER BY SiteOrder DESC,SiteID ASC ");
					
					if(!empty($topsort))
					{
						foreach($topsort as $v)
						{
							echo '<div style="width:25%;display: inline-block;" title="'.$v['SiteName'].'"><input style="width:15%" id="UserSite_'.$v['SiteID'].'" name="data_UserSite[]" type="checkbox" value="'.$v['SiteID'].'" />'.$v['SiteName'].'</div>';	
						}
					}					
					?>
					</div>
                    </td>					
                   <td bgcolor="#FFFFFF" valign="top">  
				  <span class="red">*</span>&nbsp;&nbsp;选择您要销售的商品分类<br /></td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_UserRemark" rows="5"  id="data_UserRemark"></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_user();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		 </div>
            
         </div>
         <INPUT TYPE="hidden" name="referer" value ="" >
         </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>