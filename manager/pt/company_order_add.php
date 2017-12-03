<?php
include_once ("header.php");
$menu_flag = "company_order";

$company_list = $db->get_results("SELECT c.CompanyID,c.CompanyPrefix,c.CompanyName,c.CompanyContact,c.CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company c inner join ".DATABASEU.DATATABLE."_order_cs cs ON c.CompanyID=cs.CS_Company where cs.CS_Flag='T' ORDER BY c.CompanyID DESC");
$product_config = get_buy_conf($db,'product');
$sms_config = get_buy_conf($db,'sms');
$erp_config = get_buy_conf($db,'erp');
$weixin_config = get_buy_conf($db,'weixin');

function get_buy_conf($db , $type = 'product') {
    $result = array();
    $list = $db->get_results("SELECT * FROM ".DATABASEU . DATATABLE."_buy_conf WHERE type='{$type}'");
    foreach($list ? $list : array() as $key => $val) {
        $val['data'] = json_decode($val['data'],true);
        $result[$key] = $val;
    }
    unset($list);
    return $result;
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

    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $("#CS_BeginDate").datepicker();
            $("#CS_EndDate").datepicker();
            $("#CS_UpDate").datepicker();
            $("#buy_time").datepicker();
        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
<div class="lineblank"></div>
<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="do_company.php">

<div id="searchline">
    <div class="leftdiv width300">
        <div class="locationl"><strong>当前位置：</strong><a href="company_order.php">订单管理</a> &#8250;&#8250; <a href="company_order_add.php">新增订单</a></div>
    </div>

    <div class="rightdiv sublink" style="padding-right:20px; ">
        <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_company_order();" />
        <input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
        <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
    </div>

</div>


<div class="line2"></div>
<div class="bline" >

    <fieldset  class="fieldsetstyle">
        <legend>订单信息</legend>
        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
            <tr>
                <td width="16%" bgcolor="#F0F0F0"><div align="right">订单用户：</div></td>
                <td width="55%"><label>
                        <select name="company_id" class="select2">
                            <?php foreach($company_list as $company) { ?>
                                <option value="<?php echo $company['CompanyID']; ?>"><?php echo $company['CompanyPrefix'] . '-' . $company['CompanyName'] . ' - '.$company['CompanyContact'].' - ' . $company['CompanyMobile']; ?></option>
                            <?php } ?>
                        </select>
                        <span class="red">*</span></label></td>
                <td width="29%"></td>
            </tr>
            <tr>
                <td width="16%" bgcolor="#F0F0F0"><div align="right">订单类型：</div></td>
                <td width="55%" bgcolor="#FFFFFF"><label>
                        <select class="select2" name="buy_type">
                            <option value="">请选择</option>
                            <option value="product">系统续费</option>
                            <option value="sms">购买短信</option>
                            <option value="erp">ERP标准接口</option>
                            <option value="weixin">微信独立部署</option>
                        </select>
                        <span class="red">*</span></label></td>
                <td width="29%" bgcolor="#FFFFFF"></td>
            </tr>
            <tr>
                <td width="16%" bgcolor="#F0F0F0"><div align="right">购买项目：</div></td>
                <td width="55%" bgcolor="#FFFFFF"><label>
                        <select class="select2" name="buy_info" style="width:555px;"> <!--370-->

                        </select>
                        <input type="text" name="total" id="total" style="width:170px;display:none;" placeholder="金额" />
                        <span class="red">*</span></label></td>
                <td width="29%" bgcolor="#FFFFFF"></td>
            </tr>
            <tr>
                <td width="16%" bgcolor="#F0F0F0"><div align="right">购买时间：</div></td>
                <td width="55%" bgcolor="#FFFFFF"><label>
                        <input name="time" type="text" id="buy_time"/>
                        <span class="red">*</span></label></td>
                <td width="29%" bgcolor="#FFFFFF"></td>
            </tr>
            <tr>
                <td bgcolor="#F0F0F0"><div align="right">订单备注：</div></td>
                <td bgcolor="#FFFFFF"><textarea name="order_remark" rows="2"></textarea>
                    &nbsp;</td>
                <td bgcolor="#FFFFFF">&nbsp;</td>
            </tr>
        </table>
    </fieldset>

    <br style="clear:both;" />
    <fieldset class="fieldsetstyle">
        <legend>支付信息</legend>
        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
            <tr>
                <td bgcolor="#F0F0F0" width="16%"><div align="right">转入账号：</div></td>
                <td bgcolor="#FFFFFF" width="55%">
                    <select name="trade_no" class="select2">
                        <option value="公司支付宝">公司支付宝</option>
                        <option value="对私-建设银行">对私-建设银行</option>
                        <option value="对公-招商银行">对公-招商银行</option>
                    </select>
                </td>
                <td bgcolor="#FFFFFF" width="29%"></td>
            </tr>
            <tr>
                <td bgcolor="#F0F0F0"><div align="right">支付备注：</div></td>
                <td bgcolor="#FFFFFF">
                    <textarea name="stream_remark" rows="3" ></textarea>&nbsp;
                </td>
                <td bgcolor="#FFFFFF">&nbsp;</td>
            </tr>
        </table>
    </fieldset>

    <div class="rightdiv sublink" style="padding-right:20px;">
        <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_company_order();" />
        <input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
        <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
    </div>

</div>
    <input name="m" type="hidden" value="save_company_order"/>
<INPUT TYPE="hidden" name="referer" value ="" >
</form>
<br style="clear:both;" />
</div>

<?php include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
    function do_save_company_order () {
        var fm = $("#MainForm");
        //FIXME 验证输入
        var buy_type = $("select[name='buy_type']").val();
        var err_msg = "";
        if(!buy_type) {
            err_msg = "请选择订单类型!";
        }
        if(err_msg) {
            $.blockUI({
                message : '<p>'+err_msg+'</p>'
            });
            return false;
        }
        $.post(fm.attr('action'),fm.serialize(),function(data){
            data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>订单生成成功!</p>'
                });
                setTimeout(function(){
                    location.href = "company_order.php";
                },710);
            } else {
                $.blockUI({
                    message : '<p>'+data+'</p>'
                });
            }
        },'text');
    }
    $(function(){
        $("body").on('click','.blockOverlay',function(){
            $.unblockUI();
        });


        $("body").on('change','select[name="buy_info"]',function(){
            var _this = $(this);
            var val = $(this).val();
            if(val != -1) {
                $("#total").hide();
                _this.css({
                    width:555
                }).select2();
            } else {
                _this.css({
                    width:370
                }).select2();
                $("#total").show();
            }
        });


       $("select[name='buy_type']").change(function(){
           var pinfo,optStr='';
           var type = $(this).val();
           if(!type) {
               return false;
           }
           switch(type) {
               case 'product':
                   pinfo = <?php echo json_encode($product_config[0]); ?>;
                   if(!pinfo) {
                       $.blockUI({
                           message : '<p>暂无系统续费相关信息</p>'
                       });
                   } else {
                       optStr = '';
                       $(pinfo.data).each(function(){
                            optStr += '<option value="'+this.buy_time+'">'+this.total + '购买' + this.buy_time + '年' + pinfo.title +'</option>';
                       });
                   }
                   break;
               case 'sms':
                   pinfo = <?php echo json_encode($sms_config); ?>;
                   if(!pinfo) {
                       $.blockUI({
                           message : '<p>暂无短信相关信息!</p>'
                       });
                   } else {
                       optStr = '';
                        $(pinfo).each(function(){
                            optStr += '<option value="'+this.id+'">'+this['data'][0]['total']+'购买'+this.title+'</option>';
                        });
                   }
                   break;
               case 'erp':
                   pinfo = <?php echo json_encode($erp_config[0]); ?>;
                   if(!pinfo) {
                       $.blockUI({
                           message : '<p>暂无ERP相关信息!</p>'
                       });
                   } else {
                       var optStr = '';
                       $(pinfo.data).each(function(){
                           optStr += '<option value="'+this.buy_time+'">'+this.total+'购买'+this.buy_time+'年'+pinfo.title+'</option>';
                       });
                   }
                   break;
               case 'weixin':
                   pinfo = <?php echo json_encode($weixin_config[0]); ?>;
                   if(!pinfo) {
                       $.blockUI({
                           message : '<p>暂无微信部署信息!</p>'
                       });
                   } else {
                       $(pinfo.data).each(function(){
                            optStr += '<option value="'+this.buy_time+'">'+this.total+'购买' + pinfo.title + '</option>';
                       });
                   }
                   break;
               default:break;
           }
           if(type != 'weixin') {
               optStr += '<option value="-1">其它</option>';
           }

           $("select[name='buy_info']").html(optStr).select2();
           $("select[name='buy_info']").trigger('change');
       });
    });
</script>
</body>
</html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "┠-";
    $selectmsg = "";

    if($var['AreaParent']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['AreaParent'] == $p_id)
        {
            $repeatMsg = str_repeat("-+-", $layer-2);
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
