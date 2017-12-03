<?php
include_once ("header.php");
$menu_flag = "system";
$company_id = $_SESSION['uc']['CompanyID'];
$in['ID'] = $company_id;
if(!intval($company_id))
{
    exit('非法操作!');
}else{
    $cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyID=".intval($company_id)." limit 0,1");
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
        <!--
        <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
        -->
        <script type="text/javascript">
            $(function() {
                $("#CS_BeginDate").datepicker();
                $("#CS_EndDate").datepicker();
                $("#CS_UpDate").datepicker();
            });
        </script>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
        <div class="lineblank"></div>
        <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

            <div id="searchline">
                <div class="leftdiv width300">
                    <div class="locationl"><strong>当前位置：</strong><a href="manager.php">系统设置</a> &#8250;&#8250; <a href="javascript:;">完善资料</a></div>
                </div>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_company();" />
                    <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
                </div>

            </div>


            <div class="line2"></div>
            <div class="bline" >
                <fieldset title="“*”为必填项！" class="fieldsetstyle">


                    <legend>属性资料</legend>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">

                        <tr>
                            <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                            <td width="55%"><label>
                                    <select name="data_CompanyArea" id="data_CompanyArea" class="select2">
                                        <option value="0">⊙ 请选择客户所在地区</option>
                                        <?
                                       $sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY AreaParent ASC, AreaID ASC");
                                        echo ShowTreeMenu($sortarr,0,$cinfo['CompanyArea'],1);
                                        ?>
                                    </select>
                                    <span class="red">*</span></label></td>
                            <td width="29%"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                            <td>
                                <select name="data_CompanyIndustry" id="data_CompanyIndustry" class="select2">
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
                                <span class="red">*</span></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset>

                <br style="clear:both;" />
                <fieldset  class="fieldsetstyle">
                    <legend>基本资料</legend>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                            <td bgcolor="#FFFFFF">
                                <? echo $cinfo['CompanyPrefix'];?>
                            <td bgcolor="#FFFFFF">药店帐号前缀</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                            <td bgcolor="#FFFFFF">
                                <? echo $cinfo['CompanyMobile'];?>
                            </td>
                            <td bgcolor="#FFFFFF">用于短信通知，等操作(13************)</td>
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
                            <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyCity" id="data_CompanyCity" value="<? echo $cinfo['CompanyCity'];?>"  /></td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyContact" id="data_CompanyContact" value="<? echo $cinfo['CompanyContact'];?>" />
                                <span class="red">*</span></td>
                            <td bgcolor="#FFFFFF"></td>
                        </tr>

                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyPhone" id="data_CompanyPhone" value="<? echo $cinfo['CompanyPhone'];?>" />
                                <span class="red">*</span></td>
                            <td bgcolor="#FFFFFF"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyFax" id="data_CompanyFax" value="<? echo $cinfo['CompanyFax'];?>" /></td>
                            <td bgcolor="#FFFFFF">&nbsp;        </td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyAddress" id="data_CompanyAddress" value="<? echo $cinfo['CompanyAddress'];?>" /></td>
                            <td bgcolor="#FFFFFF"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyEmail" id="data_CompanyEmail" value="<? echo $cinfo['CompanyEmail'];?>" />&nbsp;</td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyWeb" id="data_CompanyWeb" value="<? echo $cinfo['CompanyWeb'];?>"  /></td>
                            <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                            <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyUrl" id="data_CompanyUrl" value="<? echo $cinfo['CompanyUrl'];?>"  /></td>
                            <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                        </tr>

                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                            <td bgcolor="#FFFFFF"><textarea name="data_CompanyRemark" rows="5"  id="data_CompanyRemark"><? echo $cinfo['CompanyRemark'];?></textarea>
                                &nbsp;</td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                    </table>
                </fieldset>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_company();" />
                    <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
                </div>

            </div>
            <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
        <br style="clear:both;" />
    </div>



    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <script type="text/javascript">
        function do_edit_company()
        {
            document.MainForm.referer.value = document.location;

            if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
            {
                $.blockUI({ message: "<p>请选择所属地区！</p>" });

            }
            else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
            {
                $.blockUI({ message: "<p>请选择所属行业！</p>" });

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

            }else{

                var backlink = 'system.php';

                $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
                $.post("do_system.php?m=content_edit_company_save",$("#MainForm").serialize(),
                    function(data){
                        data = $.trim(data);
                        if(data == "ok"){
                            $.blockUI({ message: "<p>保存成功!</p>" });
                            window.location.href=backlink;
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

    <link rel="stylesheet" href="../scripts/select2/select2.min.css" type="text/css" />
    <script src="../scripts/select2/select2.min.js" type="text/javascript"></script>
    <script src="../scripts/select2/zh-CN.js" type="text/javascript"></script>
    <script>
        $(function(){
            if($(".select2").length >0){
                $(".select2").select2();
            }
        });
    </script>
    </body>
    </html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "";
    $selectmsg = "";
    $var = array();

    if($var['AreaParent']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['AreaParent'] == $p_id)
        {
            $repeatMsg = str_repeat("--", $layer-1);
            if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
            $qianzui =$frontTitleMsg.$repeatMsg;

            $frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >|". $qianzui.$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}
?>