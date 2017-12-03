<?php
$menu_flag = "system";
include_once ("header.php");
$company_id = $_SESSION['uinfo']['ucompany'];
$user_made_id = $_SESSION['uinfo']['userid'];
$uinfo  = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$company_id." limit 0,1");

$data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID={$company_id} LIMIT 1");
$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="js/system.js?v=<? echo VERID;?>05" type="text/javascript"></script>
    <script src="../scripts/layer/layer.js" type="text/javascript"></script>
    <script type="text/javascript">var resourceurl = '<?php echo RESOURCE_URL;?>'</script>
    <style type="text/css">
    .inputstyle td,div{
    	font-size:12px;
    }
    #data_BusinessCardImg_text img{
    	height:150px;
    }
    #data_IDCardImg_text img{
    	height:150px;
    }
    #data_IDLicenceImg_text img{
    	height:150px;
    }
    #data_GPImg_text img{
    	height:150px;
    }
    .approve-notice{
		float: none;
    	clear: both;
    	color: #dd1f1f;
    	border: 1px dashed #ff9f36;
    	padding: 0 10px;
	}
    </style>
</head>

<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="leftdiv">
            <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="company_upload.php">公司信息</a></div>
        </div>
    </div>


    <div class="line2"></div>
    <div class="bline" >

        <div id="sortleft">
            <!-- tree -->
            <div class="leftlist">
                <div >
                    <strong>系统设置</strong></div>
                <!-- 系统设置菜单开始 -->
                <?php include_once("inc/system_set_left_bar.php")  ;?>
                <!-- 系统设置菜单结束 -->
                <br style="clear:both;" />
            </div>
            <!-- tree -->
        </div>

        <div id="sortright">

            <div id="logoinfo" class="line">
            <?php if($cs_info['CS_Flag']=='F'){?>
            <p class="approve-notice"><b>因以下原因，您需要再次提交资料</b>：<br /><?php echo nl2br($data_info['Notice']); ?></p>
            <?php }?>
                <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >
                    <input name="m" type="hidden" value="do_company_upload"/>
                    <input type="hidden" name="set_filename" id="set_filename" value="" />
                    <?php if($cs_info['CS_Flag']=='T') { ?>
                        <fieldset class="fieldsetstyle">
                            <legend>实名认证</legend>
                            <div >
                                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                                        <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                            <?php echo $data_info['BusinessName']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：</div></td>
                                        <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                            <?php echo $data_info['BusinessCard']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">营业执照电子图片：</td>
                                        <td colspan="2" bgcolor="#FFFFFF">
                                            <div id="data_BusinessCardImg_text" style="width:500px; height:155px; "><? if(!empty($data_info['BusinessCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" border="0" style="cursor:pointer;" title="点击放大" alt="点击放大" onclick="window.open(this.src)" />';?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">法人身份证号码：</td>
                                        <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                            <?php echo $data_info['IDCard']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">法人身份证电子图片：</td>
                                        <td colspan="2" bgcolor="#FFFFFF">
                                            <div id="data_IDCardImg_text" style="width:500px; height:155px; ">
                                                <? if(!empty($data_info['IDCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">药品经营许可证：</td>
                                        <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                            <div id="data_IDCardImg_text" style="width:500px; height:155px; ">
                                                <? if(!empty($data_info['IDLicenceImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDLicenceImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">GSP认证：</td>
                                        <td colspan="2" bgcolor="#FFFFFF">
                                            <div id="data_IDCardImg_text" style="width:500px; height:155px; ">
                                                <? if(!empty($data_info['GPImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['GPImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </fieldset>
                        
                        <br />
                    <!-- 三方合同 -->
                    <?php } else { ?>
                    <fieldset class="fieldsetstyle">
                        <legend>实名认证</legend>
                        <div >
                            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_BusinessName" value="<?php echo $data_info['BusinessName'] ? $data_info['BusinessName']:$_SESSION['uc']['CompanyName']; ?>" id="data_BusinessName" style="width:75%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：<span style="color: red">*</span><br /><span style="font-size:12px;"class="red">(请签字盖章上传<br />务必清晰)</span></div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_BusinessCard" value="<?php echo $data_info['BusinessCard']; ?>" id="data_BusinessCard" style="width:75%;" />
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_BusinessCardImg');" value="上传电子图片" title="上 传" style="width:85px; height:26px; font-size:12px;" />
                                        
                                        <!-- 隐藏图片路径 -->
                                        <input type="text" name="data_BusinessCardImg" value="<? echo $data_info['BusinessCardImg'];?>" id="data_BusinessCardImg" style="width:75%;visibility:hidden;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_BusinessCardImg_text" style="width:500px; height:155px; overflow:hidden;"><?php if(!empty($data_info['BusinessCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" border="0" style="cursor:pointer;" title="点击放大" alt="点击放大" onclick="window.open(this.src);" />';?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">法人身份证号码：<span style="color: red">*</span><br /><span style="font-size:12px;"class="red">(请签字盖章上传<br />务必清晰)</span></div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_IDCard" value="<?php echo $data_info['IDCard']; ?>" id="data_IDCard" style="width:75%;" />
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_IDCardImg');" value="上传电子图片" title="上 传" style="width:85px; height:26px; font-size:12px;">
                                        
                                        <!-- 隐藏图片路径 -->
                                        <input type="text" name="data_IDCardImg" value="<? echo $data_info['IDCardImg'];?>" id="data_IDCardImg" style="width:75%;visibility:hidden;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_IDCardImg_text" style="width:500px; height:155px; overflow:hidden;">
                                            <? if(!empty($data_info['IDCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">药品经营证书编号：<span style="color: red">*</span><br /><span style="font-size:12px;" class="red">(请签字盖章上传<br />务必清晰)</span></div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_IDLicence" value="<?php echo $data_info['IDLicence']; ?>" id="data_IDLicence" style="width:75%;" />
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_IDLicenceImg');" value="上传电子图片" title="上 传" style="width:85px; height:26px; font-size:12px;">
                                        
                                        <!-- 隐藏图片路径 -->
                                   		<input type="text" name="data_IDLicenceImg" value="<? echo $data_info['IDLicenceImg'];?>" id="data_IDLicenceImg" style="width:75%;visibility:hidden;" />
                                   
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_IDLicenceImg_text" style="width:500px; height:155px; overflow:hidden;">
                                            <? if(!empty($data_info['IDLicenceImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDLicenceImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">GSP证书编号：<span style="color: red">*</span><br /><span style="font-size:12px;" class="red">(请签字盖章上传<br />务必清晰)</span></div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_IDGP" value="<?php echo $data_info['IDGP']; ?>" id="data_IDGP" style="width:75%;" />
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_GPImg');" value="上传电子图片" title="上 传" style="width:85px; height:26px; font-size:12px;">
                                        <input type="text" name="data_GPImg" value="<? echo $data_info['GPImg'];?>" id="data_GPImg" style="width:75%;visibility:hidden;" />&nbsp;
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_GPImg_text" style="width:500px; height:155px; overflow:hidden;">
                                            <? if(!empty($data_info['GPImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['GPImg']).'" border="0" title="点击放大" alt="点击放大" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="line" align="center"><input type="button" name="editbuttoninfo" id="editbuttoninfo" value=" 保存资料 " class="button_2" onclick="do_company_upload();" />&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>(注：图片上传成功后请点保存资料)</font></div>
                        </div>
                    </fieldset>
                    
                    <br />
                    <!-- 三方合同 -->
                    <?php include 'three_sides.php';?>
                    <?php } ?>
                </form>
                <br style="clear:both;" />
            </div>

        </div>
    </div>

    <br style="clear:both;" />
</div>


<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">上传图片</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent"></div>
</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">


    function upload_file_certify(fildname)
    {
        $('#windowContent').html('<iframe src="../plugin/jqUploader/upload_certify.php" width="500" marginwidth="0" height="280" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
        $.blockUI({
            message: $('#windowForm'),
            css:{
                width: '500px',top:'20%'
            }
        });
        $('#set_filename').val(fildname);
        $('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
    }

    $(function(){
        $("body").on('click','.blockOverlay',function(){
            $.unblockUI();
        });
    });

    function do_company_upload() {
        $('#editbuttoninfo').attr("disabled","disabled");
        //验证是否允许提交
        if("<?php echo $data_info && $uinfo['M_Flag']== 'T' ? 'err' : 'ok'; ?>" == "err") {
            layer.open({
            	type : 0,
            	icon:2,
            	btnAlign : 'c',
            	closeBtn: 0,
              	title: '提示信息',
              	content: '数据已过期，请重新加载页面'
            }); 
            
            $('#editbuttoninfo').removeAttr("disabled","disabled");
            return false;
        }

        //验证输入
        var bs_card = $("#data_BusinessCard").val();
        var bs_name = $("#data_BusinessName").val();
        var bs_card_img = $("#data_BusinessCardImg").val();
        var id_card = $("#data_IDCard").val();
        var id_card_img = $("#data_IDCardImg").val();
        var id_licence = $("#data_IDLicence").val();
        var id_licence_img = $("#data_IDLicenceImg").val();
        var id_gp = $("#data_IDGP").val();
        var gp_img = $("#data_GPImg").val();

        var errMsg = "";
        if(!bs_name) {
            errMsg = "请输入公司名称";
        } else if(!bs_card) {
            errMsg = "请输入营业执照号";
        } else if(!bs_card_img) {
            errMsg = "请上传营业执照";
        }
         else if(!id_card) {
            errMsg = "请输入法人身份证号码";
        } else if(!id_card_img) {
            errMsg = "请上传法人身份证照片";
        }else if(!id_licence){
        	errMsg = "请输入许可证编号";
        }
        else if(!id_licence_img) {
            errMsg = "请上传经营许可证";
        }else if(!id_gp){
        	errMsg = "请输入GSP编号";
        } else if(!gp_img) {
            errMsg = "请上传GSP证书";
        }

        
        if(errMsg) {
        	layer.open({
            	type : 0,
            	icon:2,
            	btnAlign : 'c',
            	closeBtn: 0,
              	title: '提示信息',
              	content: errMsg
            }); 
            $('#editbuttoninfo').removeAttr("disabled","disabled");
            return false;
        }

        $.post("do_system.php",$("#MainForm").serialize(),function(data){
            data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>资料上传成功!</p>'
                });
                window.location.reload();
            } else {
                $.blockUI({
                    message : '<p>数据保存失败,请重试!</p>'
                });
                $('#editbuttoninfo').removeAttr("disabled","disabled");
            }
        },'text');
    }
</script>
</body>
</html>