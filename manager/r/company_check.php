<?php
include_once ("header.php");
$menu_flag = "manager";
$company_id = $in['ID'];
if(!$company_id) {
    exit('非法操作!');
}
$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyID=".intval($in['ID'])." limit 0,1");



$data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID={$company_id} LIMIT 1");
$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");

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

        <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
    <?php include_once ("inc/son_menu_bar.php");?>

    <div id="bodycontent">
        <div class="lineblank"></div>
        <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

            <div id="searchline">
                <div class="leftdiv width300">
                    <div class="locationl"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="javascript:;">审核资料</a></div>
                </div>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="通过" onclick="do_check_company('T');" />
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="不通过" onclick="do_check_company('F');" />
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
                                    <?php echo $db->get_var("SELECT AreaName FROM ".DATABASEU.DATATABLE."_order_city WHERE AreaID={$cinfo['CompanyArea']} LIMIT 1"); ?>
                                </label></td>
                            <td width="29%"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                            <td>
                                <?php
                                    echo $db->get_var("SELECT IndustryName FROM ".DATABASEU.DATATABLE."_order_industry WHERE IndustryID={$cinfo['CompanyIndustry']} LIMIT 1 ");
                                ?></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset>

                <br style="clear:both;" />
                <fieldset  class="fieldsetstyle">
                    <legend>基本资料</legend>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                        <tr>
                            <td width="16%" bgcolor="#F0F0F0"><div align="right">公司全称：</div></td>
                            <td width="55%" bgcolor="#FFFFFF">
                                <label>
                                    <?php echo $cinfo['CompanyName']; ?></label>
                            </td>
                            <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                            <td bgcolor="#FFFFFF">
                                <?php echo $cinfo['CompanySigned']; ?>
                            </td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                            <td bgcolor="#FFFFFF">
                                <?php echo $cinfo['CompanyPrefix']; ?>
                            </td>
                            <td bgcolor="#FFFFFF">经销商帐号前缀</td>
                        </tr>

                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyCity'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyContact'];?></td>
                            <td bgcolor="#FFFFFF">可以写多个</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyMobile'];?>
                            </td>
                            <td bgcolor="#FFFFFF">用于短信通知，等操作(13************)</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPhone'];?></td>
                            <td bgcolor="#FFFFFF">可以写多个</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyFax'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;        </td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyAddress'];?></td>
                            <td bgcolor="#FFFFFF"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyEmail'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyWeb'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyUrl'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                        </tr>

                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyRemark'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                    </table>
                </fieldset>

                <br style="clear:both;" />
                <fieldset class="fieldsetstyle">
                    <legend>证件信息</legend>
                    <div >
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $data_info['BusinessCard']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                <td colspan="2" bgcolor="#FFFFFF">
                                    <div id="data_BusinessCardImg_text" style="width:500px; height:225px; overflow:hidden;"><? if(!empty($data_info['BusinessCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" border="0" />';?></div>
                                </td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">身份证号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $data_info['IDCard']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                <td colspan="2" bgcolor="#FFFFFF">
                                    <div id="data_IDCardImg_text" style="width:500px; height:225px; overflow:hidden;">
                                        <? if(!empty($data_info['IDCardImg'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" border="0" />';?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
                <br style="clear:both;" />
                <fieldset class="fieldsetstyle">
                    <legend>审核说明</legend>
                    <div >
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">审核备注：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <textarea style="height:90px;" id="remark" placeholder="请输入审核备注"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </fieldset>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="通过" onclick="do_check_company('T');" />
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="不通过" onclick="do_check_company('F');" />
                    <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
                </div>

            </div>
            <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
        <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <script type="text/javascript">
        $(function(){

            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });

        });

        function do_check_company(flag) {
            if(confirm('确认审核' + (flag == 'T' ? '通过' : '不通过'))) {
                $.post("do_manager.php",{
                    m:'company_check',
                    flag:flag,
                    id:"<?php echo $in['ID']; ?>",
                    remark:$("#remark").val()
                },function(data) {
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>操作成功!</p>'
                        });
                        setTimeout(function(){
                            window.location.href = "manager.php";
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>操作失败!</p>'
                        });
                    }
                },'text');
            }
        }
    </script>
    </body>
    </html>
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
            $repeatMsg = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $layer-1);
            if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";

            $frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}
?>