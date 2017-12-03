<?php
$menu_flag = "manager";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="../scripts/select2/select2.min.css"/>
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="../scripts/select2/select2.min.js" type="text/javascript"></script>
    <script src="../scripts/select2/zh-CN.js" type="text/javascript"></script>
    <title>时间线</title>
    <style type="text/css">
        body{ background-color:#f3f3f3; margin:0; padding:0;}
        a ,body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, fieldset, input,textarea, p, a, blockquote, th,table,td,tr,h1{ font-size:14px; font-family:"微软雅黑",Arial, Helvetica, sans-serif; color:#343434;}
        .content1{ width:555px; margin:0 auto; margin-top:25px; position:relative; height:auto;}
        .insert_record{ width:400px; height:120px; border:1px solid #cbcbcb; border-radius:5px; font-size:12px; float:left;}
        .insert_btn{ width:104px; height:37px; border:1px solid #0e86dc; border-radius:5px; background-color:#51b2f7; color:#fff; font-family:"微软雅黑"; margin-left:5px; cursor: pointer; margin-top:10px;float:left;}
        .insert_btn2{ width:104px; height:37px; border:1px solid #666; border-radius:5px; background-color:#999; color:#fff; font-family:"微软雅黑"; margin-left:25px; cursor: pointer; margin-top:10px;float:left;}
        .content1 dl dt{ width:100%; float:left}
        .clock{ width:36px; height:36px; background-image:url(img/clock_img.png); background-position:0 0; background-repeat:no-repeat; overflow:hidden; float:left; margin-left:5px;}
        .years{ float:left; font-size:14px; font-weight:bold; color:#3ec1ec; line-height:36px; height:36px; width:59px;overflow:hidden;}
        .mounth{ float:left; font-size:14px; font-weight:bold; color:#3ec1ec; line-height:36px; margin-left:30px;_margin-left:15px;height:36px; width:35px; overflow:hidden; display:block}
        .dian{ width:14px; height:14px; background-image:url(img/clock_img.png); background-position:-12px -38px; background-repeat:no-repeat; overflow:hidden; float:left; margin-top:11px;margin-left:10px;}
        .mounth_info{ width:100%;}
        .mounth_left{ width:100px; float:left;overflow:hidden}
        .mounth_right{ width:449px; float:left;border-left:1px solid #51b2f7; margin-left:-19px; padding-left:19px;overflow:hidden}
        .mounth_right ul{ list-style:none; margin-left:-30px;*margin-left:10px; margin-bottom:55px;}
        .mounth_right ul li{ width:420px; border:1px solid #cbcbcb; border-radius:5px; background-color:#fff; min-height:75px; height:auto; margin-bottom:15px;}
        .mounth_right ul li h1{ font-size:12px; font-weight:bold; color:#3ec1ec; padding-left:10px; line-height:20px; height:20px; display:block; margin-top:3px; margin-bottom:3px;}
        .mounth_right ul li p{ line-height:20px; margin-left:10px; margin-top:0px; height:auto; display:block; width:449px; overflow:hidden}
        .chaozuo{ color:#adadad; margin-left:5px;}
        .times{ color:#adadad; margin-left:5px;}
        input{width: 80%; padding:3px; height:28px;}
        select{width: 100px; padding:3px;  height:28px;}
        .nav { list-style-type:none;}
        .nav li{ display:inline-block;width:100px;}
        .nav li.active { background:#5e87b0;padding:8px 0;color:#fff;}
        .nav li.active a{ color:#fff;}
        .nav a{ text-decoration:none; display:block;text-align:center;}

    </style>
    <script type="text/javascript">

        $(function(){

            $(".select2").select2();
            $(".nav li").click(function(){
                var _this = $(this);
                var idx = _this.index();
                _this.addClass("active").siblings("li").removeClass("active");
                $(".content>.content1:eq("+idx+")").show().siblings(".content1").hide();
            });
        });
        //代理商
        function do_save_pay(type)
        {
            type = type || 'allinpay';
            var fm = $(".content1:eq("+$(".nav li.active").index()+") form");
            var err = "";
            var reg = new RegExp("^[0-9]{20}$");
            if(type == 'allinpay') {
                if($('#MerchantNO',fm).val()=="") {
                    err = '请输入商户号';
                } else if ($('#SignMsgKey',fm).val()=="") {
                    err = '请输入MD5KEY';
                } else if ($('#SignMsg',fm).val()==""){
                    err = '请输入证书';
                }
            } else {
                if($("#SignNO",fm).val() == "") {
                    err = "请输入商户ID";
                } else if(!reg.test($("#SignNO",fm).val())){
                    err = "商户ID必须为20位纯数字";
                } else if($("#SignAccount",fm).val() == "") {
                    err = "请输入商户账号";
                } else if($("#MerchantName",fm).val() == "") {
                    err = "请输入商户名称";
                }
            }

            if(err) {
                $.blockUI({
                    message : '<p>'+err+'</p>'
                });
                $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                return false;
            }

            $.post("do_manager.php?m=save_getway",fm.serialize(),function(data){
                if(data == "ok"){
                    alert('保存成功！');
                    $.blockUI({ message: "<p>保存成功!</p>" });
                    //window.setTimeout(window.location.reload(), 3000);
                    $('.blockOverlay').attr('title','点击返回!').click(window.location.reload());
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                }
            },'text');
            $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            window.setTimeout($.unblockUI, 5000);
        }

        //绑定客户选择
        $(function(){
            var yijifu = $('#yijifu-CompanyID');
        	if(yijifu.length){
        		yijifu.bind('change', function(){
					$.post('do_manager.php?m=checkpaytype&cpid=' + (this.value), function(msg){
						parseInt(msg) ? $('#open_pos option[value="T"]').attr('selected', true) : $('#open_pos option[value="T"]').attr('selected', false);


					});
            	});
        	}


         });
        
    </script>
</head>

<body>

<?php
    $company_list = $db->get_results("SELECT CompanyID,CompanyName,CompanyPrefix,CompanyMobile,CompanyContact,BusinessLicense FROM ".DATABASEU.DATATABLE."_order_company ORDER BY CompanyID DESC");
?>
<!--添加记录-->
<div>

    <ul class="nav">
        <li class="active"><a href="javascript:;" title="易极付">易极付</a></li>
        <li><a href="javascript:;" title="通联支付">通联支付</a></li>

    </ul>
    <div class="content">
        <div class="content1">

            <form id="formrecord" name="formrecord" method="post" action="do_manager.php?m=save_getway">
                <input name="AccountType" type="hidden" value="personal" />
                <input name="GetWay" value="yijifu" type="hidden"/>

                <input name="GetWayID" id="GetWayID" type="hidden" value="<?php echo $data['GetWayID'];?>" />
                <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="tc" >
                    <?php if(!$in['ID']) { ?>
                        <tr>
                            <td width="30%">客户：</td>
                            <td>
                                <select name="CompanyID" id="yijifu-CompanyID" class="select2" style="width:80%">
                                    <?php
                                    foreach($company_list as $com) {?>
                                        <option value="<?php echo $com['CompanyID']; ?>"><?php echo $com['BusinessLicense'] . ' - ' . $com['CompanyContact'] . ' - ' . $com['CompanyMobile']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td width="30%">商户ID：</td>
                        <td><input name="SignNO" id="SignNO" type="text" value="<?php echo $data['SignNO'];?>" /></td>
                    </tr>
                    <tr>
                        <td width="30%">商户账号：</td>
                        <td><input name="SignAccount" id="SignAccount" type="text" value="<?php echo $data['SignAccount'];?>" /></td>
                    </tr>
                    <tr>
                        <td width="30%">商户名称：</td>
                        <td><input name="MerchantName" id="MerchantName" type="text" value="<?php echo $data['MerchantName'];?>" /></td>
                    </tr>
                    <tr>
                        <td width="30%">是否默认</td>
                        <td style="vertical-align:middle;">
                            <label>
                                <input type="checkbox" name="IsDefault" value="T" checked="checked" style="width:22px;height:22px;vertical-align: middle;" />&nbsp;&nbsp;默认
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">POS支付：</td>
                        <td>
                            <select name="open_pos" id="open_pos" class="selectline">
                                <option value="F" >未开通</option>
                                <option value="T" >已开通</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">状 态：</td>
                        <td>
                            <select name="Status" id="Status" class="selectline">
                                <option value="T" >启用</option>
                                <option value="F" >停用</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">类型：</td>
                        <td>
                            <select name="AccountType" id="Status" class="selectline">
                                <option value="personal">个人账号</option>
                                <option value="company">公司账号</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%"></td>
                        <td>
                            <input name="button" type="button"  value="保 存" onclick="do_save_pay('yijifu');" class="insert_btn" />
                            <input name="button" type="button"  value="取 消" onclick="parent.closewindowui()" class="insert_btn2" />
                        </td>
                    </tr>
                </table>


            </form>
        </div>
        <div class="content1" id="allinpay" style="display:none;">
            <form id="formrecord" name="formrecord" method="post" action="do_manager.php?m=save_getway">

                <input name="GetWay" value="allinpay" type="hidden"/>
                <input name="GetWayID" id="GetWayID" type="hidden" value="<?php echo $data['GetWayID'];?>" />
                <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="tc" >
                    <?php if(!$in['ID']) { ?>
                        <tr>
                            <td width="30%">客户：</td>
                            <td>
                                <select name="CompanyID" class="select2" style="width:80%">
                                    <?php
                                    foreach($company_list as $com) {?>
                                        <option value="<?php echo $com['CompanyID']; ?>"><?php echo $com['CompanyName'] . ' - ' . $com['CompanyMobile']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td width="30%">商户号：</td>
                        <td><input name="MerchantNO" id="MerchantNO" type="text" value="" /></td>
                    </tr>
                    <tr>
                        <td width="30%">MD5KEY：</td>
                        <td><input name="SignMsgKey" id="SignMsgKey" type="text" value="" /></td>
                    </tr>
                    <tr>
                        <td width="30%">证 书：</td>
                        <td><textarea name="SignMsg" id="SignMsg" type="text" class="insert_record" row="5" ></textarea></td>
                    </tr>
                    <tr>
                        <td width="30%">B2B：(公对公)</td>
                        <td>
                            <select name="B2B" id="B2B" class="selectline">
                                <option value="T">开通</option>
                                <option value="F">不开通</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">手续费支付方</td>
                        <td>
                            <select name="Fee" id="Fee" class="selectline">
                                <option value="Collection" >收款方</option>
                                <option value="Pay" >付款方</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">状 态：</td>
                        <td>
                            <select name="Status" id="Status" class="selectline">
                                <option value="T" >启用</option>
                                <option value="F" >停用</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%"></td>
                        <td>
                            <input name="button" type="button"  value="保 存" onclick="do_save_pay('allinpay');" class="insert_btn" />
                            <input name="button" type="button"  value="取 消" onclick="parent.closewindowui()" class="insert_btn2" />
                        </td>
                    </tr>
                </table>


            </form>
        </div>

    </div>
</div>

</body>
</html>