<?php
$menu_flag = "manager";
include_once ("header.php");
if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$uinfo  = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".intval($in['ID'])." limit 0,1");
	$data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID=".intval($in['ID'])." LIMIT 1");
	$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company=".intval($in['ID'])." LIMIT 1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script type="text/javascript">var resourceurl = '<?php echo RESOURCE_URL;?>'</script>
</head>

<body>
<?php include_once ("top.php");?>

<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="leftdiv">
            <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="company_verify.php">客户审核</a> &#8250;&#8250; <a href="company_upload.php">修改资料</a></div>
        </div>
    </div>


    <div class="line2"></div>
    <div class="bline" >

        <div >

            <div id="logoinfo" class="line">
                <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >
                    <input name="m" type="hidden" value="do_company_upload"/>
                    <input type="hidden" name="set_filename" id="set_filename" value="" />
                    <input type="hidden" name="ID" id="ID" value="<? echo $in['ID']; ?>" />
                    <fieldset class="fieldsetstyle">
                        <legend>实名认证</legend>
                        <div >
                            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_BusinessName" value="<?php echo $data_info['BusinessName']; ?>" id="data_BusinessName" style="width:75%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_BusinessCard" value="<?php echo $data_info['BusinessCard']; ?>" id="data_BusinessCard" style="width:75%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">上传营业执照图：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_BusinessCardImg" value="<? echo $data_info['BusinessCardImg'];?>" id="data_BusinessCardImg" style="width:75%;" />&nbsp;
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_BusinessCardImg');" value="上传图片" title="上 传" style="width:85px; height:26px; font-size:12px;"> </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_BusinessCardImg_text" style="width:500px; height:225px; overflow:hidden;"><? if(!empty($data_info['BusinessCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" border="0" style="cursor:pointer;" onclick="window.open(this.src);" />';?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">法人身份证号码：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_IDCard" value="<?php echo $data_info['IDCard']; ?>" id="data_IDCard" style="width:75%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">上传身份证照片：</div></td>
                                    <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                        <input type="text" name="data_IDCardImg" value="<? echo $data_info['IDCardImg'];?>" id="data_IDCardImg" style="width:75%;" />&nbsp;
                                        <input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('data_IDCardImg');" value="上传图片" title="上 传" style="width:85px; height:26px; font-size:12px;"> </td>
                                </tr>
                                <tr>
                                    <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                    <td colspan="2" bgcolor="#FFFFFF">
                                        <div id="data_IDCardImg_text" style="width:500px; height:225px; overflow:hidden;">
                                            <? if(!empty($data_info['IDCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" border="0" style="cursor:pointer;" onclick="window.open(this.src);" />';?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">关联销售员：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <select id="sale" name="sale"  style="width:180px;" class="select2">
                                        <option value="">⊙ 选择销售员</option>
                        				<?php 
                        					$n = 0;		
                        					$sortarr = $db->get_results("SELECT ID,SaleName,SaleDepartment,SalePhone FROM ".DATABASEU.DATATABLE."_order_sale  ORDER BY ID DESC ");
                        					foreach($sortarr as $var)
                        					{
                        						if($cs_info['CS_SaleUID'] == $var['ID']) $smsg = 'selected="selected"'; else $smsg ="";
                        
                        						echo '<option value="'.$var['ID'].'" '.$smsg.' title="'.$var['SaleName'].'"  >'.$var['SaleName'].'-'.$sale_depart[$var['SaleDepartment']].'-'.$var['SalePhone'].'</option>';
                        					}
                        				?>
                                    </select>
                                </td>
                            </tr>
                            </table>
                            <div class="line" align="center"><input type="button" name="editbuttoninfo" id="editbuttoninfo" value=" 保存资料 " class="button_2" onclick="do_company_upload();" />&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>(注：图片上传成功后请点保存资料)</font></div>
                        </div>
                    </fieldset>
                </form>
                <br style="clear:both;" />
            </div>

        </div>
    </div>

    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>
<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">上传图片</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent"></div>
</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">

	function closewindowui()
	{
		$.unblockUI();
	}
	
	function setinputimg(fpn)
	{	
		var filevalue = $('#set_filename').val();
		fpn = fpn.replace('thumb_','img_');
		if(fpn!='' && fpn!=null)
		{
			$("#"+filevalue).val(fpn);
			$("#"+filevalue+"_text").html('<a href="'+resourceurl+fpn+'" target="_blank"><img src="'+resourceurl+fpn+'" border="0" /></a>');
		}	
		$.unblockUI();
	}

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


        //验证输入
        var bs_card = $("#data_BusinessCard").val();
        var bs_name = $("#data_BusinessName").val();
        var bs_card_img = $("#data_BusinessCardImg").val();
        var id_card = $("#data_IDCard").val();
        var id_card_img = $("#data_IDCardImg").val();
        var id_sale = $('#sale').val();

        var errMsg = "";
        if(!bs_name) {
            errMsg = "请输入公司名称";
        } else if(!bs_card) {
            errMsg = "请输入营业执照号";
        } else if(!bs_card_img) {
            errMsg = "请上传营业执照";
        } else if(!id_card) {
            errMsg = "请输入法人身份证号码";
        } else if(!id_card_img) {
            errMsg = "请上传身份证照片";
        } else if(!id_sale){
        	errMsg = "请选择需要关联的销售员";
        }
        if(errMsg) {
            $.blockUI({
                message : '<p>' + errMsg + '</p>'
            });
            return false;
        }

        $.post("do_company.php",$("#MainForm").serialize(),function(data){

            if(data == 'ok') {
                $.blockUI({
                    message : '<p>资料上传成功!</p>'
                });
                window.location.href="company_verify.php";
            } else {
                $.blockUI({
                    message : '<p>数据保存失败,请重试!</p>'
                });
            }
            
        },'text');
        
        $('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
		window.setTimeout($.unblockUI, 2000);
    }
</script>
</body>
</html>