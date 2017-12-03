<?php
include_once ("header.php");
$menu_flag = "system";
$company_id = $_SESSION['uc']['CompanyID'];
$company_flag = intval($_SESSION['uc']['CompanyFlag']);
$in['ID'] = $company_id;
if(!intval($company_id) || $company_flag != 2)
{
    exit('非法操作!');
}else{
    $cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyID=".intval($company_id)." limit 0,1");
}
$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY AreaParent ASC, AreaID ASC");
if($in['ac']=="rcity"){
	echo ShowTreeMenu($sortarr,$in['cid'],0,1);
    exit;
}
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <link rel="stylesheet" href="../scripts/select2/select2.min.css" type="text/css" />
                <script src="../scripts/select2/select2.min.js" type="text/javascript"></script>
                <script src="../scripts/select2/zh-CN.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $("#CS_BeginDate").datepicker();
                $("#CS_EndDate").datepicker();
                $("#CS_UpDate").datepicker();
				if($(".select2").length >0){
                     $(".select2").select2();
                   }
			 if($(".select3").length >0){
                       $(".select3").select2();
                 }
            });
			
			function changecity(a){
			    $.post("company_wxqyinit.php",
     			  {ac:"rcity", cid:a},
    			  function(data){
					  if(data==""){
						   $("#data_CompanyArea").html("<option value='"+$(".select2").select2("val")+"'>该地区为直辖市</option>"); 
						   $("#data_CompanyArea").select2("val","");
					  }else{
					 	　$("#data_CompanyArea").html(data);
						 $("#data_CompanyArea").select2("val","");
					  }
    			  }   
			 	); 
				//$("#data_CompanyArea").each(function(){alert($(this).id)}); 
			}
        </script>
    </head>

    <body>


    <?php include_once ("top.php");?>
    <style type="text/css" >
        body{margin: 0;font-size: 12px;font-family: Microsoft YaHei,SimSun,Arial;}
        .temp-modal{
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            bottom: 0;
            font-size: 14px;
            z-index: 999;
        }
        .temp-modal > div:first-child{
            background-color: #000000;
            opacity: .5;
            filter: alpha(opacity=50);
            height: 100%;
        }
        .temp-modal > div:first-child + div{
            width: 740px;
            height: 88%;
            position: absolute;
            top: 5%;
            left: 22%;
            background: #ffffff;
            overflow: auto;
            border-radius:0px;
            color: rgb(139,139,139);
            -ms-box-shadow: 0px 0px 1em  0 #000000;
            -webkit-box-shadow: 0px 0px 1em  0 #000000;
            -moz-box-shadow: 0px 0px 1em  0 #000000;
            box-shadow: 0px 0px 1em  0 #000000;
        }
        .temp-modal > div:first-child + div > div{
            padding: 1em;
            text-align: center;
        }
        .temp-modal h3.modal-title{
            font-size: 30px;
            font-weight: normal;
            margin-top:40px;
            color: #ff4a00;
        }
        .temp-modal h3.modal-title + p{
            margin: 6px;
            color: #666666;
            font-size: 18px;
        }
        .wlogo{
            background:url(img/writeLogo.png);
            height: 90px;
            width:189px;
            display: block;
            color: #fff;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-radius:15px;
            position: relative;
            margin-left: auto;
            margin-right: auto;
            margin-top: 10px;
        }
        .btnBox{
            position: absolute;
            right:50px;
            bottom:10px;
            margin:auto;
            overflow:hidden;
        }
        .linkbtn{
            
           margin:auto;
        }
        .modal-btn{

            width:189px;
            height:50px;
            border-radius:90px;
            background-color:#ff4a00;
            font-size: 18px;
            font-weight: normal;
            margin-top:10px;
            border:0;
            color:#fff;
            cursor:pointer;
            border-radius:40px;
            border:1px solid rgb(245, 114, 86);
        }
        .modal-btn1{

            width:189px;
            height:50px;
            border-radius:90px;
            background-color:#fff;
            font-size: 18px;
            font-weight: normal;
            margin-top:10px;
            border:0;
            color:#ff4a00;
            cursor:pointer;
            border-radius:40px;
            border:1px solid rgb(245, 114, 86);
        }
        #data_CompanyArea,#data_CompanyIndustry{
            *display:block !important;
            *float:left;
            *margin:0px;
            *background-color: #fff;
            *border:0px;
        }
        .select2{
            *display:none !important;
        }
        select{*height:28px !important;}
        input{
            *border:2px solid !important;
            *border-color: #696969 #E3E3E3 #E3E3E3 #696969 !important;
        }
    </style>

    <div class="temp-modal">
        <div></div>
        <div>
            <div>
                <div id="bodycontent" style="width:700px;">
                    <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
                        <div class="line2"></div>
                        <div class="bline" >
                            <fieldset  class="fieldsetstyle">
                                <legend>基本资料</legend>
                              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">请设置登录账号：</div></td>
                                    <td bgcolor="#FFFFFF">&nbsp;
                                      <input name="data_UserName" type="text" id="data_UserName"  maxlength="18" />
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">可以是数字、字母、下划线(3-18位)</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">密码：</div></td>
                                    <td bgcolor="#FFFFFF">&nbsp;
                                      <input name="data_NewPass" type="password" id="data_NewPass" value=""  maxlength="18" />
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">确认密码：</div></td>
                                    <td bgcolor="#FFFFFF">&nbsp;
                                      <input name="data_ConfirmPass" type="password" id="data_ConfirmPass" value=""  maxlength="18" />
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">个性域名：</div></td>
                                    <td bgcolor="#FFFFFF">http://
                                      <input type="text" style="width:50px;margin-top:-3px;" name="data_CompanyPrefix" id="data_CompanyPrefix" value=""/>
                                      .dhb.hk <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">例:【http://<span style="color:red;">my</span>.dhb.hk】红色部分为自定义</td>
                                  </tr>
                                  <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">平台全称：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF"><label>
                                      <input type="text" name="data_CompanyName" id="data_CompanyName" value="<? echo $cinfo['CompanyName'];?>" />
                                      <input type="hidden" name="ID" id="ID" value="<? echo $cinfo['CompanyID'];?>" />
                                      <span class="red">*</span></label></td>
                                    <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                                    <td bgcolor="#FFFFFF"><input type="text" name="data_CompanySigned" id="data_CompanySigned" value="<? echo $cinfo['CompanySigned'];?>" />
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">&nbsp;用于短信签名(2-6个字)</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                                    <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyContact" id="data_CompanyContact" value="<? echo $cinfo['CompanyContact'];?>" />
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">您贵姓</td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                                    <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyMobile" id="data_CompanyMobile" value="<? echo $cinfo['CompanyMobile'];?>"/>
                                      <span class="red">*</span></td>
                                    <td bgcolor="#FFFFFF">用于短信通知，等操作(13************)</td>
                                  </tr>
                                  <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">所属省：</div></td>
                                    <td width="55%"><label>
                                      <select name="data_CompanyArea2" id="data_CompanyArea2"  class="select2" styel="*display:block;" onchange='changecity(this.options[this.options.selectedIndex].value);'>
                                        <option value="0">⊙ 请选择客户所在省</option>
                                        <?
                                                    echo ShowTreeMenu($sortarr,0,$cinfo['CompanyArea'],1);
                                                    ?>
                                      </select>
                                      <span class="red">&nbsp;*</span></label></td>
                                    <td width="29%"></td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">所属市：</div></td>
                                    <td><select name="data_CompanyArea" id="data_CompanyArea" class="select3" styel="*display:block;">
                                      <option value="0">⊙ 请选择客户所在市</option>
                                    </select></td>
                                    <td></td>
                                  </tr>
                                  <tr>
                                    <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                                    <td><select name="data_CompanyIndustry" id="data_CompanyIndustry" class="select2">
                                      <option value="0">⊙ 请选择客户所属行业</option>
                                      <?
                                                $industryarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
                                                foreach($industryarr as $ivar)
                                                {
                                                    if($cinfo['CompanyIndustry'] == $ivar['IndustryID'])
                                                    {
                                                        echo '<option value="'.$ivar['IndustryID'].'" selected="selected">┠-'.$ivar['IndustryName'].'</option>';
                                                    }else{
                                                        echo '<option value="'.$ivar['IndustryID'].'">┠-'.$ivar['IndustryName'].'</option>';
                                                    }
                                                }
                                                ?>
                                    </select>
                                      <span class="red">&nbsp;*</span></td>
                                    <td></td>
                                  </tr>
                              </table>
                            </fieldset>
                            <div style="margin:0 auto;clear:both;text-align:center"><input class="modal-btn" onclick="do_edit_company();" type="button" value="完成提交"/></div>
                        </div>
                        <INPUT TYPE="hidden" name="referer" value ="" >
                                        <div class="btnBox">
                    </form>
                    <br style="clear:both;" />
                </div>

                <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
                <script type="text/javascript">
                    function do_edit_company()
                    {
                        document.MainForm.referer.value = document.location;
						if($('#data_UserName').val()=="" )
						{
							$.blockUI({ message: "<p>请输入登陆帐号！</p>" });
						}else if($('#data_NewPass').val()==""){
							$.blockUI({ message: "<p>请输入新密码！</p>" });
						}else if($('#data_ConfirmPass').val()==""){
							$.blockUI({ message: "<p>请输确认新密码！</p>" });
						}else if($('#data_NewPass').val()!=$('#data_ConfirmPass').val()){
							$.blockUI({ message: "<p>新密码与确认密码不一致！</p>" });
						}else if($('#data_CompanyPrefix').val()=="")
                        {
                            $.blockUI({ message: "<p>请输入账号前缀！</p>" });
                        }
                        else if($('#data_CompanyName').val()=="")
                        {
                            $.blockUI({ message: "<p>请输入平台名称！</p>" });
                        } else if($("#data_CompanySigned").val() == "") {
                            $.blockUI({ message : '<p>请输入平台简称</p>'});
                        } else if($("#data_CompanySigned").val().length >6) {
                            $.blockUI({ message : '<p>平台简称不能超过六个字!</p>'});
                        }
                        else if($('#data_CompanyContact').val()=="")
                        {
                            $.blockUI({ message: "<p>请输入联系人！</p>" });
                        }
                        else if($('#data_CompanyPhone').val()=="")
                        {
                            $.blockUI({ message: "<p>请输入联系电话！</p>" });

                        }
                        else if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
                        {
                            $.blockUI({ message: "<p>请选择所属地区！</p>" });

                        }
                        else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
                        {
                            $.blockUI({ message: "<p>请选择所属行业！</p>" });
                        }else{

                            var backlink = 'system.php';
                            $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
                            $.post("do_system.php?m=content_edit_wxqymerchant_save",$("#MainForm").serialize(),
                                function(data){
                                    data = $.trim(data);
                                    if(data == "ok"){
                                        $.blockUI({ message: "<p>保存成功!</p>" });
                                        window.location.href=backlink;
                                    }else if(data == "okname"){
										$.blockUI({ message: "<p>请输入正确的用户名(数字、字母和下划线 3-18位)!</p>" });
										$("#data_UserName")[0].focus();
										$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
									}else if(data == "repeat"){
                                        $.blockUI({ message: "<p>客户已存在，请不要重复添加!</p>" });
                                        $("#data_CompanyName")[0].focus();
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
    </body>
</html>


            </div>
        </div>
    </div>




<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "";
    $selectmsg = "";

    if($var['AreaParent']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['AreaParent'] == $p_id)
        {
            $repeatMsg = str_repeat("--", $layer-1);
            if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
			if($s_id==0){
           		$rcity[]=$var['AreaID'];
            }
             $frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg.">|".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";
            //$frontMsg2  = "";
            //$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
            //$frontMsg  .= $frontMsg2;
        }
    }
    if($s_id==0){
	   return str_replace("'".$rcity[0]."'","'".$rcity[0]."' selected='selected'",$frontMsg);
    }else{
     return $frontMsg;
    }
}
?>